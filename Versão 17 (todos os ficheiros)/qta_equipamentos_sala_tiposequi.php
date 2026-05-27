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


<?php

$sql2a = "select max(id) as me  from escolas ";
$result2a = mysqli_query($db,$sql2a); 
$rows2a =mysqli_fetch_row($result2a);


$maxesc = $rows2a[0];


if (  !empty($_GET["x"]) &&  isset($_GET["x"])   &&  !empty($_GET["ies"]) &&  isset($_GET["ies"])   )
{

$x = (int)base64_decode($_GET["x"]);
$idescola = (int)base64_decode($_GET["ies"]);

}

else
{
?>



<script>

window.setTimeout(function() {
             window.location.href = '<?php echo SVRURL ?>lista';
          },10);
          </script>


<?php
}







if ($x==2 &&  isset($_GET["ti"]) && !empty($_GET["ti"]) &&
 ( empty($_POST['tiposequi']) || !isset($_POST['tiposequi']) 
)  
 )

{
  
    $tipoeq = (int)base64_decode($_GET["ti"]);
}
else

if ($idescola>$maxesc || $idescola<0 
|| $x>1 || $x<0 || !isset($x)  || !is_numeric($x) 
|| !isset($idescola)  || empty($idescola)  || !is_numeric($idescola)  
|| !isset($_POST["tiposequi"]) || empty($_POST["tiposequi"])
)
{



?>


<script>

window.setTimeout(function() {
              window.location.href = '<?php echo SVRURL ?>lista';
          },10);
          </script>


<?php
}


if ($x==0)
{
$tipoeq=$_POST["tiposequi"];

}
elseif ($x==1)
{
$tipoeq = (int)base64_decode($_GET["ti"]);

}



$sql11 = "select nome_escola  from escolas where id=$idescola";
$result11 = mysqli_query($db,$sql11); 
$rows11 =mysqli_fetch_row($result11);


$ne = $rows11[0];
$num_ne = mysqli_num_rows($result11);
?>



<?php
     if ( $num_ne==0 )
{
?>

<script>
window.setTimeout(function() {
    window.location.href = '<?php echo SVRURL ?>lista';
}, 10);
</script>


<?php

}

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
                        <a href="<?php echo SVRURL ?>lista" style="color:#4b6cb7;text-decoration:none;">Listagens</a>
                     </li>
                     <li style="color:#c5cde0;font-size:.9rem;">&#8250;</li>
                     <li style="color:#1e2a45;">Quantidade por tipo de equipamento</li>
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
               <!-- Cabeçalho com info do tipo e escola -->
               <div style="display:flex; align-items:center; flex-wrap:wrap; gap:16px; margin:14px 0 10px; padding:12px 16px; background:#f4f6fb; border:1px solid #e3e8f4; border-radius:10px;">
                  <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#4b6cb7" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" style="flex-shrink:0;"><rect x="2" y="3" width="20" height="14" rx="2"/><line x1="8" y1="21" x2="16" y2="21"/><line x1="12" y1="17" x2="12" y2="21"/></svg>
                  <div style="display:flex; flex-direction:column;">
                     <span style="font-size:.7rem; font-weight:600; text-transform:uppercase; letter-spacing:.4px; color:#7b88a0;">Tipo</span>
                     <span style="font-size:.95rem; font-weight:700; color:#182848;"><?php echo htmlspecialchars($tipoeq, ENT_QUOTES, 'UTF-8'); ?></span>
                  </div>
                  <span style="color:#c5cde0;">|</span>
                  <div style="display:flex; flex-direction:column;">
                     <span style="font-size:.7rem; font-weight:600; text-transform:uppercase; letter-spacing:.4px; color:#7b88a0;">Instituição</span>
                     <span style="font-size:.95rem; font-weight:700; color:#182848;"><?php echo htmlspecialchars($ne, ENT_QUOTES, 'UTF-8'); ?></span>
                  </div>
               </div>
