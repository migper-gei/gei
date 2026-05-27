<?php
/**
 * MOVER_CABECALHO_ESCOLA.php
 * ─────────────────────────────────────────────────────────────
 * Compatível com PHP 7.2+  (XAMPP)
 *
 * O que faz:
 *   1. Detecta em cada ficheiro .php o bloco de cabeçalho com
 *      o nome da escola/sala (ex: "AE.....SECUNDÁRIA.....")
 *      que aparece ACIMA da zona do utilizador.
 *   2. Remove esse bloco da posição actual.
 *   3. Insere, ABAIXO do welcome-section e ACIMA da tabela,
 *      o novo cabeçalho com o mesmo aspeto gráfico de
 *      ver_equipamentos_sala.php  (display:flex, ícones SVG,
 *      nome da sala | nome da escola).
 *
 * As variáveis PHP ($ns/$ne etc.) são detectadas
 * automaticamente em cada ficheiro.
 * ─────────────────────────────────────────────────────────────
 */

// ── CONFIGURAÇÃO ──────────────────────────────────────────────

$basePath  = __DIR__;
$recursive = false;

$ignoreFiles = array(
    basename(__FILE__),
    'head.php',
    'header.php',
    'footer.php',
    'jquery_bootstrap.php',
    'num_linhas.php',
    'msg_bemvindo.php',
    'sessao_timeout.php',
    'verifica_sessao.php',
    'aplicar_breadcrumb.php',
);

// ── GERADOR DO NOVO CABEÇALHO (aspeto de ver_equipamentos_sala.php) ──────────
//
// Recebe as expressões PHP a usar para o nome da sala ($exprSala)
// e o nome da escola ($exprEscola), já com htmlspecialchars se necessário.
//
// Exemplo de chamada:
//   gei_makeHeader('htmlspecialchars($ns, ENT_QUOTES, \'UTF-8\')',
//                  'htmlspecialchars($ne, ENT_QUOTES, \'UTF-8\')')
//
function gei_makeHeader($exprSala, $exprEscola) {
    // Se a expressão já tem htmlspecialchars não duplicar; caso contrário envolver
    if (strpos($exprSala, 'htmlspecialchars') === false) {
        $exprSala   = 'htmlspecialchars(' . $exprSala   . ', ENT_QUOTES, \'UTF-8\')';
    }
    if (strpos($exprEscola, 'htmlspecialchars') === false) {
        $exprEscola = 'htmlspecialchars(' . $exprEscola . ', ENT_QUOTES, \'UTF-8\')';
    }

    return <<<'HEREDOC'
               <!-- ========================================================
                    CABEÇALHO: sala + escola na mesma linha, por baixo do utilizador
                    ======================================================== -->
               <div style="display:flex; align-items:center; flex-wrap:wrap; gap:10px; margin:14px 0 10px; padding:10px 16px; background:#f4f6fb; border:1px solid #e3e8f4; border-radius:10px;">

                  <!-- Nome da sala em destaque -->
                  <span style="display:inline-flex; align-items:center; gap:7px; font-size:1.1rem; font-weight:700; color:#182848;">
                     <svg width="16" height="16" viewBox="0 0 24 24" fill="none"
                          stroke="#4b6cb7" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"
                          style="flex-shrink:0;">
                        <rect x="3" y="3" width="18" height="18" rx="2"/>
                        <path d="M3 9h18M9 21V9"/>
                     </svg>
                     <?php echo EXPR_SALA; ?>
                  </span>

                  <!-- Separador -->
                  <span style="color:#c5cde0; font-size:1.1rem; font-weight:300;">|</span>

                  <!-- Nome da escola -->
                  <span style="display:inline-flex; align-items:center; gap:6px; font-size:.92rem; font-weight:500; color:#5a6a85;">
                     <svg width="14" height="14" viewBox="0 0 24 24" fill="none"
                          stroke="#7b88a0" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"
                          style="flex-shrink:0;">
                        <path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/>
                        <polyline points="9 22 9 12 15 12 15 22"/>
                     </svg>
                     <?php echo EXPR_ESCOLA; ?>
                  </span>

               </div>
               <!-- ===== FIM CABEÇALHO ===== -->
HEREDOC;
    // (substituição dos placeholders feita fora do heredoc para compatibilidade)
}

