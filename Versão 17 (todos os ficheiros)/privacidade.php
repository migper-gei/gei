<?php
/*
 * privacidade.php — Política de Privacidade do Sistema GEI
 * Conformidade com o RGPD (Regulamento (UE) 2016/679)
 */
if (session_status() === PHP_SESSION_NONE) {
    $isHttps = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off');
    session_name('gei_session');
    session_set_cookie_params(['lifetime'=>0,'path'=>'/','secure'=>$isHttps,'httponly'=>true,'samesite'=>'Lax']);
    session_start();
    if (!isset($_SESSION['_created'])) { $_SESSION['_created'] = time(); }
    elseif (time() - $_SESSION['_created'] > 1800) { session_regenerate_id(true); $_SESSION['_created'] = time(); }
}
?>
<!DOCTYPE html>
<html lang="pt">
<?php include ("head.php"); ?>
<head>
<title>Política de Privacidade — GEI</title>
<style>
:root {
    --pp-primary:  #1a3f6f;
    --pp-blue:     #2e75b6;
    --pp-border:   #d0dff0;
    --pp-text:     #1e2a3a;
    --pp-muted:    #5f6f85;
    --pp-radius:   10px;
}
.pp-hero {
    background: linear-gradient(135deg,#1a3f6f 0%,#2e6db4 100%);
    color:#fff; padding:52px 20px 44px; text-align:center;
}
.pp-hero-badge {
    display:inline-flex; align-items:center; gap:6px;
    background:rgba(255,255,255,.18); border:1px solid rgba(255,255,255,.32);
    border-radius:20px; padding:4px 14px; font-size:.75rem; font-weight:600;
    letter-spacing:.4px; margin-bottom:14px;
}
.pp-hero h1 { font-size:1.9rem; font-weight:700; margin:0 0 8px; letter-spacing:-.4px; }
.pp-hero p  { opacity:.8; font-size:.875rem; margin:0; }

.pp-wrap {
    max-width:860px; margin:0 auto; padding:40px 20px 80px;
    color:var(--pp-text); font-size:15px; line-height:1.8;
}
.pp-toc {
    background:#eef4fb; border:1px solid var(--pp-border);
    border-radius:var(--pp-radius); padding:18px 24px 16px; margin-bottom:40px; font-size:.875rem;
}
.pp-toc-title { font-size:.78rem; font-weight:700; text-transform:uppercase; letter-spacing:.7px; color:var(--pp-primary); margin-bottom:12px; }
.pp-toc ol   { margin:0; padding-left:18px; columns:2; column-gap:24px; }
.pp-toc li   { margin-bottom:6px; }
.pp-toc a    { color:var(--pp-blue); text-decoration:none; }
.pp-toc a:hover { text-decoration:underline; }
@media(max-width:520px){ .pp-toc ol { columns:1; } }

h2.pp-h {
    font-size:1.05rem; font-weight:700; color:var(--pp-primary);
    margin:48px 0 12px; padding-bottom:8px; border-bottom:2px solid var(--pp-border);
    scroll-margin-top:20px; display:flex; align-items:center; gap:10px;
}
.pp-num {
    display:inline-flex; align-items:center; justify-content:center;
    width:26px; height:26px; background:var(--pp-primary); color:#fff;
    border-radius:50%; font-size:.75rem; flex-shrink:0;
}
p.pp-p { margin:0 0 12px; }
ul.pp-ul { margin:0 0 16px; padding-left:22px; }
ul.pp-ul li { margin-bottom:6px; }

.pp-box {
    border-radius:var(--pp-radius); padding:14px 18px;
    margin:16px 0 20px; font-size:.875rem; line-height:1.65;
}
.pp-box-blue   { background:#eef4fb; border-left:4px solid var(--pp-blue); color:var(--pp-primary); }
.pp-box-green  { background:#edf7f0; border-left:4px solid #2e7d5a; color:#1b4d34; }
.pp-box-yellow { background:#fdf9ec; border-left:4px solid #c8960a; color:#5a4100; }
.pp-box strong { display:block; margin-bottom:4px; font-size:.78rem; text-transform:uppercase; letter-spacing:.4px; }

.pp-rights {
    display:grid; grid-template-columns:repeat(auto-fill,minmax(240px,1fr));
    gap:12px; margin:16px 0 24px;
}
.pp-right-card {
    background:#fff; border:1px solid var(--pp-border);
    border-radius:var(--pp-radius); padding:16px 18px; display:flex; gap:14px; align-items:flex-start;
}
.pp-right-icon {
    width:36px; height:36px; border-radius:8px; background:#eef4fb; color:var(--pp-blue);
    display:flex; align-items:center; justify-content:center; font-size:.95rem; flex-shrink:0;
}
.pp-right-card strong { display:block; font-size:.875rem; color:var(--pp-primary); margin-bottom:3px; }
.pp-right-card span   { font-size:.8rem; color:var(--pp-muted); line-height:1.5; }

.pp-table {
    width:100%; border-collapse:collapse; font-size:.875rem;
    margin:16px 0 24px; border-radius:var(--pp-radius); overflow:hidden;
    border:1px solid var(--pp-border);
}
.pp-table th {
    background:var(--pp-primary); color:#fff; padding:10px 14px;
    text-align:left; font-weight:600; font-size:.8rem; letter-spacing:.3px;
}
.pp-table td { padding:9px 14px; border-bottom:1px solid var(--pp-border); vertical-align:top; }
.pp-table tr:last-child td { border-bottom:none; }
.pp-table tr:nth-child(even) td { background:#f5f8fd; }

.pp-contact {
    background:#edf7f0; border:1px solid #b2d8c0; border-radius:var(--pp-radius);
    padding:20px 24px; margin:16px 0;
}
.pp-contact strong { color:#1b4d34; display:block; margin-bottom:6px; }
.pp-contact a { color:#1b6b3a; }
.pp-contact-blue { background:#eef4fb; border-color:#b2cce8; }
.pp-contact-blue strong { color:var(--pp-primary); }
.pp-contact-blue a { color:var(--pp-blue); }

.pp-back {
    display:inline-flex; align-items:center; gap:6px;
    color:var(--pp-blue); text-decoration:none; font-size:.875rem; font-weight:600; margin-bottom:8px;
}
.pp-back:hover { text-decoration:underline; }
.pp-updated {
    text-align:center; font-size:.8rem; color:var(--pp-muted);
    margin-top:40px; padding-top:20px; border-top:1px solid var(--pp-border);
}
@media(max-width:600px){
    .pp-hero h1 { font-size:1.4rem; }
    .pp-wrap    { padding:24px 14px 60px; }
    .pp-table thead { display:none; }
    .pp-table td { display:block; padding:6px 10px; }
    .pp-table td::before { content:attr(data-label)": "; font-weight:700; color:var(--pp-primary); }
}
</style>
</head>
<body class="main-layout">
<?php include("loader.php"); ?>
<?php include ("header2.php"); ?>

<div class="pp-hero">
    <div class="pp-hero-badge"><i class="fas fa-shield-alt"></i> RGPD · Regulamento (UE) 2016/679</div>
    <h1>Política de Privacidade</h1>
    <p>Sistema de Gestão de Equipamentos Informáticos — GEI &nbsp;·&nbsp; Versão 17</p>
</div>

<div class="pp-wrap">

    <a href="<?php echo SVRURL ?>l" class="pp-back"><i class="fas fa-arrow-left"></i> Voltar ao login</a>

    <div class="pp-toc">
        <div class="pp-toc-title"><i class="fas fa-list" style="margin-right:6px"></i>Índice</div>
        <ol>
            <li><a href="#s1">Responsável pelo tratamento</a></li>
            <li><a href="#s2">Dados recolhidos</a></li>
            <li><a href="#s3">Finalidades e base legal</a></li>
            <li><a href="#s4">Conservação dos dados</a></li>
            <li><a href="#s5">Partilha e transferências</a></li>
            <li><a href="#s6">Segurança</a></li>
            <li><a href="#s7">Os seus direitos</a></li>
            <li><a href="#s8">Cookies e sessões</a></li>
            <li><a href="#s9">Menores</a></li>
            <li><a href="#s10">Contacto e reclamações</a></li>
        </ol>
    </div>

    <div class="pp-box pp-box-blue">
        <strong>Resumo</strong>
        O GEI trata dados pessoais de utilizadores (funcionários e colaboradores de escolas e instituições)
        exclusivamente para fins de gestão de equipamentos e manutenção. Os dados não são vendidos,
        partilhados com terceiros para fins comerciais, nem usados para publicidade.
    </div>

    <!-- 1 -->
    <h2 class="pp-h" id="s1"><span class="pp-num">1</span>Responsável pelo tratamento</h2>
    <p class="pp-p">
        O responsável pelo tratamento dos dados pessoais é a <strong>instituição escolar ou organização</strong>
        que adotou o sistema GEI para gestão interna dos seus equipamentos. Cada instituição actua como
        responsável pelo tratamento nos termos do artigo 4.º, n.º 7 do RGPD.
    </p>
    <p class="pp-p">
        O desenvolvedor do sistema (Miguel A. R. Pereira,
        <a href="mailto:gei@miguelarpereira.pt">gei@miguelarpereira.pt</a>) actua como
        <strong>subcontratante</strong> nos termos do artigo 28.º do RGPD, prestando o serviço de software
        sem acesso autónomo aos dados pessoais das instituições clientes.
    </p>
    <div class="pp-box pp-box-yellow">
        <strong>Nota para administradores da instituição</strong>
        A instituição que implanta o GEI deve nomear um Encarregado de Proteção de Dados (EPD/DPO) quando
        o RGPD o exija (art. 37.º), registar as atividades de tratamento (art. 30.º) e assegurar que os
        utilizadores são informados nos termos desta política.
    </div>

    <!-- 2 -->
    <h2 class="pp-h" id="s2"><span class="pp-num">2</span>Dados pessoais recolhidos</h2>
    <table class="pp-table">
        <thead><tr><th>Categoria</th><th>Dados concretos</th><th>Quem fornece</th></tr></thead>
        <tbody>
            <tr><td data-label="Categoria"><strong>Identificação</strong></td><td data-label="Dados">Nome, endereço de email</td><td data-label="Fonte">Administrador / utilizador</td></tr>
            <tr><td data-label="Categoria"><strong>Credenciais de acesso</strong></td><td data-label="Dados">Password (hash Argon2ID — nunca em claro)</td><td data-label="Fonte">Utilizador</td></tr>
            <tr><td data-label="Categoria"><strong>Dados de utilização</strong></td><td data-label="Dados">Data de criação, data de alteração de password, perfil (admin/reparador/funcionário)</td><td data-label="Fonte">Sistema (automático)</td></tr>
            <tr><td data-label="Categoria"><strong>Requisições e movimentos</strong></td><td data-label="Dados">Requisições de material e transferências de equipamentos associadas ao utilizador</td><td data-label="Fonte">Utilizador</td></tr>
            <tr><td data-label="Categoria"><strong>Comunicações internas</strong></td><td data-label="Dados">Mensagens de chat entre utilizadores dentro da plataforma</td><td data-label="Fonte">Utilizador</td></tr>
            <tr><td data-label="Categoria"><strong>Registos de avarias</strong></td><td data-label="Dados">Descrição, vídeos opcionais, emails de notificação enviados</td><td data-label="Fonte">Utilizador</td></tr>
            <tr><td data-label="Categoria"><strong>Dados de periféricos</strong></td><td data-label="Dados">Interface de rato e teclado (campos ratointerface / tecladointerface)</td><td data-label="Fonte">Administrador</td></tr>
        </tbody>
    </table>
    <div class="pp-box pp-box-green">
        <strong>Dados não recolhidos</strong>
        O GEI <strong>não recolhe</strong> dados de categorias especiais (art. 9.º RGPD) — saúde, origem étnica,
        opiniões políticas, dados biométricos. Não são usados cookies de rastreamento nem analytics de terceiros.
    </div>

    <!-- 3 -->
    <h2 class="pp-h" id="s3"><span class="pp-num">3</span>Finalidades e base legal do tratamento</h2>
    <table class="pp-table">
        <thead><tr><th>Finalidade</th><th>Base legal (RGPD art. 6.º)</th></tr></thead>
        <tbody>
            <tr><td data-label="Finalidade">Gestão de acesso e autenticação</td><td data-label="Base legal">al. b) — execução de contrato / relação laboral</td></tr>
            <tr><td data-label="Finalidade">Registo e acompanhamento de avarias e manutenções</td><td data-label="Base legal">al. b) e al. e) — execução de contrato / interesse público</td></tr>
            <tr><td data-label="Finalidade">Envio de notificações por email sobre avarias</td><td data-label="Base legal">al. b) — execução de contrato</td></tr>
            <tr><td data-label="Finalidade">Recuperação de password (link temporário)</td><td data-label="Base legal">al. b) — execução de contrato</td></tr>
            <tr><td data-label="Finalidade">Relatórios e estatísticas de equipamentos</td><td data-label="Base legal">al. e) — interesse público / gestão institucional</td></tr>
            <tr><td data-label="Finalidade">Chat interno entre utilizadores</td><td data-label="Base legal">al. b) — execução de contrato / relação laboral</td></tr>
        </tbody>
    </table>

    <!-- 4 -->
    <h2 class="pp-h" id="s4"><span class="pp-num">4</span>Conservação dos dados</h2>
    <ul class="pp-ul">
        <li><strong>Conta ativa:</strong> durante toda a vigência da relação laboral/contratual.</li>
        <li><strong>Conta inativa:</strong> até 1 ano após cessação, salvo obrigação legal superior.</li>
        <li><strong>Registos de avarias e manutenção:</strong> até 5 anos (gestão patrimonial).</li>
        <li><strong>Tokens de redefinição de password:</strong> expiram automaticamente ao fim de 1 hora; eliminados após uso.</li>
        <li><strong>Mensagens de chat:</strong> conservadas enquanto a conta estiver ativa.</li>
        <li><strong>Backups:</strong> conforme política de retenção definida pelo administrador da instituição.</li>
    </ul>

    <!-- 5 -->
    <h2 class="pp-h" id="s5"><span class="pp-num">5</span>Partilha e transferência de dados</h2>
    <p class="pp-p">Os dados pessoais <strong>não são vendidos nem partilhados com terceiros para fins comerciais</strong>. A partilha ocorre apenas em:</p>
    <ul class="pp-ul">
        <li><strong>Utilizadores da mesma instituição:</strong> dados de identificação visíveis a administradores e reparadores no âmbito das suas funções.</li>
        <li><strong>Fornecedor Escola Digital:</strong> quando configurado, o email de avaria pode ser enviado ao fornecedor.</li>
        <li><strong>Servidor SMTP:</strong> o email é transmitido ao servidor SMTP configurado pela instituição para notificações e links de redefinição.</li>
        <li><strong>Obrigações legais:</strong> em caso de requisição por autoridade judicial ou regulatória competente.</li>
    </ul>
    <p class="pp-p">Não são realizadas transferências para fora do EEE, salvo se o servidor SMTP da instituição estiver sediado fora do EEE — situação da responsabilidade da instituição.</p>

    <!-- 6 -->
    <h2 class="pp-h" id="s6"><span class="pp-num">6</span>Segurança dos dados</h2>
    <ul class="pp-ul">
        <li><strong>Passwords:</strong> hash Argon2ID — nunca armazenadas em texto claro.</li>
        <li><strong>Comunicação:</strong> HTTPS/TLS em todas as comunicações.</li>
        <li><strong>Sessões:</strong> cookies com flags <code>HttpOnly</code>, <code>Secure</code> e <code>SameSite=Lax</code>; ID regenerado periodicamente.</li>
        <li><strong>Tokens de redefinição:</strong> gerados com <code>random_bytes()</code> criptograficamente seguro; apenas o hash SHA-256 é guardado na BD.</li>
        <li><strong>Proteção CSRF:</strong> formulários protegidos com tokens de sessão de uso único.</li>
        <li><strong>SQL injection:</strong> todas as queries usam prepared statements parametrizados.</li>
        <li><strong>Backups:</strong> funcionalidade de cópia e restauro disponível para o administrador.</li>
    </ul>
    <div class="pp-box pp-box-yellow">
        <strong>Responsabilidade da instituição</strong>
        A segurança do servidor (SO, MySQL/MariaDB, Apache/Nginx) é da responsabilidade exclusiva da
        instituição ou do seu fornecedor de alojamento.
    </div>

    <!-- 7 -->
    <h2 class="pp-h" id="s7"><span class="pp-num">7</span>Os seus direitos enquanto titular dos dados</h2>
    <p class="pp-p">Para exercer os seus direitos, contacte o responsável pelo tratamento da sua instituição (ver secção <a href="#s10">10</a>):</p>
    <div class="pp-rights">
        <div class="pp-right-card">
            <div class="pp-right-icon"><i class="fas fa-eye"></i></div>
            <div><strong>Acesso (art. 15.º)</strong><span>Saber quais os dados que temos sobre si e como são usados.</span></div>
        </div>
        <div class="pp-right-card">
            <div class="pp-right-icon"><i class="fas fa-pen"></i></div>
            <div><strong>Retificação (art. 16.º)</strong><span>Corrigir dados incorretos ou incompletos.</span></div>
        </div>
        <div class="pp-right-card">
            <div class="pp-right-icon"><i class="fas fa-trash-alt"></i></div>
            <div><strong>Apagamento (art. 17.º)</strong><span>Solicitar a eliminação dos seus dados, quando aplicável.</span></div>
        </div>
        <div class="pp-right-card">
            <div class="pp-right-icon"><i class="fas fa-pause-circle"></i></div>
            <div><strong>Limitação (art. 18.º)</strong><span>Restringir o tratamento em certas circunstâncias.</span></div>
        </div>
        <div class="pp-right-card">
            <div class="pp-right-icon"><i class="fas fa-download"></i></div>
            <div><strong>Portabilidade (art. 20.º)</strong><span>Receber os seus dados num formato legível por máquina.</span></div>
        </div>
        <div class="pp-right-card">
            <div class="pp-right-icon"><i class="fas fa-ban"></i></div>
            <div><strong>Oposição (art. 21.º)</strong><span>Opor-se ao tratamento baseado em interesse legítimo.</span></div>
        </div>
    </div>
    <p class="pp-p">Tem o direito de apresentar reclamação à <strong>Comissão Nacional de Proteção de Dados (CNPD)</strong> — <a href="https://www.cnpd.pt" target="_blank" rel="noopener">www.cnpd.pt</a>.</p>

    <!-- 8 -->
    <h2 class="pp-h" id="s8"><span class="pp-num">8</span>Cookies e sessões</h2>
    <table class="pp-table">
        <thead><tr><th>Cookie</th><th>Tipo</th><th>Finalidade</th><th>Duração</th></tr></thead>
        <tbody>
            <tr>
                <td data-label="Cookie"><code>gei_session</code></td>
                <td data-label="Tipo">Sessão (técnico)</td>
                <td data-label="Finalidade">Manter a sessão autenticada e proteger contra CSRF</td>
                <td data-label="Duração">Fim da sessão (browser fechado)</td>
            </tr>
        </tbody>
    </table>
    <div class="pp-box pp-box-green">
        <strong>Sem cookies de terceiros</strong>
        O GEI não utiliza cookies de rastreamento, publicidade, Google Analytics, Facebook Pixel
        ou qualquer ferramenta de análise de comportamento de terceiros. O único cookie usado é o
        cookie de sessão estritamente necessário, isento de consentimento nos termos da Diretiva ePrivacy.
    </div>

    <!-- 9 -->
    <h2 class="pp-h" id="s9"><span class="pp-num">9</span>Menores</h2>
    <p class="pp-p">
        O GEI é um sistema de gestão interna destinado a funcionários e colaboradores de instituições —
        <strong>não é destinado a menores de 18 anos</strong> enquanto utilizadores registados.
        Quando a instituição crie contas para pessoal menor de idade, é da sua responsabilidade
        garantir os requisitos adicionais do RGPD, incluindo consentimento parental quando exigível.
    </p>

    <!-- 10 -->
    <h2 class="pp-h" id="s10"><span class="pp-num">10</span>Contacto, EPD e reclamações</h2>
    <p class="pp-p">Para exercer os seus direitos ou esclarecer questões, contacte o <strong>administrador do sistema GEI da sua instituição</strong>.</p>

    <div class="pp-contact">
        <strong><i class="fas fa-envelope" style="margin-right:7px"></i>Contacto do desenvolvedor (subcontratante)</strong>
        Para questões relativas ao funcionamento do software GEI:<br>
        <a href="mailto:gei@miguelarpereira.pt">gei@miguelarpereira.pt</a>
    </div>

    <div class="pp-contact pp-contact-blue">
        <strong><i class="fas fa-gavel" style="margin-right:7px"></i>Autoridade de controlo — CNPD</strong>
        Comissão Nacional de Proteção de Dados<br>
        Rua de São Bento, 148-3.º · 1200-821 Lisboa<br>
        <a href="https://www.cnpd.pt" target="_blank" rel="noopener">www.cnpd.pt</a> &nbsp;·&nbsp;
        <a href="mailto:geral@cnpd.pt">geral@cnpd.pt</a>
    </div>

    <p class="pp-updated">
        <i class="fas fa-calendar-alt" style="margin-right:5px"></i>
        Última atualização: <strong>16 de março de 2026</strong> &nbsp;·&nbsp;
        Esta política está sempre disponível em
        <a href="<?php echo SVRURL ?>privacidade.php"><?php echo SVRURL ?>privacidade.php</a>
    </p>

</div>

<?php include ("footer.php"); ?>
</body>
</html>
