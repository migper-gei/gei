<?php
// ============================================================
// criar_qr_equipamentos.php — GEI
// Gera PDF com etiquetas QR Code para os equipamentos de uma sala.
// Apenas administradores (tipo == 1).
// ============================================================

if (session_status() === PHP_SESSION_NONE) {
    $isHttps = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off');
    session_name('gei_session');
    session_set_cookie_params([
        'lifetime' => 0, 'path' => '/',
        'secure'   => $isHttps, 'httponly' => true, 'samesite' => 'Lax',
    ]);
    session_start();
    if (!isset($_SESSION['_created'])) {
        $_SESSION['_created'] = time();
    } elseif (time() - $_SESSION['_created'] > 1800) {
        session_regenerate_id(true);
        $_SESSION['_created'] = time();
    }
}

include_once('svrurl.php');
include_once('config.php');
include('sessao_timeout.php');

// Só administradores
if (!isset($_SESSION['login_user']) || ($_SESSION['tipo'] ?? 0) != 1) {
    header('Location: ' . SVRURL . 'i');
    exit();
}

// Obter o codigo de acesso da settingsbd a partir do nobd da sessão
$_codigo_sessao = 0;
if (!empty($_SESSION['nobd'])) {
    try {
        // Carregar .env para obter credenciais da BD de settings
        $_env_file = __DIR__ . '/.env';
        if (file_exists($_env_file)) {
            foreach (file($_env_file, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) as $_el) {
                if (strncmp(trim($_el), '#', 1) === 0 || strpos($_el, '=') === false) continue;
                [$_ek, $_ev] = explode('=', $_el, 2);
                if (!isset($_ENV[trim($_ek)])) $_ENV[trim($_ek)] = trim($_ev, " '\"");
            }
        }
        $_s_host = $_ENV['DB_HOST']          ?? getenv('DB_HOST')          ?? 'localhost';
        $_s_user = $_ENV['DB_USER']          ?? getenv('DB_USER')          ?? '';
        $_s_pass = $_ENV['DB_PASS']          ?? getenv('DB_PASS')          ?? '';
        $_s_name = $_ENV['DB_SETTINGS_NAME'] ?? getenv('DB_SETTINGS_NAME') ?? '';

        $db0 = new mysqli($_s_host, $_s_user, $_s_pass, $_s_name);
        $db0->set_charset('utf8mb4');
        $stmt_cod = $db0->prepare("SELECT codigo FROM settingsbd WHERE nomebd = ? LIMIT 1");
        $stmt_cod->bind_param('s', $_SESSION['nobd']);
        $stmt_cod->execute();
        $stmt_cod->bind_result($_codigo_sessao);
        $stmt_cod->fetch();
        $stmt_cod->close();
        $db0->close();
    } catch (Exception $e) {
        error_log('criar_qr codigo lookup error: ' . $e->getMessage());
        $_codigo_sessao = 0;
    }
}

