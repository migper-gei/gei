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
<!DOCTYPE html>
<html lang="pt">
   <head>
      

<?php

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

//include("verifica_sessao.php");


 
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
                        <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="#4b6cb7" stroke-width="2.3" stroke-linecap="round" stroke-linejoin="round"><rect x="2" y="7" width="20" height="14" rx="2"/><path d="M16 7V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v2"/></svg>
                        <a href="<?php echo SVRURL ?>configura" style="color:#4b6cb7;text-decoration:none;">Configurações</a>
                     </li>
                     <li style="color:#c5cde0;font-size:.9rem;">&#8250;</li>
                     <li style="color:#1e2a45;">Períodos</li>
                  </ol>
               </nav>
               <div class="titlepage">
          
                  </div>
               </div>
            </div>
            
            <div class="container">
               <div class="row">
                  <div class="col-md-10 offset-md-2">
              
            
                  <div class="welcome-section">   
<?php
include("msg_bemvindo.php");
?>
    
</div>




<?php
  
// Set session
 // session_start();
  if(isset($_POST['records-limit'])){
      $_SESSION['records-limit'] = $_POST['records-limit'];
  }
  
  $limit = isset($_SESSION['records-limit']) ? $_SESSION['records-limit'] : 10;
  $page = (isset($_GET['page']) && is_numeric($_GET['page']) ) ? $_GET['page'] : 1;
  $paginationStart = ($page - 1) * $limit;


  $sql = "select * from periodos order by ano_lectivo desc, num_periodo asc LIMIT $paginationStart, $limit";
  $result = mysqli_query($db,$sql);


  // Get total records
  $sql1 = "select count(*) as cs from periodos";
  $result1 = mysqli_query($db,$sql1); 
  $rows =mysqli_fetch_row($result1);
  

  $totallinhas = $rows[0];



  
  // Calculate total pages
  $totoalPages = ceil($totallinhas / $limit);

  // Prev + Next
  $prev = $page - 1;
  $next = $page + 1;

// CORRIGIDO: Gerar token CSRF para operações de eliminação (uso único por página)
if (empty($_SESSION['csrf_token_delete'])) {
    $_SESSION['csrf_token_delete'] = bin2hex(random_bytes(32));
}
$csrf_delete = $_SESSION['csrf_token_delete'];
?>




<script>
function a(n,al,np) {

var n1;
n1=n;al1=al;np1=np

 //alert(n1);
  event.preventDefault(); // prevent form submit

   swal({
  title: "Deseja eliminar?",
  text: al1+" - "+np1+" Período de tempo",
  type: "warning",
  showCancelButton: true,
  //confirmButtonColor: "#DD6B55",
  confirmButtonText: "Sim",
  cancelButtonText: "Não",
  closeOnConfirm: false,
  closeOnCancel: false
},
function(isConfirm){
  if (isConfirm) {
    
        //alert(n1);
        // CORRIGIDO: incluir csrf_token no URL de eliminação
        window.setTimeout(function() {
    window.location.href = '<?php echo SVRURL ?>eliminaper/'+n1+'?csrf_token=<?php echo urlencode($csrf_delete); ?>';
}, 10);


          
  } else {
   
    swal("Cancelado.");
 
  

  }

});

}

</script>