// Wrapper que substitui os placeholders após o heredoc
function gei_buildHeader($exprSala, $exprEscola) {
    if (strpos($exprSala, 'htmlspecialchars') === false) {
        $exprSala   = 'htmlspecialchars(' . $exprSala   . ', ENT_QUOTES, \'UTF-8\')';
    }
    if (strpos($exprEscola, 'htmlspecialchars') === false) {
        $exprEscola = 'htmlspecialchars(' . $exprEscola . ', ENT_QUOTES, \'UTF-8\')';
    }

    $tpl = gei_makeHeader($exprSala, $exprEscola);
    $tpl = str_replace('EXPR_SALA',   $exprSala,   $tpl);
    $tpl = str_replace('EXPR_ESCOLA', $exprEscola, $tpl);
    return $tpl;
}

// ── DETECÇÃO DAS VARIÁVEIS DE SALA E ESCOLA NO FICHEIRO ───────
//
// Tenta descobrir quais as variáveis PHP usadas para o nome da
// sala e da escola, analisando o conteúdo do ficheiro.
//
function gei_detectVars($content) {
    // Variáveis candidatas para SALA (por ordem de preferência)
    $salaCandidates   = array('$ns', '$nome_sala',   '$nomesala',   '$sala',   '$nSala');
    // Variáveis candidatas para ESCOLA
    $escolaCandidates = array('$ne', '$nome_escola', '$nomeescola', '$escola', '$nEscola');

    $varSala   = null;
    $varEscola = null;

    foreach ($salaCandidates as $v) {
        // Procura a variável usada fora de comentários PHP
        if (preg_match('/' . preg_quote($v, '/') . '\b/', $content)) {
            $varSala = $v;
            break;
        }
    }
    foreach ($escolaCandidates as $v) {
        if (preg_match('/' . preg_quote($v, '/') . '\b/', $content)) {
            $varEscola = $v;
            break;
        }
    }

    return array($varSala, $varEscola);
}

// ── PADRÕES DE DETECÇÃO DO BLOCO ANTIGO ───────────────────────
//
// Detecta o bloco existente (seja qual for a forma) que contém
// o nome da escola/sala e está ANTES do welcome-section.
// O padrão é propositadamente largo para apanhar muitas variantes.
//

// Padrão A — <div style="display:flex..."> com var de escola/sala
$patternFlex =
    '/<div\b[^>]*style\s*=\s*(?:"[^"]*display\s*:\s*flex[^"]*"|\'[^\']*display\s*:\s*flex[^\']*\')[^>]*>'
  . '(?:(?!<div\b).)*?'
  . '(?:\$ns\b|\$ne\b|\$nome_escola\b|\$nomeescola\b|\$escola\b|\$sala\b|\$nome_sala\b'
  .   '|htmlspecialchars\s*\(\s*\$n[se]\b|htmlspecialchars\s*\(\s*\$nome_escola\b)'
  . '.*?<\/div>'
  . '\s*(?:<!--[^>]*-->\s*)?/si';

// Padrão B — <hN> com echo da escola/sala
$patternHeading =
    '/<h[1-4]\b[^>]*>'
  . '[^<]*<\?php[^?]*(?:echo|print)\s*[^;]*'
  . '(?:\$ne\b|\$ns\b|\$nome_escola\b|\$nomeescola\b|\$escola\b|\$sala\b|\$nome_sala\b'
  .   '|htmlspecialchars\s*\(\s*\$n[se]\b)'
  . '[^?]*\?>[^<]*<\/h[1-4]>/si';

// Padrão C — <div class contendo "titulo/header/escola/escola"> com echo
$patternDivClass =
    '/<div\b[^>]*class\s*=\s*(?:"[^"]*(?:titulo|header|escola|school|nome-)[^"]*"|\'[^\']*(?:titulo|header|escola|school|nome-)[^\']*\')[^>]*>'
  . '.*?'
  . '(?:\$ne\b|\$ns\b|\$nome_escola\b|\$nomeescola\b|\$escola\b|\$sala\b'
  .   '|htmlspecialchars\s*\(\s*\$n[se]\b)'
  . '.*?<\/div>/si';

