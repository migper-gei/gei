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
                        <span style="color:#4b6cb7;">Configurações</span>
                     </li>
                     <li style="color:#c5cde0;font-size:.9rem;">&#8250;</li>
                     <li style="color:#1e2a45;">Salas &rsaquo; Nº por instituição</li>
                  </ol>
               </nav>
               <div class="titlepage">
             
                  </div>
               </div>
            </div>
            
            <div class="container">
               <div class="row">
                  <div class="col-md-10 offset-md-2">
              
                        

 <!-- Welcome Section -->
 <div class="welcome-section">
               
               <div>
             
                   <?php include("msg_bemvindo.php"); ?>
               </div>
      
       </div>
    


        <?php 

if(isset($_POST['records-limit'])){
    $_SESSION['records-limit'] = $_POST['records-limit'];
}

$limit = isset($_SESSION['records-limit']) ? $_SESSION['records-limit'] : 10;
$page = (isset($_GET['page']) && is_numeric($_GET['page']) ) ? $_GET['page'] : 1;
$paginationStart = ($page - 1) * $limit;


$sql = "select count(*) as cs, s.id_escola, e.nome_escola
from salas s, escolas e
where s.id_escola=e.id
group by id_escola order by e.nome_escola
LIMIT $paginationStart, $limit";
$result = mysqli_query($db,$sql);




$totallinhas =$result->num_rows ;//$rows[0];



// Calculate total pages
$totoalPages = ceil($totallinhas / $limit);

// Prev + Next
$prev = $page - 1;
$next = $page + 1;
?>





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
</style>

<div class="gei-table-wrap">
    <div class="gei-table-toolbar">
        <div class="gei-table-toolbar-left">
            <form action="<?php echo SVRURL ?>salasnum" method="post" style="margin:0;">
                <?php include("num_linhas.php"); ?>
            </form>
        </div>
    </div>

    <table class="gei-table" id="js-sort-table">
        <thead>
            <tr>
                <th>Instituição</th>
                <th style="width:15%;text-align:center;">Nº de Salas</th>
            </tr>
        </thead>
        <tbody>
        <?php while($row = mysqli_fetch_array($result)): ?>
        <tr>
            <td style="font-weight:600;"><?php echo htmlspecialchars($row['nome_escola'], ENT_QUOTES, 'UTF-8'); ?></td>
            <td style="text-align:center;">
                <span style="display:inline-flex;align-items:center;padding:2px 10px;border-radius:5px;font-size:.78rem;font-weight:700;background:#e8f0fe;color:#4b6cb7;border:1.5px solid #c7d4f0;">
                    <?php echo htmlspecialchars($row['cs'], ENT_QUOTES, 'UTF-8'); ?>
                </span>
            </td>
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
    <a class="gei-page-btn <?php if($page==$i) echo 'active'; ?>"
       href="<?php echo SVRURL ?>salasnum?page=<?= $i ?>"><?= $i ?></a>
    <?php endfor; ?>
    <a class="gei-page-btn <?php if($page>=$totoalPages) echo 'disabled'; ?>"
       href="<?php echo $page>=$totoalPages ? '#' : '?page='.$next; ?>">
        <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round"><polyline points="9 18 15 12 9 6"/></svg>
    </a>
    <span class="gei-page-total">Total: <?php echo $totallinhas; ?></span>
</div>

<a href="<?php echo SVRURL ?>sala?x=<?php echo base64_encode(0) ?>">
    <img src="<?php echo SVRURL ?>images/voltar.svg" alt="Voltar">
</a>


<br><br>



        <?php include ("jquery_bootstrap.php");?>






                    </div>
               
               </div>
            </div>
         </div>
      </div>
      <!-- end about -->
    


      <?php include ("footer.php");?>


   </body>
</html>