<style>
.gei-table-wrap {
    background:#fff; border-radius:10px;
    box-shadow:0 2px 12px rgba(75,108,183,.10);
    border:1px solid #e3e8f4; overflow:hidden; margin-bottom:16px;
}
.gei-table-toolbar {
    display:flex; align-items:center; justify-content:space-between;
    flex-wrap:wrap; gap:10px; padding:12px 16px;
    background:#f4f6fb; border-bottom:1px solid #e3e8f4;
}
.gei-table-toolbar-left  { display:flex; align-items:center; gap:10px; flex-wrap:wrap; }
.gei-table-toolbar-right { display:flex; align-items:center; gap:8px; }
.gei-table { width:100%; border-collapse:collapse; font-size:.84rem; }
.gei-table thead th {
    padding:10px 14px; background:#182848; color:#fff;
    font-size:.75rem; font-weight:700; text-transform:uppercase;
    letter-spacing:.5px; border:none; white-space:nowrap;
}
.gei-table tbody tr { border-bottom:1px solid #eef1f8; transition:background .15s; }
.gei-table tbody tr:last-child { border-bottom:none; }
.gei-table tbody tr:hover { background:#f0f4fb; }
.gei-table tbody tr:nth-child(even) { background:#f7f9fe; }
.gei-table tbody tr:nth-child(even):hover { background:#eef2fb; }
.gei-table td { padding:10px 14px; vertical-align:middle; color:#1e2a45; }
.gei-insert-btn {
    display:inline-flex; align-items:center; gap:6px;
    padding:6px 14px; border-radius:7px; font-size:.8rem; font-weight:600;
    background:#1cc88a !important; color:#fff !important;
    text-decoration:none !important; border:none;
    box-shadow:0 2px 8px rgba(28,200,138,.25);
    transition:opacity .15s, transform .12s;
}
.gei-insert-btn:hover { opacity:.88; transform:translateY(-1px); }
.gei-action-btn {
    display:inline-flex; align-items:center; gap:5px;
    padding:4px 10px; border-radius:6px; font-size:.75rem; font-weight:600;
    text-decoration:none !important; border:none; cursor:pointer;
    transition:opacity .15s, transform .12s; white-space:nowrap;
}
.gei-action-btn:hover { opacity:.85; transform:translateY(-1px); }
.gei-btn-edit   { background:#eef2fb; color:#00509e !important; border:1.5px solid #c7d4f0; }
.gei-btn-delete { background:#fde8e6; color:#c0392b !important; border:1.5px solid #f5c0bb; }
.gei-btn-locked { background:#f4f6fb; color:#aab0bb !important; border:1.5px solid #dde2ec; cursor:not-allowed; opacity:.7; }
.gei-info-bar {
    display:inline-flex; align-items:center; gap:7px;
    background:#fff3cd; color:#7d4e00; border:1px solid #e87722;
    border-radius:7px; padding:6px 14px; font-size:.8rem; font-weight:500;
    margin-bottom:12px;
}
.gei-pagination {
    display:flex; align-items:center; justify-content:center;
    gap:4px; flex-wrap:wrap; padding:12px 0;
}
.gei-page-btn {
    display:inline-flex; align-items:center; justify-content:center;
    min-width:32px; height:32px; padding:0 10px; border-radius:6px;
    font-size:.8rem; font-weight:600; text-decoration:none !important;
    border:1.5px solid #e3e8f4; color:#4b6cb7 !important; background:#fff;
    transition:all .15s;
}
.gei-page-btn:hover { background:#eef2fb; border-color:#4b6cb7; }
.gei-page-btn.active { background:#182848; color:#fff !important; border-color:#182848; }
.gei-page-btn.disabled { opacity:.4; pointer-events:none; }
.gei-page-total { font-size:.78rem; color:#7b88a0; font-weight:600; padding:0 10px; }

/* ── Responsivo mobile ── */
@media (max-width: 768px) {
    .gei-table-wrap { border-radius:8px; }
    .gei-table thead { display:none; }
    .gei-table tbody tr {
        display:block; border:1px solid #e3e8f4; border-radius:8px;
        margin-bottom:10px; padding:10px 12px; background:#fff;
        box-shadow:0 1px 6px rgba(75,108,183,.08);
    }
    .gei-table tbody tr:nth-child(even) { background:#fff; }
    .gei-table tbody tr:hover { background:#f5f8ff; }
    .gei-table td {
        display:flex; align-items:flex-start; gap:8px;
        padding:5px 2px; border:none; font-size:.83rem;
    }
    .gei-table td::before {
        content: attr(data-label); min-width:120px;
        font-size:.72rem; font-weight:700; text-transform:uppercase;
        letter-spacing:.4px; color:#7b88a0; padding-top:2px; flex-shrink:0;
    }
    .gei-table td[data-label="Ações"] {
        flex-wrap:wrap; gap:6px; padding-top:8px;
        border-top:1px dashed #e3e8f4; margin-top:4px;
    }
    .gei-table td[data-label="Ações"]::before { display:none; }
}
</style>

<?php if ($_SESSION['tipo'] == 1): ?>
<div class="gei-info-bar">
    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="#e87722" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
        <circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/>
    </svg>
    Não é possível atualizar/eliminar períodos com avarias associadas.
</div>
<?php endif; ?>

<div class="gei-table-wrap">
    <div class="gei-table-toolbar">
        <div class="gei-table-toolbar-left">
            <form action="<?php echo SVRURL ?>peri" method="post" style="margin:0;">
                <label style="font-size:.8rem;font-weight:600;color:#7b88a0;display:flex;align-items:center;gap:6px;">
                Linhas por página:
                <select name="records-limit" onchange="this.form.submit()"
                    style="padding:5px 10px;border-radius:7px;border:1.5px solid #c7d4f0;font-size:.82rem;font-weight:600;color:#1e2a45;background:#fff;cursor:pointer;outline:none;">
                    <?php foreach([5,10,20,30,50,100] as $opt): ?>
                    <option value="<?= $opt ?>" <?= ($limit==$opt) ? 'selected' : '' ?>><?= $opt ?></option>
                    <?php endforeach; ?>
                </select>
            </label>
            </form>
           
        </div>
        <?php if ($_SESSION['tipo']==1): ?>
        <div class="gei-table-toolbar-right">
            <a href="<?php echo SVRURL ?>inserirper" class="gei-insert-btn" title="Inserir período">
                <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="#fff" stroke-width="2.5" stroke-linecap="round"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
                Novo período
            </a>
        </div>
        <?php endif; ?>
    </div>

    <table class="gei-table" id="js-sort-table">
        <thead>
            <tr>
                <th>Ano</th>
                <th>Período de tempo</th>
                <th>Data Início</th>
                <th>Data Fim</th>
                <?php if ($_SESSION['tipo']==1): ?>
                <th style="text-align:center;width:16%">Ações</th>
                <?php endif; ?>
            </tr>
        </thead>
        <tbody>
        <?php while ($row = mysqli_fetch_array($result)):
            $id = $row['id'];
            $np = $row['num_periodo'];
            $al = $row['ano_lectivo'];
            $ccav = 0;
            if ($_SESSION['tipo'] == 1) {
                $sql9 = "SELECT count(*) FROM avarias_reparacoes WHERE ano_letivo='".mysqli_real_escape_string($db,$al)."' AND periodo=".(int)$np;
                $res9 = mysqli_query($db, $sql9);
                $row9 = mysqli_fetch_row($res9);
                $ccav = (int)$row9[0];
            }
        ?>
        <tr>
            <td data-label="Ano Letivo" style="font-weight:700;"><?php echo htmlspecialchars($al, ENT_QUOTES, 'UTF-8'); ?></td>
            <td data-label="Período / Semestre">
                <span style="display:inline-flex;align-items:center;padding:2px 10px;border-radius:5px;font-size:.78rem;font-weight:700;background:#e8f0fe;color:#4b6cb7;border:1.5px solid #c7d4f0;">
                    <?php echo htmlspecialchars($np, ENT_QUOTES, 'UTF-8'); ?>
                </span>
            </td>
            <td data-label="Data Início" style="font-family:monospace;font-size:.82rem;"><?php echo date('d/m/Y', strtotime($row['data_inicio'])); ?></td>
            <td data-label="Data Fim" style="font-family:monospace;font-size:.82rem;"><?php echo date('d/m/Y', strtotime($row['data_fim'])); ?></td>
            <?php if ($_SESSION['tipo']==1): ?>
            <td data-label="Ações" style="text-align:center;white-space:nowrap;">
                <?php if ($ccav == 0): ?>
                <a class="gei-action-btn gei-btn-edit" title="Atualizar" href="atualizaper/<?php echo base64_encode($id); ?>">
                    <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
                    Editar
                </a>
                <a class="gei-action-btn gei-btn-delete" title="Eliminar"
                   onclick="a(<?php echo (int)$id;?>,'<?php echo htmlspecialchars($al,ENT_QUOTES,'UTF-8');?>',<?php echo (int)$np;?>);"
                   href="<?php echo SVRURL ?>eliminaper">
                    <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round"><polyline points="3 6 5 6 21 6"/><path d="M19 6l-1 14a2 2 0 0 1-2 2H8a2 2 0 0 1-2-2L5 6"/><path d="M10 11v6"/><path d="M14 11v6"/></svg>
                    Eliminar
                </a>
                <?php else: ?>
                <span class="gei-action-btn gei-btn-locked" title="Com avarias associadas">
                    <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round"><rect x="3" y="11" width="18" height="11" rx="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg>
                    Bloqueado
                </span>
                <?php endif; ?>
            </td>
            <?php endif; ?>
        </tr>
        <?php endwhile; ?>
        </tbody>
    </table>
</div>

<div style="display:flex;align-items:center;gap:6px;margin-bottom:12px;font-size:.78rem;color:#7b88a0;">
    <img src="<?php echo SVRURL ?>images/ordenar_tab.svg" alt="Ordenar coluna" style="width:16px;opacity:.6;">
    Clique numa coluna para ordenar
</div>

<div class="gei-pagination">
    <a class="gei-page-btn <?php if($page<=1) echo 'disabled'; ?>"
       href="<?php echo $page<=1 ? '#' : '?page='.$prev; ?>">
        <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round"><polyline points="15 18 9 12 15 6"/></svg>
    </a>
    <?php for($i=1; $i<=$totoalPages; $i++): ?>
    <a class="gei-page-btn <?php if($page==$i) echo 'active'; ?>" href="<?php echo SVRURL ?>peri?page=<?= $i ?>"><?= $i ?></a>
    <?php endfor; ?>
    <a class="gei-page-btn <?php if($page>=$totoalPages) echo 'disabled'; ?>"
       href="<?php echo $page>=$totoalPages ? '#' : '?page='.$next; ?>">
        <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round"><polyline points="9 18 15 12 9 6"/></svg>
    </a>
    <span class="gei-page-total">Total: <?php echo $totallinhas; ?></span>
</div>
       

        <a href="<?php echo SVRURL ?>configura">
<img src="<?php echo SVRURL ?>images/voltar.svg" alt="Voltar">
</a>


<br>




   <?php include ("jquery_bootstrap.php");?>




<br>


                    </div>
               
               </div>
            </div>
         </div>
      </div>
      <!-- end about -->
    


      <?php include ("footer.php");?>


   </body>
</html>