// Padrão D — <div style="font-weight:700/bold"> com echo de escola (padrão da imagem)
$patternBold =
    '/<div\b[^>]*style\s*=\s*(?:"[^"]*font-weight\s*:\s*(?:700|bold|bolder)[^"]*"|\'[^\']*font-weight\s*:\s*(?:700|bold|bolder)[^\']*\')[^>]*>'
  . '.*?'
  . '(?:\$ne\b|\$ns\b|\$nome_escola\b|\$nomeescola\b|\$escola\b|\$sala\b'
  .   '|htmlspecialchars\s*\(\s*\$n[se]\b)'
  . '.*?<\/div>/si';

// Padrão E — <hr> + texto bold de escola (versão antiga)
$patternHr =
    '/(?:<hr[^>]*>\s*){1,2}\s*(?:<[bBhH][^>]*>)?\s*'
  . '<\?php[^?]*(?:echo|print)\s*[^;]*'
  . '(?:\$ne\b|\$ns\b|\$nome_escola\b|\$nomeescola\b|\$escola\b|\$sala\b'
  .   '|htmlspecialchars\s*\(\s*\$n[se]\b)'
  . '[^?]*\?>\s*(?:<\/[bBhH][1-6]>)?\s*(?:<hr[^>]*>\s*)?/si';

$allPatterns = array(
    'flex'        => $patternFlex,
    'heading'     => $patternHeading,
    'div-class'   => $patternDivClass,
    'bold-div'    => $patternBold,
    'hr-escola'   => $patternHr,
);

// ── PADRÃO DO PONTO DE INSERÇÃO ───────────────────────────────

// Após o </div> que fecha o welcome-section / msg_bemvindo
$patternWelcomeClose =
    '/((?:<!--\s*Welcome[^>]*-->\s*)?'
  . '<div\b[^>]*class\s*=\s*(?:"[^"]*welcome[^"]*"|\'[^\']*welcome[^\']*\')[^>]*>'
  . '.*?'
  . 'include\s*\(\s*(?:"msg_bemvindo\.php"|\'msg_bemvindo\.php\')\s*\)'
  . '.*?\?>\s*\s*<\/div>\s*)/si';

// Alternativa: include + fecho PHP + </div> imediato
$patternWelcomeSimple =
    '/(include\s*\(\s*(?:"msg_bemvindo\.php"|\'msg_bemvindo\.php\')\s*\)\s*;?\s*\?>\s*\s*<\/div>\s*)/si';

$welcomePatterns = array($patternWelcomeClose, $patternWelcomeSimple);

// ── FUNÇÕES AUXILIARES ────────────────────────────────────────

function gei_collectPhpFiles($dir, $recursive, $ignore) {
    $files = array();
    $items = scandir($dir);
    if ($items === false) return $files;
    foreach ($items as $item) {
        if ($item === '.' || $item === '..') continue;
        $path = $dir . DIRECTORY_SEPARATOR . $item;
        if (is_dir($path) && $recursive) {
            $files = array_merge($files, gei_collectPhpFiles($path, true, $ignore));
        } elseif (is_file($path) && strtolower(pathinfo($path, PATHINFO_EXTENSION)) === 'php') {
            if (!in_array(basename($path), $ignore, true)) {
                $files[] = $path;
            }
        }
    }
    return $files;
}

function gei_findOldBlock($content, $patterns) {
    foreach ($patterns as $name => $pattern) {
        if (preg_match($pattern, $content, $m)) {
            return array($name, $m[0]);
        }
    }
    return null;
}

// O bloco já está na posição correcta se estiver APÓS msg_bemvindo
// e ANTES de <table ou gei-table-wrap
function gei_alreadyCorrect($content, $block) {
    $posBlock    = strpos($content, $block);
    $posBemvindo = strpos($content, 'msg_bemvindo.php');
    if ($posBlock === false || $posBemvindo === false) return false;
    if ($posBlock <= $posBemvindo) return false;
    $posTable   = strpos($content, '<table');
    $posGeiWrap = strpos($content, 'gei-table-wrap');
    if ($posTable   !== false && $posBlock > $posTable)   return false;
    if ($posGeiWrap !== false && $posBlock > $posGeiWrap) return false;
    return true;
}

