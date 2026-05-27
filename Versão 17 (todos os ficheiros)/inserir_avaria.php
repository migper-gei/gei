<?php
// Sessão segura
if (session_status() === PHP_SESSION_NONE) {
    $isHttps = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off');
    session_name('gei_session');
    session_set_cookie_params([
        'lifetime' => 0,
        'path'     => '/',
        'secure'   => $isHttps,
        'httponly' => true,
        'samesite' => 'Lax',
    ]);
    session_start();
    // Regenerar ID periodicamente (previne session fixation)
    if (!isset($_SESSION['_created'])) {
        $_SESSION['_created'] = time();
    } elseif (time() - $_SESSION['_created'] > 1800) {
        session_regenerate_id(true);
        $_SESSION['_created'] = time();
    }
}
?>

<?php
// Gerar token CSRF
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}
$csrf_token = $_SESSION['csrf_token'];
?>
<!DOCTYPE html>
<html lang="pt">
   <head>
 <meta charset="UTF-8">
 <style>
 .upload-container {
            background-color: #ffffff;
            border-radius: 12px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
            width: 100%;
            max-width: 600px;
            padding: 30px;
        }
        
        .upload-title {
            color: #4a5568;
            font-size: 24px;
            margin-bottom: 30px;
            text-align: center;
        }
        
        .upload-section {
            margin-bottom: 25px;
        }
        
        .upload-label {
            display: block;
            color: #64748b;
            font-size: 16px;
            margin-bottom: 10px;
        }
        
        .file-input-container {
            position: relative;
            margin-bottom: 5px;
        }
        
        .file-input {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            opacity: 0;
            cursor: pointer;
        }
        
        .file-input-button {
            background-color: #f0f2f5;
            border: 2px solid #cbd5e0;
            border-radius: 6px;
            color: #4a5568;
            cursor: pointer;
            font-size: 16px;
            padding: 12px 20px;
            text-align: left;
            transition: all 0.3s ease;
            width: 100%;
        }
        
        .file-input-button:hover {
            background-color: #e2e8f0;
        }
        
        .file-input-status {
            color: #718096;
            font-size: 14px;
            margin-top: 8px;
            font-style: italic;
        }

        /* ── Sistema de Ajuda ─────────────────────────────── */

        /* Tooltips de ajuda */
        .gei-help {
            display: inline-flex; align-items: center; justify-content: center;
            width: 17px; height: 17px; border-radius: 50%;
            background: #4b6cb7; color: #fff; font-size: .65rem; font-weight: 700;
            cursor: pointer; margin-left: 6px; position: relative;
            vertical-align: middle; flex-shrink: 0; border: none;
            font-style: normal; line-height: 1; user-select: none;
        }
        .gei-help::after {
            content: attr(data-tip);
            position: absolute; left: 24px; top: 50%; transform: translateY(-50%);
            background: #1e2a45; color: #fff; font-size: .75rem; font-weight: 400;
            padding: 9px 13px; border-radius: 8px; width: 250px; line-height: 1.55;
            z-index: 9999; box-shadow: 0 4px 18px rgba(0,0,0,.22);
            opacity: 0; pointer-events: none; transition: opacity .18s;
            white-space: normal; text-align: left;
        }
        .gei-help:hover::after,
        .gei-help.active::after { opacity: 1; }
        @media (max-width: 600px) {
            .gei-help::after { left: auto; right: 0; top: 28px; transform: none; width: 220px; }
        }

        /* Botões de template de descrição */
        .tmpl-section {
            margin-bottom: 8px;
        }
        .tmpl-section-label {
            font-size: .75rem; font-weight: 700; color: #7b88a0;
            margin-bottom: 7px; display: block;
        }
        .tmpl-btns {
            display: flex; flex-wrap: wrap; gap: 6px;
        }
        .tmpl-btn {
            background: #eef2fb; border: 1.5px solid #c9d4e8; color: #2E4057;
            border-radius: 20px; padding: 5px 11px; font-size: .76rem; font-weight: 600;
            cursor: pointer; transition: all .15s; white-space: nowrap;
        }
        .tmpl-btn:hover {
            background: #4b6cb7; color: #fff; border-color: #4b6cb7;
        }
        .tmpl-btn.active {
            background: #4b6cb7; color: #fff; border-color: #4b6cb7;
        }

        /* Contador e indicador de qualidade da descrição */
        .desc-meta {
            display: flex; justify-content: space-between; align-items: center;
            margin-top: 5px; min-height: 18px;
        }
        .desc-qualidade { font-size: .75rem; font-weight: 600; }
        .desc-contador  { font-size: .72rem; color: #7b88a0; }

        /* Checklist pré-submit */
        .checklist-wrap {
            margin: 18px 0 6px;
            border: 1px solid #e3e8f4;
            border-radius: 10px;
            overflow: hidden;
        }
        .checklist-summary {
            padding: 10px 14px;
            background: #f4f6fb;
            cursor: pointer;
            font-size: .82rem; font-weight: 700; color: #4b6cb7;
            display: flex; align-items: center; gap: 8px;
            list-style: none; user-select: none;
        }
        .checklist-summary::-webkit-details-marker { display: none; }
        .checklist-summary::before {
            content: '▶'; font-size: .6rem; transition: transform .2s; flex-shrink: 0;
        }
        details[open] .checklist-summary::before { transform: rotate(90deg); }
        .checklist-body {
            padding: 12px 16px; font-size: .82rem; color: #444;
        }
        .checklist-item {
            display: flex; align-items: flex-start; gap: 9px;
            margin-bottom: 9px; cursor: pointer;
        }
        .checklist-item:last-child { margin-bottom: 0; }
        .checklist-item input[type=checkbox] {
            margin-top: 2px; width: 15px; height: 15px; cursor: pointer; flex-shrink: 0;
        }
        .checklist-item span { line-height: 1.45; }
        .checklist-note {
            margin-top: 10px; font-size: .73rem; color: #7b88a0; font-style: italic;
        }
    </style>

<?php
 include ("css_inserir.php");

 include ("head.php");
?>

   </head>

   <!-- body -->
   <body class="main-layout">
      <?php include("loader.php"); ?>

     <?php include ("header.php");?>
     

     <?php
//session_start();

include("sessao_timeout.php");

 
  ?>

<?php

$sql1 = "select count(*) as cs from periodos";
$result1 = mysqli_query($db,$sql1); 
$rows =mysqli_fetch_row($result1);

if ($rows[0]==0)
{
?>

<script>
    
swal({
title: 'Não tem períodos definidos!',
//text: 'Os dados foram guardados!',
icon: 'error',
//buttons: false,

})
.then(function() {
window.location = "<?php echo SVRURL ?>avaria";
});

</script>

<?php

}
?>

<?php

$sql2a = "select max(id) as me  from escolas ";
$result2a = mysqli_query($db,$sql2a); 
$rows2a =mysqli_fetch_row($result2a);

$maxesc = $rows2a[0];

if (base64_decode($_GET["aves"])>$maxesc)
{

?>

<script>

window.setTimeout(function() {
              window.location.href = '<?php echo SVRURL ?>avaria';
          },10);
          </script>

<?php
}

   $idescola = (int)base64_decode($_GET["aves"]);
   

   if ( !isset($idescola)    || empty($idescola)     || !is_numeric($idescola) 
   )
   
   {
   ?>
   
   
   <script>
   window.setTimeout(function() {
       window.location.href = '<?php echo SVRURL ?>avaria';
   }, 10);
   </script>
   
   
   <?php
   }

$stmt11 = $db->prepare("SELECT nome_escola FROM escolas WHERE id = ?");
$stmt11->bind_param("i", $idescola);
$stmt11->execute();
$result11 = $stmt11->get_result();
$rows11   = $result11->fetch_row();
$stmt11->close();

$ne = $rows11[0] ?? '';

?>
      
      <!-- about -->
      <div  class="about">
         <div class="container">
            <div class="row">
               <div class="col-md-12">
               <!-- Breadcrumb melhorado -->
               <nav style="margin-bottom:10px;">
                  <ol style="display:flex;align-items:center;flex-wrap:wrap;gap:4px;list-style:none;padding:0;margin:0;font-size:.8rem;font-weight:600;color:#7b88a0;">
                     <li style="display:flex;align-items:center;gap:4px;">
                        <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="#4b6cb7" stroke-width="2.3" stroke-linecap="round" stroke-linejoin="round"><path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"/><line x1="12" y1="9" x2="12" y2="13"/><line x1="12" y1="17" x2="12.01" y2="17"/></svg>
                        <a href="<?php echo SVRURL ?>avaria" style="color:#4b6cb7;text-decoration:none;">Avarias</a>
               
                     </li>
                     <li style="color:#c5cde0;font-size:.9rem;">&#8250;</li>
                     <li style="color:#1e2a45;">Inserir</li>
                  </ol>
               </nav>
              
               </div>
            </div>
            
            <div class="container">
               <div class="row">
                  <div class="col-md-10 offset-md-2">
              
                   <!-- Welcome Section -->
 <div class="welcome-section">              
<?php
include("msg_bemvindo.php");
?>
</div>               


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
                     <?php echo htmlspecialchars($ne, ENT_QUOTES, 'UTF-8'); ?>
                  </span>

              
               </div>
               <!-- ===== FIM CABEÇALHO ===== -->
<script language="javascript" type="text/javascript">

function showequi(sala) {

    document.frm.submit();

}

</script>

<!-- ── CSS extra: sala picker ao estilo dashboard ───────── -->
<style>
:root {
    --bg:         #f0f4fb;
    --surface:    #ffffff;
    --surface2:   #f7f9fe;
    --primary:    #4b6cb7;
    --primary-dk: #182848;
    --accent:     #507feb;
    --border:     #e3e8f4;
    --text:       #1e2a45;
    --text-muted: #7b88a0;
    --radius:     14px;
    --shadow:     0 2px 16px rgba(75,108,183,.10);
}
.sala-picker-wrap {
    background: var(--surface);
    border: 1px solid var(--border);
    border-radius: var(--radius);
    box-shadow: var(--shadow);
    margin-bottom: 22px;
    overflow: hidden;
}

/* Cabeçalho da picker */
.sala-picker-head {
    display: flex;
    align-items: center;
    gap: 12px;
    padding: 12px 16px;
    border-bottom: 1px solid var(--border);
    background: var(--surface2);
    flex-wrap: wrap;
}
.sala-picker-label {
    font-size: .82rem;
    font-weight: 700;
    color: var(--text-muted);
    display: flex;
    align-items: center;
    gap: 7px;
    white-space: nowrap;
}

/* Campo de pesquisa */
.sala-search-wrap {
    position: relative;
    flex: 1;
    min-width: 160px;
    max-width: 320px;
}
.sala-search-wrap svg {
    position: absolute;
    left: 9px;
    top: 50%;
    transform: translateY(-50%);
    color: var(--text-muted);
    pointer-events: none;
}
#sala-search {
    width: 100%;
    padding: 7px 10px 7px 30px;
    border: 1.5px solid var(--border);
    border-radius: 8px;
    font-family: inherit;
    font-size: .85rem;
    color: var(--text);
    background: var(--surface);
    transition: border .2s;
    box-sizing: border-box;
}
#sala-search:focus { outline: none; border-color: var(--accent); }