// ── Processar pedido de PDF ───────────────────────────────────────────────────
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['sala_qr'])) {

    $id_sala = (int)$_POST['sala_qr'];
    $id_esc  = (int)base64_decode($_GET['esc'] ?? base64_encode(0));

    // Verificar que a sala pertence à escola
    $stmt_s = $db->prepare("SELECT id, nome FROM salas WHERE id = ? AND id_escola = ? LIMIT 1");
    $stmt_s->bind_param('ii', $id_sala, $id_esc);
    $stmt_s->execute();
    $sala_info = $stmt_s->get_result()->fetch_assoc();
    $stmt_s->close();

    if ($sala_info) {

        // Equipamentos da sala
        $stmt_eq = $db->prepare("
            SELECT id, nomeequi FROM equipamento
            WHERE id_sala = ? ORDER BY nomeequi
        ");
        $stmt_eq->bind_param('i', $id_sala);
        $stmt_eq->execute();
        $equipamentos = $stmt_eq->get_result()->fetch_all(MYSQLI_ASSOC);
        $stmt_eq->close();

        // Obter nome da escola para as etiquetas
        $stmt_esc_nome = $db->prepare("SELECT nome_escola FROM escolas WHERE id = ? LIMIT 1");
        $stmt_esc_nome->bind_param('i', $id_esc);
        $stmt_esc_nome->execute();
        $stmt_esc_nome->bind_result($nome_escola_etiq);
        $stmt_esc_nome->fetch();
        $stmt_esc_nome->close();

        if (!empty($equipamentos)) {
            // ── Gerar PDF com FPDF ────────────────────────────────────────────
            require('fpdf/fpdf.php');

            // phpqrcode — usar método text() que NÃO precisa de GD
            $use_local_qr = file_exists(__DIR__ . '/phpqrcode/qrlib.php');
            if ($use_local_qr) {
                require_once(__DIR__ . '/phpqrcode/qrlib.php');
            }

            while (ob_get_level()) { ob_end_clean(); }

            // Dimensões da etiqueta (mm): 3 colunas × 10 linhas em A4
            $pg_w  = 210; $pg_h  = 297;
            $m_esq = 8;   $m_sup = 10;
            $l_eti = 64;  $a_eti = 28;   // largura e altura de cada etiqueta
            $qr_sz = 20;                 // tamanho do QR em mm

            $pdf = new FPDF('P', 'mm', 'A4');
            $pdf->SetMargins($m_esq, $m_sup, $m_esq);
            $pdf->SetAutoPageBreak(false);
            $pdf->AddPage();
            $pdf->SetFont('Arial', '', 7);

            $col = 0; $lin = 0;

            foreach ($equipamentos as $eq) {
                if ($col === 3) { $col = 0; $lin++; }
                if ($lin === 10) { $pdf->AddPage(); $lin = 0; }

                $x = $m_esq + $col * $l_eti;
                $y = $m_sup + $lin * $a_eti;

                // URL do formulário de avaria
                // Usar o IP real da máquina (não 127.0.0.1) para que o QR
                // funcione em telemóveis na mesma rede local
                // Usar o endereço definido pelo administrador no formulário
                $base_qr    = rtrim($_POST['base_url'] ?? SVRURL, '/') . '/';
                $codigo_bd  = $_codigo_sessao ?: (int)($_POST['codigo_bd'] ?? 0);
                // qr_scan.php — router inteligente:
                //   Admin/Reparador → ficha do equipamento
                //   Utilizador/Funcionário autenticado → formulário interno de avaria
                //   Não autenticado → formulário público de avaria (avaria_qr.php)
                $url_avaria = $base_qr . 'qr_scan.php?eq=' . $eq['id']
                            . '&sala=' . $id_sala
                            . '&esc='  . $id_esc
                            . '&cod='  . $codigo_bd;

                // Bordas da etiqueta
                $pdf->SetDrawColor(200, 210, 230);
                $pdf->SetLineWidth(0.2);
                $pdf->Rect($x, $y, $l_eti, $a_eti);

                $txt_x = $x + $qr_sz + 3;

                // Gerar e desenhar QR Code
                $qr_x = $x + 1;
                $qr_y = $y + ($a_eti - $qr_sz) / 2;

                if ($use_local_qr) {
                    // QRcode::text() devolve array de strings — cada char '1'=escuro '0'=claro
                    $qr_matrix = QRcode::text($url_avaria, false, QR_ECLEVEL_M, 3);
                    $modules   = count($qr_matrix);
                    $mod_sz    = $qr_sz / $modules;
                    $pdf->SetFillColor(0, 0, 0);
                    foreach ($qr_matrix as $r => $row) {
                        for ($c = 0; $c < $modules; $c++) {
                            if (isset($row[$c]) && $row[$c] === '1') {
                                $pdf->Rect(
                                    $qr_x + $c * $mod_sz,
                                    $qr_y + $r * $mod_sz,
                                    $mod_sz, $mod_sz, 'F'
                                );
                            }
                        }
                    }
                    $pdf->SetFillColor(0, 0, 0); // reset
                } else {
                    // Fallback: Google Charts API (requer internet)
                    $qr_src = 'https://chart.googleapis.com/chart?chs=150x150&cht=qr&chl='
                              . urlencode($url_avaria) . '&choe=UTF-8';
                    $pdf->Image($qr_src, $qr_x, $qr_y, $qr_sz, $qr_sz, 'PNG');
                }

                // Texto: escola, nome do equipamento, sala
                $avail_w = $l_eti - $qr_sz - 5;

                $pdf->SetFont('Arial', 'I', 5.5);
                $pdf->SetTextColor(100, 115, 140);
                $pdf->SetXY($txt_x, $y + 3);
                $pdf->Cell($avail_w, 3, utf8_decode($nome_escola_etiq ?? ''), 0, 1, 'L');

                $pdf->SetFont('Arial', 'B', 7);
                $pdf->SetTextColor(24, 40, 72);
                $pdf->SetXY($txt_x, $pdf->GetY() + 1);
                $pdf->MultiCell($avail_w, 4, utf8_decode($eq['nomeequi']), 0, 'L');

                $pdf->SetFont('Arial', '', 6);
                $pdf->SetTextColor(100, 115, 140);
                $pdf->SetXY($txt_x, $pdf->GetY() + 1);
                $pdf->Cell($avail_w, 3.5, utf8_decode($sala_info['nome']), 0, 1, 'L');

                $pdf->SetFont('Arial', 'I', 5.5);
                $pdf->SetTextColor(150, 160, 175);
                $pdf->SetXY($txt_x, $pdf->GetY() + 1);
                $pdf->Cell($avail_w, 3, 'Aponte a camera para reportar avaria', 0, 1, 'L');

                $col++;
            }

            $nome_pdf = 'QR_Avarias_' . preg_replace('/[^A-Za-z0-9_-]/', '_', $sala_info['nome']) . '.pdf';
            $pdf->Output('I', $nome_pdf);
            exit;
        }
    }
}

// ── Página HTML ───────────────────────────────────────────────────────────────
$id_esc = (int)base64_decode($_GET['esc'] ?? base64_encode(0));
$escolas = [];
$res_esc = mysqli_query($db, "SELECT id, nome_escola FROM escolas ORDER BY id");
while ($row = mysqli_fetch_assoc($res_esc)) { $escolas[] = $row; }
if ($id_esc === 0 && !empty($escolas)) { $id_esc = (int)$escolas[0]['id']; }
?>
<!DOCTYPE html>
<html lang="pt">
<head>
    <?php include('head.php'); ?>
</head>
<body class="main-layout">
    <?php include('loader.php'); ?>
    <?php include('header.php'); ?>

    <div class="about">
      <div class="container">
        <div class="row">
          <div class="col-md-12">

            <nav style="margin-bottom:10px;">
              <ol style="display:flex;align-items:center;flex-wrap:wrap;gap:4px;list-style:none;padding:0;margin:0;font-size:.8rem;font-weight:600;color:#7b88a0;">
                
 <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="#4b6cb7" stroke-width="2.3" stroke-linecap="round" stroke-linejoin="round"><rect x="2" y="7" width="20" height="14" rx="2"/><path d="M16 7V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v2"/></svg>
                        
                 <a href="<?php echo SVRURL ?>equip" style="color:#4b6cb7;text-decoration:none;">Equipamentos</a>
                     
                
                <li style="color:#c5cde0;">&#8250;</li>
                <li style="color:#1e2a45;">QR Codes - Gerar etiquetas</li>
              </ol>
            </nav>

            <div style="background:#fff;border:1px solid #e3e8f4;border-radius:12px;padding:28px;max-width:540px;margin:0 auto;box-shadow:0 2px 12px rgba(75,108,183,.10);">

              <div style="display:flex;align-items:center;gap:14px;margin-bottom:24px;">
                <div style="width:46px;height:46px;border-radius:12px;background:linear-gradient(135deg,#4b6cb7,#507feb);display:flex;align-items:center;justify-content:center;flex-shrink:0;box-shadow:0 4px 12px rgba(75,108,183,.35);">
                  <i class="fas fa-qrcode" style="color:#fff;font-size:1.2rem;"></i>
                </div>
                <div>
                  <h1 style="font-size:1.25rem;font-weight:700;margin:0;color:#182848;">Gerar QR Codes de avaria</h1>
                  <p style="margin:0;font-size:.82rem;color:#7b88a0;">Etiquetas PDF para afixar nos equipamentos</p>
                </div>
              </div>

              <form method="POST" action="?esc=<?php echo base64_encode($id_esc); ?>">

                <div style="margin-bottom:16px;">
                  <label style="font-size:.78rem;font-weight:700;color:#7b88a0;text-transform:uppercase;letter-spacing:.4px;display:block;margin-bottom:6px;">
                    <i class="fas fa-school"></i> Instituição
                  </label>
                  <select name="escola_qr" onchange="window.location='?esc='+btoa(this.value)"
                          style="width:100%;border:1.5px solid #e3e8f4;border-radius:8px;padding:9px 12px;font-family:inherit;font-size:.9rem;color:#1e2a45;background:#f7f9fe;">
                    <?php foreach ($escolas as $esc): ?>
                      <option value="<?php echo (int)$esc['id']; ?>"
                        <?php echo ((int)$esc['id'] === $id_esc) ? 'selected' : ''; ?>>
                        <?php echo htmlspecialchars($esc['nome_escola']); ?>
                      </option>
                    <?php endforeach; ?>
                  </select>
                </div>

                <div style="margin-bottom:16px;">
                  <label style="font-size:.78rem;font-weight:700;color:#7b88a0;text-transform:uppercase;letter-spacing:.4px;display:block;margin-bottom:6px;">
                    <i class="fas fa-globe"></i> IP do servidor
                  </label>
                  <input type="text" name="base_url" value="<?php echo htmlspecialchars(SVRURL); ?>"
                         style="width:100%;border:1.5px solid #e3e8f4;border-radius:8px;padding:9px 12px;font-family:monospace;font-size:.85rem;color:#1e2a45;background:#f7f9fe;">
                  <small style="color:#7b88a0;font-size:.72rem;display:block;margin-top:4px;">

                  </small>
                </div>

                <div style="margin-bottom:24px;">
                  <label style="font-size:.78rem;font-weight:700;color:#7b88a0;text-transform:uppercase;letter-spacing:.4px;display:block;margin-bottom:6px;">
                    <i class="fas fa-door-open"></i> Sala
                  </label>
                  <select name="sala_qr" required
                          style="width:100%;border:1.5px solid #e3e8f4;border-radius:8px;padding:9px 12px;font-family:inherit;font-size:.9rem;color:#1e2a45;background:#f7f9fe;">
                    <option value="">— Selecione a sala —</option>
                    <?php
                    $res_salas = $db->prepare("
                        SELECT DISTINCT s.id, s.nome
                        FROM salas s
                        INNER JOIN equipamento e ON e.id_sala = s.id
                        WHERE s.id_escola = ?
                        ORDER BY s.nome
                    ");
                    $res_salas->bind_param('i', $id_esc);
                    $res_salas->execute();
                    $salas = $res_salas->get_result()->fetch_all(MYSQLI_ASSOC);
                    $res_salas->close();
                    foreach ($salas as $sl):
                    ?>
                      <option value="<?php echo (int)$sl['id']; ?>">
                        <?php echo htmlspecialchars($sl['nome']); ?>
                      </option>
                    <?php endforeach; ?>
                  </select>
                </div>

                <button type="submit"
                        style="width:100%;background:#4b6cb7;color:#fff;border:none;border-radius:8px;padding:12px;font-family:inherit;font-size:.95rem;font-weight:700;cursor:pointer;display:flex;align-items:center;justify-content:center;gap:8px;transition:background .2s;">
                  <i class="fas fa-file-pdf"></i> Gerar PDF com QR Codes
                </button>

              </form>

              <div style="margin-top:20px;padding:14px 16px;background:#f0f4fb;border:1px solid #e3e8f4;border-radius:8px;font-size:.78rem;color:#7b88a0;line-height:1.6;">
                <strong style="color:#4b6cb7;display:block;margin-bottom:4px;"><i class="fas fa-info-circle"></i> Como funciona</strong>
                O PDF gerado contém uma etiqueta por equipamento com um QR Code. Ao ser lido com o telemóvel, abre diretamente o formulário de avaria com a sala e o equipamento pré-preenchidos — sem necessidade de login.
                <?php if (!file_exists(__DIR__ . '/phpqrcode/qrlib.php')): ?>
                <br><br>
                <strong style="color:#c8860a;"><i class="fas fa-exclamation-triangle"></i> Nota:</strong>
                A biblioteca <code>phpqrcode</code> não foi encontrada. Os QR Codes serão gerados via Google Charts API (requer ligação à internet). Para funcionamento offline, coloque a pasta <code>phpqrcode/</code> dentro de <code>gei/</code>.
                <?php endif; ?>
              </div>







            </div>


                  <a href="<?php echo SVRURL ?>equip">
    <img src="<?php echo SVRURL ?>images/voltar.svg" alt="Voltar">
</a>
<br><br>
          </div>

    



        </div>
      </div>
    </div>






    <?php include('footer.php'); ?>
</body>
</html>