function gei_findInsertPos($content, $welcomePatterns) {
    foreach ($welcomePatterns as $pattern) {
        if (preg_match($pattern, $content, $m, PREG_OFFSET_CAPTURE)) {
            return $m[0][1] + strlen($m[0][0]);
        }
    }
    return false;
}

function gei_statusBadge($status) {
    if ($status === 'ok')        return array('b-ok',      '&#10003; movido');
    if ($status === 'error')     return array('b-err',     '&#10008; erro');
    if ($status === 'already')   return array('b-already', '&#10003; j&aacute; correcto');
    if ($status === 'noanchor')  return array('b-skip',    '&#8505; sem &acirc;ncora');
    if ($status === 'novars')    return array('b-skip',    '&#8505; vari&aacute;veis n&atilde;o detectadas');
    return array('b-skip', '&#8505; ' . htmlspecialchars($status));
}

// ── PROCESSAMENTO PRINCIPAL ───────────────────────────────────

$phpFiles = gei_collectPhpFiles($basePath, $recursive, $ignoreFiles);

$results = array();
$cntOk   = 0;
$cntNone = 0;
$cntErr  = 0;
$cntSkip = 0;

foreach ($phpFiles as $fpath) {
    $fname   = basename($fpath);
    $content = file_get_contents($fpath);

    // Só processa ficheiros que têm welcome-section
    if (strpos($content, 'msg_bemvindo.php') === false) {
        continue;
    }

    // Encontrar bloco antigo
    $found = gei_findOldBlock($content, $allPatterns);
    if ($found === null) {
        continue; // sem cabeçalho detectado
    }
    $patternName = $found[0];
    $oldBlock    = $found[1];

    // Verificar se já está correcto E já tem o novo aspeto gráfico
    $alreadyNew = (strpos($content, 'CABEÇALHO: sala + escola na mesma linha') !== false)
               || (strpos($content, 'CABECALHO: sala + escola') !== false);

    if ($alreadyNew && gei_alreadyCorrect($content, $oldBlock)) {
        $results[] = array(
            's' => 'already',
            'f' => $fname,
            'm' => 'J&aacute; tem o novo aspeto gr&aacute;fico na posi&ccedil;&atilde;o correcta',
        );
        $cntSkip++;
        continue;
    }

    // Detectar variáveis de sala e escola usadas neste ficheiro
    $vars      = gei_detectVars($content);
    $varSala   = $vars[0];
    $varEscola = $vars[1];

    // Se não encontrou variável de escola, não pode gerar o bloco
    if ($varEscola === null) {
        $results[] = array(
            's' => 'novars',
            'f' => $fname,
            'm' => 'Bloco antigo encontrado mas vari&aacute;veis de escola/sala n&atilde;o detectadas &mdash; ficheiro n&atilde;o alterado',
        );
        $cntNone++;
        continue;
    }

    // Se não há variável de sala, usa a de escola para ambos os campos
    // (páginas sem sala mostram apenas a escola)
    if ($varSala === null) {
        $varSala = $varEscola;
    }

    // Construir o novo bloco com o aspeto gráfico de ver_equipamentos_sala.php
    $newBlock = gei_buildHeader($varSala, $varEscola);

    // Backup
    $backup = $fpath . '.bak';
    if (!file_exists($backup) && !copy($fpath, $backup)) {
        $results[] = array(
            's' => 'error',
            'f' => $fname,
            'm' => 'N&atilde;o foi poss&iacute;vel criar backup &mdash; ignorado',
        );
        $cntErr++;
        continue;
    }

    // Remover o bloco antigo
    $contentWithout = str_replace($oldBlock, '', $content);
    $contentWithout = preg_replace('/\n{3,}/', "\n\n", $contentWithout);

    // Encontrar ponto de inserção (após welcome-section)
    $insertPos = gei_findInsertPos($contentWithout, $welcomePatterns);

    if ($insertPos === false) {
        $results[] = array(
            's' => 'noanchor',
            'f' => $fname,
            'm' => 'Bloco antigo encontrado (padr&atilde;o: ' . $patternName . ') mas &acirc;ncora de inser&ccedil;&atilde;o n&atilde;o encontrada &mdash; ficheiro n&atilde;o alterado',
        );
        $cntNone++;
        continue;
    }

    // Inserir novo bloco na posição correcta
    $newContent = substr($contentWithout, 0, $insertPos)
                . "\n" . $newBlock . "\n"
                . substr($contentWithout, $insertPos);

    if (file_put_contents($fpath, $newContent) === false) {
        $results[] = array(
            's' => 'error',
            'f' => $fname,
            'm' => 'Erro ao gravar o ficheiro',
        );
        $cntErr++;
        continue;
    }

    $results[] = array(
        's' => 'ok',
        'f' => $fname,
        'm' => 'Cabe&ccedil;alho substitu&iacute;do e movido &mdash; vari&aacute;veis: sala=<code>'
             . htmlspecialchars($varSala) . '</code> escola=<code>'
             . htmlspecialchars($varEscola) . '</code>'
             . ' (padr&atilde;o antigo: ' . $patternName . ')',
    );
    $cntOk++;
}