<style>
.gei-table-wrap { background:#fff; border-radius:10px; box-shadow:0 2px 12px rgba(75,108,183,.10); border:1px solid #e3e8f4; overflow:hidden; margin-bottom:16px; }
.gei-table-toolbar { display:flex; align-items:center; justify-content:space-between; flex-wrap:wrap; gap:10px; padding:12px 16px; background:#f4f6fb; border-bottom:1px solid #e3e8f4; }
.gei-table-toolbar-left { display:flex; align-items:center; gap:10px; flex-wrap:wrap; }
.gei-table { width:100%; border-collapse:collapse; font-size:.84rem; }
.gei-table thead th { padding:10px 14px; background:#182848; color:#fff; font-size:.75rem; font-weight:700; text-transform:uppercase; letter-spacing:.5px; border:none; white-space:nowrap; }
.gei-table tbody tr { border-bottom:1px solid #eef1f8; transition:background .15s; }
.gei-table tbody tr:last-child { border-bottom:none; }
.gei-table tbody tr:hover { background:#f0f4fb; }
.gei-table tbody tr:nth-child(even) { background:#f7f9fe; }
.gei-table td { padding:10px 14px; vertical-align:middle; color:#1e2a45; }
.gei-badge { display:inline-flex; align-items:center; padding:2px 10px; border-radius:5px; font-size:.78rem; font-weight:700; background:#e8f0fe; color:#4b6cb7; border:1.5px solid #c7d4f0; }
.gei-pagination { display:flex; align-items:center; justify-content:center; gap:4px; flex-wrap:wrap; padding:12px 0; }
.gei-page-btn { display:inline-flex; align-items:center; justify-content:center; min-width:32px; height:32px; padding:0 10px; border-radius:6px; font-size:.8rem; font-weight:600; text-decoration:none !important; border:1.5px solid #e3e8f4; color:#4b6cb7 !important; background:#fff; transition:all .15s; }
.gei-page-btn:hover { background:#eef2fb; border-color:#4b6cb7; }
.gei-page-btn.active { background:#182848; color:#fff !important; border-color:#182848; }
.gei-page-btn.disabled { opacity:.4; pointer-events:none; }
.gei-page-total { font-size:.78rem; color:#7b88a0; font-weight:600; padding:0 10px; }
@media (max-width: 768px) {
    .gei-table thead { display:none; }
    .gei-table tbody tr { display:block; border:1px solid #e3e8f4; border-radius:8px; margin-bottom:10px; padding:10px 12px; background:#fff; box-shadow:0 1px 6px rgba(75,108,183,.08); }
    .gei-table tbody tr:nth-child(even) { background:#fff; }
    .gei-table td { display:flex; align-items:flex-start; gap:8px; padding:5px 2px; border:none; }
    .gei-table td::before { content: attr(data-label); min-width:110px; font-size:.72rem; font-weight:700; text-transform:uppercase; letter-spacing:.4px; color:#7b88a0; padding-top:2px; flex-shrink:0; }
}
</style>



<?php 

  // Database
  //include('config.php');
  
  // Set session
 // session_start();
  if(isset($_POST['records-limit'])){
      $_SESSION['records-limit'] = $_POST['records-limit'];
  }
  
  $limit = isset($_SESSION['records-limit']) ? $_SESSION['records-limit'] : 10;
  $page = (isset($_GET['page']) && is_numeric($_GET['page']) ) ? $_GET['page'] : 1;
  $paginationStart = ($page - 1) * $limit;
//$em=$_SESSION['email'];

  $sql = "select tipo,count(*) as qta, s.id,s.nome
  from equipamento eq, salas s
    where eq.id_sala=s.id and eq.tipo='".$tipoeq."' and s.id_escola=$idescola
    group by s.id, s.nome, tipo 
    order by qta desc LIMIT $paginationStart, $limit";
  $result = mysqli_query($db,$sql);


  // Get total records
  $totallinhas=$result->num_rows;

  // Prev + Next
  $prev = $page - 1;
  $next = $page + 1;
?>




<div class="gei-table-wrap">
    <div class="gei-table-toolbar">
        <div class="gei-table-toolbar-left">
            <form action="" method="post">
                <input type="hidden" name="tiposequi" value="<?php echo htmlspecialchars($tipoeq, ENT_QUOTES); ?>">
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
    </div>
    <table class="gei-table" id="js-sort-table">
        <thead>
            <tr>
                <th>Sala</th>
                <th>Quantidade</th>
                <th>Equipamento / Marca·Modelo</th>
            </tr>
        </thead>
        <tbody>

              

      

                <?php  



                
                $c=0; $soma=0;
                while($row=mysqli_fetch_array($result)) { 
                    //$n=$row['id'];
                     $sid=$row['id'];
                      //$c=$c+1;
                      //$totallinhas = $c;
                   
                   
                   
               

                    ?>
                <tr>
                    <td data-label="Sala"><?php echo htmlspecialchars($row['nome'], ENT_QUOTES, 'UTF-8'); ?></td>
                    <td data-label="Quantidade">
                        <span class="gei-badge"><?php echo htmlspecialchars($row['qta'], ENT_QUOTES, 'UTF-8'); $soma=$soma+$row['qta']; ?></span>
                    </td>
                    <td data-label="Equipamento">
                        <?php
                        $sql5 = "SELECT e.nomeequi, e.marca_modelo FROM equipamento e, salas s WHERE e.id_sala=s.id AND s.id=".$sid." AND e.tipo='".$tipoeq."'";
                        $result5 = mysqli_query($db,$sql5);
                        // Obter nomes avariados nesta sala para este tipo
                        $nomes_avariados = [];
                        $stmt_av = $db->prepare("SELECT DISTINCT e.nomeequi FROM equipamento e, avarias_reparacoes a WHERE e.id_sala=? AND a.datareparacao IS NULL AND e.tipo=? AND a.id_equi=e.id");
                        $stmt_av->bind_param("is", $sid, $tipoeq);
                        $stmt_av->execute();
                        $result_av = $stmt_av->get_result();
                        while($rav = $result_av->fetch_row()) { $nomes_avariados[] = $rav[0]; }
                        echo '<div style="display:flex;flex-direction:column;gap:4px;">';
                        while($row5=mysqli_fetch_array($result5)) {
                            $nome = htmlspecialchars($row5['nomeequi'], ENT_QUOTES, 'UTF-8');
                            $mm   = htmlspecialchars($row5['marca_modelo'], ENT_QUOTES, 'UTF-8');
                            if (in_array($row5['nomeequi'], $nomes_avariados)) {
                                echo '<span><span style="display:inline-block;background:#fde8e6;color:#c0392b;border:1.5px solid #f5c0bb;border-radius:7px;padding:2px 10px;font-weight:700;font-size:.82rem;">'.$nome.'</span> — '.$mm.'</span>';
                            } else {
                                echo '<span>'.$nome.' — '.$mm.'</span>';
                            }
                        }
                        echo '</div>';
                        ?>
                    </td>
                </tr>
                <?php }          
                     
                    // Calculate total pages
                    $totoalPages = ceil($totallinhas / $limit);
                ?>



        </tbody>
    </table>
</div>

<div style="display:flex;align-items:center;gap:6px;margin-bottom:12px;font-size:.78rem;color:#7b88a0;">
    <img src="<?php echo SVRURL ?>images/ordenar_tab.svg" alt="Ordenar coluna" style="width:16px;opacity:.6;">
    Clique numa coluna para ordenar
</div>
<div style="display:flex;align-items:center;gap:8px;margin-bottom:14px;padding:8px 14px;background:#fff8f8;border:1px solid #f5c0bb;border-radius:8px;font-size:.78rem;color:#7b88a0;">
    <span style="display:inline-block;background:#fde8e6;color:#c0392b;border:1.5px solid #f5c0bb;border-radius:7px;padding:2px 10px;font-weight:700;font-size:.78rem;">Ex.</span>
    <span>Equipamentos assinalados a vermelho encontram-se <strong style="color:#c0392b;">avariados</strong>.</span>
</div>

<div class="gei-pagination">
    <a class="gei-page-btn <?php if($page<=1) echo 'disabled'; ?>"
       href="<?php echo $page<=1?'#':'?x='.base64_encode(2).'&&ti='.base64_encode($tipoeq).'&&ies='.base64_encode($idescola).'&&page='.$prev; ?>">
        <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round"><polyline points="15 18 9 12 15 6"/></svg>
    </a>
    <?php for($i=1; $i<=$totoalPages; $i++): ?>
    <a class="gei-page-btn <?php if($page==$i) echo 'active'; ?>"
       href="<?php echo SVRURL ?>qta_equipamentos_sala_tiposequi.php?x=<?php echo base64_encode(2);?>&&ti=<?php echo base64_encode($tipoeq);?>&&ies=<?php echo base64_encode($idescola);?>&&page=<?= $i ?>"><?= $i ?></a>
    <?php endfor; ?>
    <a class="gei-page-btn <?php if($page>=$totoalPages) echo 'disabled'; ?>"
       href="<?php echo $page>=$totoalPages?'#':'?x='.base64_encode(2).'&&ti='.base64_encode($tipoeq).'&&ies='.base64_encode($idescola).'&&page='.$next; ?>">
        <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round"><polyline points="9 18 15 12 9 6"/></svg>
    </a>
    <span class="gei-page-total">Total: <?php echo $soma; ?></span>
</div>
       


        
        <?php
        ?>


 
        <br>


<br>


   

<?php include ("jquery_bootstrap.php");?>



<a href="<?php echo SVRURL ?>lista">
<img src="<?php echo SVRURL ?>images/voltar.svg" alt="Voltar">
</a>
<br><br>


                    </div>
               
               </div>
            </div>
         </div>
      </div>
      <!-- end about -->
    


      <?php include ("footer.php");?>


   </body>
</html>