/* Contador */
.sala-count-badge {
    margin-left: auto;
    font-size: .72rem;
    font-weight: 600;
    color: var(--text-muted);
    white-space: nowrap;
}

/* Lista de salas */
.sala-list {
    max-height: 220px;
    overflow-y: auto;
    scroll-behavior: smooth;
}
.sala-list::-webkit-scrollbar { width: 5px; }
.sala-list::-webkit-scrollbar-track { background: var(--surface2); }
.sala-list::-webkit-scrollbar-thumb { background: var(--border); border-radius: 99px; }

.sala-row {
    display: flex;
    align-items: center;
    gap: 10px;
    padding: 8px 16px;
    cursor: pointer;
    border-bottom: 1px solid var(--border);
    transition: background .14s;
}
.sala-row:last-child { border-bottom: none; }
.sala-row:hover { background: #eef2fb; }
.sala-row.selected {
    background: linear-gradient(90deg,#eaf0ff,#f4f7ff);
    border-left: 3px solid var(--primary);
    padding-left: 13px;
}
.sala-row.hidden { display: none; }

.sala-row-icon {
    width: 26px; height: 26px;
    border-radius: 7px;
    background: var(--border);
    color: var(--primary);
    display: flex; align-items: center; justify-content: center;
    flex-shrink: 0;
    transition: background .14s, color .14s;
}
.sala-row.selected .sala-row-icon { background: var(--primary); color: #fff; }

.sala-row-name {
    font-size: .84rem;
    font-weight: 600;
    color: var(--text);
    flex: 1;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}
.sala-row.selected .sala-row-name { color: var(--primary-dk); }

.sala-row-badge {
    font-size: .65rem;
    font-weight: 600;
    padding: 2px 7px;
    border-radius: 20px;
    background: #e8f0fe;
    color: var(--primary);
    flex-shrink: 0;
}

.sala-no-results {
    padding: 22px 16px;
    text-align: center;
    color: var(--text-muted);
    font-size: .83rem;
    display: none;
}

/* Rodapé: sala seleccionada */
.sala-picker-foot {
    display: flex;
    align-items: center;
    gap: 10px;
    padding: 9px 16px;
    border-top: 1px solid var(--border);
    background: var(--surface2);
    font-size: .78rem;
    color: var(--text-muted);
    min-height: 38px;
}
.sala-picker-foot-sel {
    font-weight: 700;
    color: var(--primary-dk);
}

#sala-hidden { display: none; }
</style>

<div class="form-container">

    <div class="step-indicator">
        <i class="fas fa-info-circle mr-2"></i>
        Complete todos os campos obrigatórios (indicados com fundo azul claro)
    </div>

    <?php
    $sql = $db->prepare("SELECT DISTINCT s.nome as no, s.id,
        (SELECT COUNT(*) FROM equipamento eq2 WHERE eq2.id_sala = s.id) as n_equip
    FROM salas s
    INNER JOIN equipamento eq ON eq.id_sala = s.id
    WHERE s.id_escola = ?
    ORDER BY s.nome");
    $sql->bind_param("i", $idescola);
    $sql->execute();
    $result    = $sql->get_result();
    $rowcount  = mysqli_num_rows($result);
    $salas_arr = [];
    while ($row = mysqli_fetch_assoc($result)) { $salas_arr[] = $row; }
    $sala_sel     = isset($_REQUEST["sala"]) ? (int)$_REQUEST["sala"] : 0;
    $sala_sel_nome = '';
    foreach ($salas_arr as $s) {
        if ((int)$s['id'] === $sala_sel) { $sala_sel_nome = $s['no']; break; }
    }
    ?>

    <form name="frm" id="frm" action="" method="post">
        <input type="hidden" name="sala" id="sala-hidden" value="<?php echo $sala_sel; ?>">

        <div class="sala-picker-wrap">

            <!-- Cabeçalho -->
            <div class="sala-picker-head">
                <span class="sala-picker-label">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                         stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                        <rect x="3" y="3" width="18" height="18" rx="2"/>
                        <path d="M3 9h18M9 21V9"/>
                    </svg>
                    Sala:
                </span>

                <?php if ($rowcount > 0): ?>
                <div class="sala-search-wrap">
                    <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                         stroke-width="2.3" stroke-linecap="round" stroke-linejoin="round">
                        <circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/>
                    </svg>
                    <input type="text" id="sala-search" placeholder="Pesquisar sala…" autocomplete="off">
                </div>
                <span class="sala-count-badge" id="sala-count"><?php echo $rowcount; ?> salas</span>
                <?php else: ?>
                <span style="font-size:.82rem;color:#b07d00;font-weight:600;">
                    Nenhuma sala com equipamentos disponíveis.
                </span>
                <?php endif; ?>
            </div>

            <?php if ($rowcount > 0): ?>
            <!-- Lista -->
            <div class="sala-list" id="sala-list">
                <div class="sala-no-results" id="sala-no-results">Nenhuma sala encontrada.</div>
                <?php foreach ($salas_arr as $s):
                    $isSel = ((int)$s['id'] === $sala_sel);
                ?>
                <div class="sala-row <?php echo $isSel ? 'selected' : ''; ?>"
                     data-id="<?php echo (int)$s['id']; ?>"
                     data-nome="<?php echo htmlspecialchars(mb_strtolower($s['no'], 'UTF-8'), ENT_QUOTES, 'UTF-8'); ?>"
                     data-label="<?php echo htmlspecialchars($s['no'], ENT_QUOTES, 'UTF-8'); ?>"
                     onclick="selecionarSala(this)">
                    <div class="sala-row-icon">
                        <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                             stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                            <rect x="3" y="3" width="18" height="18" rx="2"/>
                            <path d="M3 9h18M9 21V9"/>
                        </svg>
                    </div>
                    <span class="sala-row-name"><?php echo htmlspecialchars($s['no'], ENT_QUOTES, 'UTF-8'); ?></span>
                    <span class="sala-row-badge"><?php echo (int)$s['n_equip']; ?>&nbsp;equip.</span>
                </div>
                <?php endforeach; ?>
            </div>

            <!-- Rodapé: feedback da selecção -->
            <div class="sala-picker-foot" id="sala-foot">
                <?php if ($sala_sel > 0): ?>
                <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="var(--primary)"
                     stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                    <polyline points="20 6 9 17 4 12"/>
                </svg>
                <span>Selecionada: <span class="sala-picker-foot-sel" id="sala-foot-nome">
                    <?php echo htmlspecialchars($sala_sel_nome, ENT_QUOTES, 'UTF-8'); ?>
                </span></span>
                <?php else: ?>
                <span id="sala-foot-placeholder">Clique numa sala para selecionar</span>
                <?php endif; ?>
            </div>
            <?php endif; ?>
        </div>

        <div id="sala-error" style="display:none;color:#c0392b;font-size:.82rem;margin:-10px 0 12px 2px;">
            <i class="fa-solid fa-circle-exclamation"></i> Selecione uma sala antes de continuar.
        </div>

        <script>
        (function() {
            var selectedRow = document.querySelector('.sala-row.selected');

            // Scroll automático para a sala já selecionada
            if (selectedRow) {
                var list = document.getElementById('sala-list');
                list.scrollTop = selectedRow.offsetTop - list.offsetTop - 60;
            }

            // Auto-submit quando sala vem pré-selecionada via GET (ex: dashboard_sala)
            <?php if (!empty($_GET['sala']) && empty($_POST['sala'])): ?>
            if (selectedRow) {
                document.getElementById('frm').submit();
            }
            <?php endif; ?>

            window.selecionarSala = function(row) {
                document.querySelectorAll('.sala-row.selected').forEach(function(r) {
                    r.classList.remove('selected');
                });
                row.classList.add('selected');

                var id    = row.dataset.id;
                var label = row.dataset.label;
                document.getElementById('sala-hidden').value = id;
                document.getElementById('sala-error').style.display = 'none';

                // Atualizar rodapé
                var foot = document.getElementById('sala-foot');
                foot.innerHTML =
                    '<svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="var(--primary)" ' +
                    'stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">' +
                    '<polyline points="20 6 9 17 4 12"/></svg>' +
                    '<span>Selecionada: <span class="sala-picker-foot-sel">' + label + '</span></span>';

                document.getElementById('frm').submit();
            };

            // Pesquisa ao vivo
            var inp = document.getElementById('sala-search');
            if (inp) {
                inp.addEventListener('input', function() {
                    var q       = this.value.toLowerCase().trim();
                    var rows    = document.querySelectorAll('.sala-row');
                    var visible = 0;
                    rows.forEach(function(r) {
                        var match = r.dataset.nome.indexOf(q) !== -1;
                        r.classList.toggle('hidden', !match);
                        if (match) visible++;
                    });
                    document.getElementById('sala-no-results').style.display =
                        visible === 0 ? 'block' : 'none';
                    document.getElementById('sala-count').textContent =
                        visible + ' sala' + (visible !== 1 ? 's' : '');
                });
                inp.focus();
            }

            // Validar antes de submit
            document.getElementById('frm').addEventListener('submit', function(e) {
                var v = document.getElementById('sala-hidden').value;
                if (!v || v === '0') {
                    e.preventDefault();
                    document.getElementById('sala-error').style.display = 'block';
                }
            });
        })();
        </script>

    </form>


<?php 
                
               
               
                //$sa=$_POST["sala"];
           
          
              if (!empty($_POST["sala"])) {
              
              $sa=$_POST["sala"];
              
              }
              else{
               $sa=" ";
              }
             
  
         

              // $sa=$_POST["sala"];
               //$em=$_SESSION['email'];
             

           
               

               ?>

        

<script type="text/javascript">
function validateImage() {
    var formData = new FormData();
 
    var file = document.getElementById("img").files[0];
 
    formData.append("Filedata", file);
    var t = file.type.split('/').pop().toLowerCase();
    if (t != "jpeg" && t != "jpg" && t != "png" && t != "bmp" && t != "gif") {
       // alert('Inserir um tipo de ficheiro válido.');
        
             
       swal({
       title: 'Inserir um tipo de ficheiro válido!',
       text: 'tipo: JPEG, JPG, PNG, BMP ou GIF',
       icon: 'error',
       //buttons: false,    
       //position: 'top-rigth',
       
       })
     
       ;
       
        
        
        document.getElementById("img").value = '';
        return false;
    }
  /*  if (file.size > 1024000) {
        alert('Max Upload size is 1MB only');
        document.getElementById("img").value = '';
        return false;
    }*/
    return true;
}
</script>

<script type="text/javascript">

function Filevalidation () {
        const fi = document.getElementById('file').files[0];;
   
   //alert(fi.size);
                const fsize = fi.size;

                const file = Math.round((fsize / 1024));
         
                var fileIsMp4 = (fi.type === "video/mp4");
 
                // alert(fileIsMp4);    

                if (file >= 3000 || !fileIsMp4) {
                    //alert("O vídeo deve ter menos de 3Mb!");
                       

                    swal({
       title: 'Tamanho máximo de 3Mb!',
       text: 'Tipo MP4',
       icon: 'error',
        
       })   
       ;

                      document.getElementById("file").value = '';
                      return false;

        
                } 
            
                    return true;
             
   
   
   
    }

</script>

<!--

<br>

<form name="frm" id="frm" action = "" method = "post" >
      

      <label>Sala: </label>  
      &nbsp; 
      <?php

$sql = "SELECT DISTINCT(nome) as no,s.id 
FROM salas s, equipamento eq
where s.id_escola=$idescola and s.id=eq.id_sala
order by s.nome";

$result = mysqli_query($db,$sql);
?>

<select required style="background-color:#CEF6CE"  name="sala" id="sala" onChange="showequi(this.value);">

<?php

echo('<option value=""> Escolha a sala   </option>');  

while($row=mysqli_fetch_array($result))
{

if ($row['id']==$_REQUEST["sala"])
{
/*echo('<option selected value="'.$row['no'].'">'.$row['no'].'</option>');*/
//echo('<option selected value="'.$_REQUEST["sala"].'">'.$row['no'].'</option>');

echo '<option selected value="' . htmlspecialchars($_REQUEST['sala'], ENT_QUOTES, 'UTF-8') . '">'
     . htmlspecialchars($row['no'], ENT_QUOTES, 'UTF-8') . '</option>';

}
else

echo('<option value="'.$row['id'].'">'.$row['no'].'</option>');

}

echo('</select>');
?>     

  <br>   <br>

  </form>

-->

         <!--
            sa=<php echo ($sa);?>
            -->

            <?php

            if($_SERVER["REQUEST_METHOD"] == "POST") {

               
               
               ?>

             

              <form name="avaria" action="<?php echo SVRURL ?>grava_avaria.php?ai=<?php echo base64_encode($sa);?>&&esi=<?php echo  base64_encode($idescola);?>"   
               method = "post" enctype="multipart/form-data" 
               class="needs-validation" novalidate>
                <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($csrf_token, ENT_QUOTES, 'UTF-8'); ?>">

                   <?php
                  // if ($sa<>" ")
                   //{
                   ?>

              <?php if (isset($_SESSION['tipo']) && ($_SESSION['tipo'] == 1 || $_SESSION['tipo'] == 3)): ?>
              <div style="display:flex;align-items:center;gap:8px;margin:10px 0 18px;padding:10px 14px;background:#f4f6fb;border:1px solid #e3e8f4;border-radius:8px;font-size:.82rem;color:#4b6cb7;font-weight:600;">
                  <img src="<?php echo SVRURL ?>images/informacao.svg" alt="Informação" style="width:18px;height:18px;flex-shrink:0;">
                  <label style="display:flex;align-items:center;gap:6px;margin:0;cursor:pointer;font-weight:600;color:#4b6cb7;">
                      <input type="checkbox" name="notif_admin" value="yes" style="width:16px;height:16px;cursor:pointer;">
                      Enviar email de notificação ao administrador/reparador
                  </label>
              </div>
              <?php else: ?>
              <div style="display:flex;align-items:center;gap:8px;margin:10px 0 18px;padding:10px 14px;background:#f4f6fb;border:1px solid #e3e8f4;border-radius:8px;font-size:.82rem;color:#4b6cb7;font-weight:600;">
                  <img src="<?php echo SVRURL ?>images/informacao.svg" alt="Informação" style="width:18px;height:18px;flex-shrink:0;">
                  Após submeter a avaria, receberá um email com os dados da avaria registada.
              </div>
              <?php endif; ?>

              <br>
                   <label>Equipamento:</label>
                  <br>

               <?php
               // Todos os equipamentos da sala, com flag se estão avariados (sem data de reparação)
               $sa_int = (int)$sa;
               $stmt_eq = $db->prepare("SELECT e.id, e.nomeequi,
                           EXISTS (
                               SELECT 1 FROM avarias_reparacoes ar
                               WHERE ar.id_equi = e.id AND ar.datareparacao IS NULL
                           ) AS avariado
                       FROM equipamento e
                       WHERE e.id_sala = ?
                       ORDER BY e.nomeequi");
               $stmt_eq->bind_param("i", $sa_int);
               $stmt_eq->execute();
               $result      = $stmt_eq->get_result();
               $stmt_eq->close();
               $num_equip   = mysqli_num_rows($result);
               $equip_rows  = [];
               while ($row = mysqli_fetch_assoc($result)) { $equip_rows[] = $row; }

               $num_operacionais = count(array_filter($equip_rows, function($r) { return !$r['avariado']; }));

               if ($num_equip == 0 && $sa != " "): ?>
               <div style="display:flex;align-items:center;gap:8px;padding:10px 14px;background:#fff8e1;border:1px solid #ffe082;border-radius:8px;font-size:.82rem;color:#b07d00;font-weight:600;margin-bottom:10px;">
                   <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#b07d00" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" style="flex-shrink:0;"><path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"/><line x1="12" y1="9" x2="12" y2="13"/><line x1="12" y1="17" x2="12.01" y2="17"/></svg>
                   Esta sala não tem equipamentos definidos.
               </div>
               <?php else: ?>

               <?php if ($num_operacionais === 0): ?>
               <div style="display:flex;align-items:center;gap:8px;padding:10px 14px;background:#fff8e1;border:1px solid #ffe082;border-radius:8px;font-size:.82rem;color:#b07d00;font-weight:600;margin-bottom:10px;">
                   <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#b07d00" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" style="flex-shrink:0;"><path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"/><line x1="12" y1="9" x2="12" y2="13"/><line x1="12" y1="17" x2="12.01" y2="17"/></svg>
                   Todos os equipamentos desta sala estão em reparação.
               </div>
               <?php endif; ?>

               <div id="equip-list" style="border:1px solid #c9d4e8;border-radius:8px;padding:10px 14px;background:#f8faff;max-height:260px;overflow-y:auto;">
                   <?php if (count($equip_rows) > 0): ?>

                   <!-- Selecionar todos (só operacionais) -->
                   <?php if ($num_operacionais > 0): ?>
                   <div style="margin-bottom:8px;padding-bottom:7px;border-bottom:1px solid #e3e8f4;display:flex;align-items:center;justify-content:space-between;">
                       <label style="display:flex;align-items:center;gap:8px;font-weight:700;color:#4b6cb7;cursor:pointer;font-size:.82rem;">
                           <input type="checkbox" id="selecionar_todos" style="width:15px;height:15px;cursor:pointer;"
                               onchange="toggleTodosEquip(this)">
                           Selecionar todos os operacionais
                       </label>
                       <span style="font-size:.72rem;color:#7b88a0;font-weight:600;">
                           <?php echo $num_operacionais; ?> operacional<?php echo $num_operacionais != 1 ? 'is' : ''; ?>
                           &nbsp;·&nbsp;
                           <?php echo count($equip_rows) - $num_operacionais; ?> avariado<?php echo (count($equip_rows) - $num_operacionais) != 1 ? 's' : ''; ?>
                       </span>
                   </div>
                   <?php endif; ?>

                   <?php foreach ($equip_rows as $row):
                       $avariado = (bool)$row['avariado'];
                   ?>
                   <?php if ($avariado): ?>
                   <!-- Equipamento avariado: visível mas não selecionável -->
                   <div style="display:flex;align-items:center;gap:8px;padding:6px 4px;border-radius:5px;opacity:.72;cursor:not-allowed;"
                        title="Este equipamento já tem uma avaria em aberto">
                       <div style="width:15px;height:15px;flex-shrink:0;border-radius:3px;border:1.5px solid #e0a800;background:#fff8e1;display:flex;align-items:center;justify-content:center;">
                           <svg width="9" height="9" viewBox="0 0 24 24" fill="none" stroke="#b07d00" stroke-width="3" stroke-linecap="round" stroke-linejoin="round">
                               <line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/>
                           </svg>
                       </div>
                       <span style="font-size:.86rem;color:#7b88a0;text-decoration:line-through;flex:1;">
                           <?php echo htmlspecialchars($row['nomeequi'], ENT_QUOTES, 'UTF-8'); ?>
                       </span>
                       <span style="font-size:.68rem;font-weight:700;padding:2px 7px;border-radius:20px;background:#fff0cd;color:#b07d00;white-space:nowrap;flex-shrink:0;">
                           ⚠ Em reparação
                       </span>
                   </div>
                   <?php else: ?>
                   <!-- Equipamento operacional: selecionável -->
                   <label style="display:flex;align-items:center;gap:8px;padding:6px 4px;cursor:pointer;border-radius:5px;transition:background .15s;"
                          onmouseover="this.style.background='#eef2fb'" onmouseout="this.style.background=''">
                       <input type="checkbox" name="equip[]" value="<?php echo (int)$row['id']; ?>"
                              class="equip-check" style="width:15px;height:15px;cursor:pointer;">
                       <span style="font-size:.86rem;color:#1e2a45;flex:1;">
                           <?php echo htmlspecialchars($row['nomeequi'], ENT_QUOTES, 'UTF-8'); ?>
                       </span>
                       <span style="font-size:.68rem;font-weight:700;padding:2px 7px;border-radius:20px;background:#e0f7f0;color:#13a073;white-space:nowrap;flex-shrink:0;">
                           ✓ Operacional
                       </span>
                   </label>
                   <?php endif; ?>
                   <?php endforeach; ?>

                   <?php endif; ?>
               </div>
               <div id="equip-error" style="display:none;color:#c0392b;font-size:.82rem;margin-top:5px;">
                   <i class="fa-solid fa-circle-exclamation"></i> Selecione pelo menos um equipamento.
               </div>
               <?php endif;

               mysqli_close($db); ?>
               
            

               <?php if ($num_operacionais > 0): ?>
                   <br />
                   <br>
                    <label>Data: </label>  
                    <input class="form-control required-field" required  value="<?php echo date("Y-m-d"); ?>"            
                    size="10" type = "date" name = "data" id="data_input" >
                  
                   <br />
                   <br>
                   <!-- ── Label com tooltip de ajuda ── -->
                   <label style="display:inline-flex;align-items:center;margin-bottom:6px;">
                       Avaria (descrição):
                       <button type="button" class="gei-help"
                           data-tip="Descreva o que está a acontecer: quando começou, o que o utilizador tentou fazer e se o problema acontece sempre ou só às vezes. Quanto mais detalhe, mais rápida será a reparação.">?</button>
                   </label>
                   <br>

                   <!-- ── Templates rápidos de descrição ── -->
                   <div class="tmpl-section">
                       <span class="tmpl-section-label">💡 Escolha um tipo de problema para pré-preencher a descrição:</span>
                       <div class="tmpl-btns">
                           <button type="button" class="tmpl-btn" data-tmpl="O equipamento não liga. Verificado: cabo de alimentação e botão de ligar. O equipamento não apresenta qualquer sinal de vida (sem luzes, sem imagem).">🔌 Não liga</button>
                           <button type="button" class="tmpl-btn" data-tmpl="Problema no ecrã. Sintoma: [sem imagem / imagem distorcida / linhas no ecrã / ecrã partido]. Cabo de vídeo verificado: [Sim / Não].">🖥️ Ecrã</button>
                           <button type="button" class="tmpl-btn" data-tmpl="O equipamento liga mas está muito lento ou bloqueia com frequência. Sintoma observado: [demora a arrancar / bloqueia durante a aula / reinicia sozinho]. Sistema operativo: Windows.">🐌 Lento / Bloqueia</button>
                           <button type="button" class="tmpl-btn" data-tmpl="Periférico com problema. Periférico afetado: [rato / teclado / colunas / impressora]. Sintoma: não é reconhecido ou não funciona. Testado noutro equipamento: [Sim / Não].">🖱️ Periférico</button>
                           <button type="button" class="tmpl-btn" data-tmpl="Sem acesso à internet ou à rede local. Outros equipamentos da mesma sala com o mesmo problema: [Sim / Não]. Cabo de rede verificado: [Sim / Não]. Wi-Fi ou cabo: [Wi-Fi / Cabo].">🌐 Sem rede</button>
                           <button type="button" class="tmpl-btn" data-tmpl="Problema com software ou aplicação. Aplicação afetada: [nome da aplicação]. Mensagem de erro apresentada: [descrever ou fotografar]. O problema acontece sempre ou só às vezes: [sempre / às vezes].">💾 Software</button>
                           <button type="button" class="tmpl-btn" data-tmpl="Dano físico no equipamento. Parte afetada: [ecrã / carcaça / teclado / outro]. Causa conhecida: [queda / vandalismo / desgaste / desconhecida]. O equipamento ainda funciona: [Sim / Não].">🔨 Dano físico</button>
                       </div>
                   </div>

                   <!-- ── Textarea com feedback de qualidade ── -->
                   <textarea class="form-control required-field"
                       placeholder="Descreva a avaria com o máximo de detalhe possível: o que acontece, quando começou, o que já foi tentado..."
                       required rows="4" cols="60"
                       name="avaria" id="avaria_txt"
                       oninput="geiAvaliarDescricao(this)"></textarea>

                   <!-- Contador de caracteres + indicador de qualidade -->
                   <div class="desc-meta">
                       <div id="desc-qualidade" class="desc-qualidade"></div>
                       <div id="desc-contador" class="desc-contador">0 caracteres</div>
                   </div>

                  <br><br>

                  <div class="upload-section">
                <label class="upload-label" style="display:inline-flex;align-items:center;">
                    Imagem:
                    <button type="button" class="gei-help"
                        data-tip="Tire uma foto ao ecrã de erro ou ao equipamento danificado. Ajuda o técnico a diagnosticar sem se deslocar. Formatos aceites: JPEG, PNG, GIF, BMP.">?</button>
                </label>
                <div class="file-input-container">
                    <button type="button" class="file-input-button">Escolher ficheiro</button>
                    <input type="file" class="file-input" name = "imgavaria" id="img" onChange="validateImage()"  accept="image/jpeg,image/jpg,image/png,image/gif,image/bmp">
                </div>
                <div class="file-input-status">Nenhum ficheiro selecionado</div>
                <div class="file-type-info">Formatos aceites: JPEG, JPG, PNG, GIF, BMP</div>
                <!-- Miniatura de pré-visualização -->
                <img id="img_preview" src="" alt="Pré-visualização"
                     style="display:none;margin-top:10px;max-width:200px;max-height:150px;border-radius:6px;border:1px solid #e3e8f4;">
            </div>
            <br>

            <div class="upload-section">
                <label class="upload-label" style="display:inline-flex;align-items:center;">
                    Vídeo:
                    <button type="button" class="gei-help"
                        data-tip="Grave um pequeno vídeo a mostrar o problema. Muito útil para erros intermitentes ou sons estranhos. Formato MP4, máximo 3MB.">?</button>
                </label>
                <div class="file-input-container">
                    <button type="button" class="file-input-button">Escolher ficheiro</button>
                    <input type="file" class="file-input" name="v" id="file" onChange="return Filevalidation();" accept="video/mp4">
                </div>
                <div class="file-input-status">Nenhum ficheiro selecionado</div>
                <div class="file-type-info">Formato aceite: MP4</div>
                <div class="max-size-info">Tamanho máximo: 3MB</div>
            </div>

<!--
                   <br />
                   <br>
                   <label>Avaria (imagem: JPEG, JPG, PNG, GIF, BMP): </label>  <br>  
                   <input accept="image/png, image/gif, image/jpeg, image/jpg, image/bmp"  size=50 type="file" name = "imgavaria" id="img" onChange="validateImage()" />
                   <br /><br />

                   <label>Avaria (vídeo tamanho máximo 3Mb, tipo MP4): </label>  <br>  
                   <input accept="video/mp4" size=50 type="file" name="v" id="file" onChange="return Filevalidation();">
                   
     

                   <br /><br />
                                   
                   <div  style=" text-align:center;width:100%"> <input  type = "submit" value = "Inserir"/>   
    </div>   --> 

                           <div class="text-center mt-4" style="display:flex;gap:10px;justify-content:center;flex-wrap:wrap;">

                           <!-- Checklist pré-submit -->
                           <details class="checklist-wrap">
                               <summary class="checklist-summary">
                                   Verificou estes pontos antes de submeter?
                               </summary>
                               <div class="checklist-body">
                                   <label class="checklist-item">
                                       <input type="checkbox">
                                       <span>O equipamento está <strong>ligado à corrente</strong> e o cabo está bem inserido?</span>
                                   </label>
                                   <label class="checklist-item">
                                       <input type="checkbox">
                                       <span>Tentou <strong>reiniciar o equipamento</strong>?</span>
                                   </label>
                                   <label class="checklist-item">
                                       <input type="checkbox">
                                       <span>O problema acontece <strong>só neste equipamento</strong> ou em vários da sala?</span>
                                   </label>
                                   <label class="checklist-item">
                                       <input type="checkbox">
                                       <span>Adicionou uma <strong>fotografia ou vídeo</strong> do problema para ajudar o técnico?</span>
                                   </label>
                                   <label class="checklist-item">
                                       <input type="checkbox">
                                       <span>A descrição inclui <strong>quando o problema começou</strong> e o que já foi tentado?</span>
                                   </label>
                                   <p class="checklist-note">Estas verificações não são obrigatórias, mas ajudam a resolver a avaria mais depressa.</p>
                               </div>
                           </details>

                           <!-- Botão Inserir -->
                                    <button type="submit" class="btn-submit">
                                        <i class="fa-solid fa-circle-check" style="margin-right:6px;"></i>
                                        &nbsp;Inserir Avaria
                                    </button>

                                    <!-- Botão WhatsApp 
                                    <button type="button" onclick="enviarWhatsApp()"
                                        style="background:#25D366;color:white;border:none;padding:10px 20px;border-radius:8px;font-size:15px;font-weight:600;cursor:pointer;display:flex;align-items:center;gap:8px;">
                                        📲 PTE
                                    </button>
-->
                                </div>

               <?php endif; ?>

                </form>

<?php

         }
?>

<script>
        // Script para atualizar o texto de status quando um arquivo é selecionado
        document.querySelectorAll('.file-input').forEach(input => {
            input.addEventListener('change', function() {
                const status = this.parentElement.nextElementSibling;
                if (this.files.length > 0) {
                    status.textContent = this.files[0].name;
                } else {
                    status.textContent = 'Nenhum ficheiro selecionado';
                }
            });
        });
        
        // Script para fazer o botão personalizado funcionar como o input file
        document.querySelectorAll('.file-input-button').forEach(button => {
            button.addEventListener('click', function() {
                this.nextElementSibling.click();
            });
        });
    </script>

                    </div>
               


<!--

<div class="text-center mt-3">
                            <a class="btn btn-secondary" title="Voltar" href="<?php echo SVRURL ?>avaria">
                                <i class="bi bi-arrow-left"></i> Voltar
                            </a>
                            <br><br>

                     
                            <div style="text-align:center;padding:10px 14px;background:#f4f6fb;border:1px solid #e3e8f4;border-radius:8px;font-size:.82rem;color:#4b6cb7;">
                                <span style="font-weight:600;">Ainda não és membro do grupo?</span><br>
                                <a href="https://chat.whatsapp.com/JK5mB2Xpv1UFxlVdDKhUCH" target="_blank"
                                   style="display:inline-flex;align-items:center;gap:6px;margin-top:8px;padding:7px 16px;background:#25D366;color:white;border-radius:7px;text-decoration:none;font-weight:700;font-size:.85rem;">
                                    📲 Aderir ao grupo do reporte de avarias
                                </a>
                            </div>
                            <br>
                        </div>
-->


<a href="<?php echo SVRURL ?>avaria">
    <img src="<?php echo SVRURL ?>images/voltar.svg" alt="Voltar">
</a>
<br><br>

               </div>
            </div>
         </div>
      </div>
      <!-- end about -->
    
    <!-- Script para validação do formulário -->
    <script>
        // Example starter JavaScript for disabling form submissions if there are invalid fields
        (function() {
            'use strict';
            window.addEventListener('load', function() {
                var forms = document.getElementsByClassName('needs-validation');
                var validation = Array.prototype.filter.call(forms, function(form) {
                    form.addEventListener('submit', function(event) {
                        // Validate equipment checkboxes
                        var checks = document.querySelectorAll('.equip-check');
                        var equip_error = document.getElementById('equip-error');
                        if (checks.length > 0) {
                            var anyChecked = Array.from(checks).some(function(c) { return c.checked; });
                            if (!anyChecked) {
                                event.preventDefault();
                                event.stopPropagation();
                                if (equip_error) equip_error.style.display = 'block';
                                return;
                            } else {
                                if (equip_error) equip_error.style.display = 'none';
                            }
                        }
                        if (form.checkValidity() === false) {
                            event.preventDefault();
                            event.stopPropagation();
                        }
                        form.classList.add('was-validated');
                    }, false);
                });
            }, false);
        })();

        function toggleTodosEquip(master) {
            document.querySelectorAll('.equip-check').forEach(function(c) {
                c.checked = master.checked;
            });
        }

        // Keep "Selecionar todos" in sync when individual boxes change
        document.addEventListener('change', function(e) {
            if (e.target && e.target.classList.contains('equip-check')) {
                var all  = document.querySelectorAll('.equip-check');
                var allC = Array.from(all).every(function(c) { return c.checked; });
                var master = document.getElementById('selecionar_todos');
                if (master) master.checked = allC;
                // Hide error if at least one is checked
                var anyC = Array.from(all).some(function(c) { return c.checked; });
                var equip_error = document.getElementById('equip-error');
                if (equip_error) equip_error.style.display = anyC ? 'none' : 'block';
            }
        });
    </script>

      <?php 
           
         
      
      include ("footer.php");?>









<script>

// Pré-visualização da imagem selecionada
var imgBase64 = "";
document.addEventListener("DOMContentLoaded", function() {
    var imgInput = document.getElementById("img");
    if (imgInput) {
        imgInput.addEventListener("change", function() {
            var file = this.files[0];
            if (file) {
                var reader = new FileReader();
                reader.onload = function(e) {
                    imgBase64 = e.target.result; // data:image/jpeg;base64,...
                    // Mostrar miniatura
                    var preview = document.getElementById("img_preview");
                    if (preview) {
                        preview.src = imgBase64;
                        preview.style.display = "block";
                    }
                };
                reader.readAsDataURL(file);
            } else {
                imgBase64 = "";
                var preview = document.getElementById("img_preview");
                if (preview) preview.style.display = "none";
            }
        });
    }
});

function enviarWhatsApp(){

    var escola      = "<?php echo addslashes(htmlspecialchars_decode($ne)); ?>";
    var utilizador  = "<?php echo addslashes($_SESSION['nome'] ?? ($_SESSION['login_user'] ?? '')); ?>";
    var email       = "<?php echo addslashes($_SESSION['email'] ?? ''); ?>";

    // Sala
    var salaEl      = document.getElementById("sala");
    var sala        = (salaEl && salaEl.selectedIndex > 0)
                      ? salaEl.options[salaEl.selectedIndex].text : "-";

    // Equipamento
    var equipEl     = document.getElementById("equip");
    var equipamento = (equipEl && equipEl.selectedIndex >= 0)
                      ? equipEl.options[equipEl.selectedIndex].text : "-";

    // Descricao
    var descEl      = document.getElementById("avaria_txt");
    var descricao   = descEl ? descEl.value.trim() : "";

    // Data
    var dataEl      = document.getElementById("data_input");
    var dataVal     = dataEl ? dataEl.value : "";
    var dataFmt     = dataVal
                      ? new Date(dataVal + "T12:00:00").toLocaleDateString('pt-PT')
                      : new Date().toLocaleDateString('pt-PT');

    // Imagem
    var temImagem   = imgBase64 !== "";

    if (!descricao) {
        alert("Preenche a descricao da avaria antes de enviar para o WhatsApp.");
        return;
    }

    var mensagem =
        "*Reporte de Avaria*\n\n" +
        "*Escola:* "        + escola      + "\n" +
        "*Sala:* "          + sala        + "\n" +
        "*Equipamento:* "   + equipamento + "\n" +
        "*Data:* "          + dataFmt     + "\n" +
        "*Descricao:* "     + descricao   + "\n\n" +
        "*Registado por:* " + utilizador  + "\n" +
        "*Email:* "         + email;

    var waUrl = "https://wa.me/?text=" + encodeURIComponent(mensagem);
    window.open(waUrl, "_blank");
}

</script>



   </body>

<script>
/* ══════════════════════════════════════════════
   GEI — Sistema de Ajuda ao Preenchimento
   ══════════════════════════════════════════════ */

/* ── 1. Tooltips: toggle no mobile ao click ── */
document.querySelectorAll('.gei-help').forEach(function(btn) {
    btn.addEventListener('click', function(e) {
        e.preventDefault();
        e.stopPropagation();
        var isActive = this.classList.contains('active');
        // fechar todos os outros
        document.querySelectorAll('.gei-help.active').forEach(function(b) { b.classList.remove('active'); });
        if (!isActive) this.classList.add('active');
    });
});
document.addEventListener('click', function() {
    document.querySelectorAll('.gei-help.active').forEach(function(b) { b.classList.remove('active'); });
});

/* ── 2. Templates de descrição ── */
document.querySelectorAll('.tmpl-btn').forEach(function(btn) {
    btn.addEventListener('click', function() {
        var txt = document.getElementById('avaria_txt');
        if (!txt) return;

        // toggle: se já está ativo, limpa o campo
        var jaAtivo = this.classList.contains('active');
        document.querySelectorAll('.tmpl-btn.active').forEach(function(b) { b.classList.remove('active'); });

        if (jaAtivo) {
            txt.value = '';
        } else {
            this.classList.add('active');
            txt.value = this.dataset.tmpl;
            txt.focus();
            // selecionar tudo para o utilizador editar facilmente
            txt.select();
        }
        // atualizar o indicador de qualidade
        geiAvaliarDescricao(txt);
    });
});

/* ── 3. Indicador de qualidade da descrição ── */
function geiAvaliarDescricao(el) {
    var txt = el.value.trim();
    var n   = txt.length;
    var qi  = document.getElementById('desc-qualidade');
    var ci  = document.getElementById('desc-contador');
    if (!qi || !ci) return;

    ci.textContent = n + ' caracter' + (n !== 1 ? 'es' : '');

    if (n === 0) {
        qi.innerHTML = '';
    } else if (n < 20) {
        qi.innerHTML = '<span style="color:#c0392b;">⚠ Muito curta — adicione mais detalhes</span>';
    } else if (n < 60) {
        qi.innerHTML = '<span style="color:#e67e22;">📝 Razoável — quando começou o problema?</span>';
    } else if (n < 120) {
        qi.innerHTML = '<span style="color:#2980b9;">👍 Boa — considere adicionar o que já tentou</span>';
    } else {
        qi.innerHTML = '<span style="color:#27ae60;">✓ Descrição completa</span>';
    }

    // se o utilizador editou o texto, desativar o botão de template ativo
    var activeBtn = document.querySelector('.tmpl-btn.active');
    if (activeBtn && txt !== activeBtn.dataset.tmpl.trim()) {
        activeBtn.classList.remove('active');
    }
}
</script>
</html>