$total = count($phpFiles);

// ── SAÍDA ─────────────────────────────────────────────────────

if (PHP_SAPI === 'cli') {
    echo "=== MOVER + REFORMATAR CABECALHO ESCOLA/SALA ===\n";
    echo "Pasta : " . $basePath . "\n";
    echo "Ficheiros .php analisados: " . $total . "\n\n";
    if (empty($results)) {
        echo "Nenhum ficheiro com cabecalho deslocado encontrado.\n";
    } else {
        foreach ($results as $r) {
            if ($r['s'] === 'ok')       $p = '[OK]    ';
            elseif ($r['s'] === 'error')    $p = '[ERRO]  ';
            elseif ($r['s'] === 'already')  $p = '[JA OK] ';
            else                            $p = '[SKIP]  ';
            echo $p . $r['f'] . " -- " . strip_tags(html_entity_decode($r['m'], ENT_QUOTES, 'UTF-8')) . "\n";
        }
    }
    echo "\n--- RESUMO ---\n";
    echo "Actualizados : " . $cntOk   . "\n";
    echo "Ja correctos : " . $cntSkip . "\n";
    echo "Ignorados    : " . $cntNone . "\n";
    echo "Erros        : " . $cntErr  . "\n";
    echo "\nBackups: .bak  |  Apague este ficheiro apos execucao.\n";

} else {

    header('Content-Type: text/html; charset=utf-8');

?><!DOCTYPE html>
<html lang="pt">
<head>
<meta charset="utf-8">
<title>Mover + Reformatar Cabeçalho</title>
<style>
*{box-sizing:border-box;}
body{font-family:system-ui,sans-serif;background:#f4f6fb;padding:30px 24px;color:#1e2a45;margin:0;}
h2{color:#182848;margin-bottom:4px;}
.meta{font-size:.8rem;color:#7b88a0;margin-bottom:20px;line-height:1.9;}
table{border-collapse:collapse;width:100%;max-width:960px;background:#fff;
      border-radius:10px;overflow:hidden;box-shadow:0 2px 12px rgba(0,0,0,.09);}
th{background:#182848;color:#fff;padding:10px 16px;text-align:left;
   font-size:.78rem;text-transform:uppercase;letter-spacing:.4px;}
td{padding:9px 16px;font-size:.83rem;border-bottom:1px solid #eef1f8;vertical-align:middle;}
tr:last-child td{border-bottom:none;}
tr:hover td{background:#f7f9fe;}
.badge{display:inline-block;border-radius:5px;padding:2px 9px;font-size:.74rem;font-weight:700;white-space:nowrap;}
.b-ok     {background:#e6f9f2;color:#1a7a52;}
.b-err    {background:#fde8e6;color:#c0392b;}
.b-already{background:#eef2fb;color:#4b6cb7;}
.b-skip   {background:#fff8e8;color:#b07d00;}
.summary{margin-top:20px;background:#fff;border-radius:10px;padding:16px 20px;max-width:960px;
         box-shadow:0 2px 12px rgba(0,0,0,.09);font-size:.87rem;line-height:2.2;}
.summary b{color:#182848;}
.warn{margin-top:16px;max-width:960px;background:#fff8e8;border:1.5px solid #f0d98a;
      border-radius:8px;padding:12px 16px;font-size:.83rem;}
.info{margin-top:16px;max-width:960px;background:#eef2fb;border:1.5px solid #c7d4f0;
      border-radius:8px;padding:12px 16px;font-size:.83rem;line-height:1.7;}
.preview{margin-top:20px;max-width:960px;background:#fff;border-radius:10px;padding:20px;
         box-shadow:0 2px 12px rgba(0,0,0,.09);}
.preview h3{color:#182848;font-size:.85rem;text-transform:uppercase;letter-spacing:.4px;margin:0 0 14px;}
.empty{color:#7b88a0;font-style:italic;font-size:.84rem;margin-top:10px;}
code{background:#f0f4fb;padding:1px 5px;border-radius:3px;font-size:.82em;}
</style>
</head>
<body>

<h2>&#8597; Mover + Reformatar Cabeçalho Escola / Sala</h2>
<div class="meta">
    Pasta: <code><?php echo htmlspecialchars($basePath); ?></code><br>
    Ficheiros <code>.php</code> com <code>msg_bemvindo.php</code> analisados: <b><?php echo $total; ?></b>
</div>

<!-- Pré-visualização do novo aspeto gerado -->
<div class="preview">
    <h3>&#128065; Pré-visualização do novo cabeçalho inserido</h3>
    <div style="display:flex; align-items:center; flex-wrap:wrap; gap:10px; padding:10px 16px; background:#f4f6fb; border:1px solid #e3e8f4; border-radius:10px;">
        <span style="display:inline-flex; align-items:center; gap:7px; font-size:1.1rem; font-weight:700; color:#182848;">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#4b6cb7" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" style="flex-shrink:0;"><rect x="3" y="3" width="18" height="18" rx="2"/><path d="M3 9h18M9 21V9"/></svg>
            Nome da Sala
        </span>
        <span style="color:#c5cde0; font-size:1.1rem; font-weight:300;">|</span>
        <span style="display:inline-flex; align-items:center; gap:6px; font-size:.92rem; font-weight:500; color:#5a6a85;">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="#7b88a0" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" style="flex-shrink:0;"><path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/><polyline points="9 22 9 12 15 12 15 22"/></svg>
            AE Agrupamento de Escolas Exemplo
        </span>
    </div>
</div>

<div class="info">
    <b>O que foi feito:</b><br>
    O bloco antigo (texto em destaque com o nome da escola acima do utilizador) foi
    <b>removido</b> da posição original, <b>reformatado</b> com o mesmo aspeto gráfico de
    <code>ver_equipamentos_sala.php</code> (fundo azul claro, ícones SVG, sala | escola)
    e <b>inserido abaixo</b> do utilizador (<code>msg_bemvindo.php</code>) e
    <b>acima</b> da tabela principal.
</div>

<?php if (empty($results)): ?>
<p class="empty">Nenhum ficheiro com cabeçalho deslocado foi encontrado.</p>
<?php else: ?>
<table>
  <thead>
    <tr><th>Estado</th><th>Ficheiro</th><th>Detalhe</th></tr>
  </thead>
  <tbody>
  <?php foreach ($results as $r):
      $badge      = gei_statusBadge($r['s']);
      $badgeClass = $badge[0];
      $badgeText  = $badge[1];
  ?>
  <tr>
    <td><span class="badge <?php echo $badgeClass; ?>"><?php echo $badgeText; ?></span></td>
    <td><code><?php echo htmlspecialchars($r['f']); ?></code></td>
    <td><?php echo $r['m']; ?></td>
  </tr>
  <?php endforeach; ?>
  </tbody>
</table>
<?php endif; ?>

<div class="summary">
    <b>Actualizados com sucesso:</b> <?php echo $cntOk; ?><br>
    <b>Já tinham o novo aspeto:</b> <?php echo $cntSkip; ?><br>
    <b>Ignorados (sem âncora / vars):</b> <?php echo $cntNone; ?><br>
    <b>Erros:</b> <?php echo $cntErr; ?><br>
    <b>Backups:</b> extensão <code>.bak</code> (criados apenas na primeira execução)
</div>

<div class="warn">
    &#9888; <b>Verifique visualmente os ficheiros alterados antes de colocar em produção.</b><br>
    Se algo não estiver correcto, restaure a partir do <code>.bak</code>.<br>
    Apague este ficheiro após a execução &mdash; não deve ficar acessível publicamente.
</div>

</body>
</html>
<?php

} // fim else browser
