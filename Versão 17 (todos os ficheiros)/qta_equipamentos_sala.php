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

$stmt2a = $db->prepare("SELECT MAX(id) AS me FROM escolas");
$stmt2a->execute();
$result2a = $stmt2a->get_result();
$rows2a = $result2a->fetch_row();
$stmt2a->close();

$maxesc = $rows2a[0];

$idescola = (int)base64_decode($_GET["ies"]);


$x = (int)base64_decode($_GET["x"]);





if ($x>1 || $x<0 || base64_decode ($_GET['ies'])>$maxesc || $idescola<0 
|| !isset($x) || !is_numeric($x)  
|| !isset($_GET['ies']) || !is_numeric(base64_decode ($_GET['ies'])) 
 || empty($_GET['ies'])  
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
$sa=(int)$_POST["sala"];
$z1 = htmlspecialchars(base64_decode($_GET["z"]), ENT_QUOTES, 'UTF-8');
}
elseif  ($x==1)
{
$sa=(int)base64_decode($_GET["si"]);
$z1 = htmlspecialchars(base64_decode($_GET["z"]), ENT_QUOTES, 'UTF-8');
}

if ( !isset($sa) || empty($sa)  || !is_numeric($sa)   || !isset($z1) || empty($z1))
{

    

?>


<script>

window.setTimeout(function() {
              window.location.href = '<?php echo SVRURL ?>lista';
          },10);
          </script>


<?php
}










$stmt10 = $db->prepare("SELECT nome FROM salas WHERE id = ?");
$stmt10->bind_param("i", $sa);
$stmt10->execute();
$result10 = $stmt10->get_result();
$rows10 = $result10->fetch_row();
$num_ns = $result10->num_rows;
$stmt10->close();

 $ns = $rows10[0];


$stmt11 = $db->prepare("SELECT nome_escola FROM escolas WHERE id = ?");
$stmt11->bind_param("i", $idescola);
$stmt11->execute();
$result11 = $stmt11->get_result();
$rows11 = $result11->fetch_row();
$num_ne = $result11->num_rows;
$stmt11->close();

$ne = $rows11[0];
?>



<?php
     if ($num_ns==0 || $num_ne==0 )
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
                     <li style="color:#1e2a45;">Quantidade de equipamento por sala</li>
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
               <!-- Cabeçalho com info da sala -->
               <div style="display:flex; align-items:center; flex-wrap:wrap; gap:16px; margin:14px 0 10px; padding:12px 16px; background:#f4f6fb; border:1px solid #e3e8f4; border-radius:10px;">
                  <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#4b6cb7" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" style="flex-shrink:0;"><path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/><polyline points="9 22 9 12 15 12 15 22"/></svg>
                  <div style="display:flex; flex-direction:column;">
                     <span style="font-size:.7rem; font-weight:600; text-transform:uppercase; letter-spacing:.4px; color:#7b88a0;">Sala</span>
                     <span style="font-size:.95rem; font-weight:700; color:#182848;"><?php echo htmlspecialchars($ns, ENT_QUOTES, 'UTF-8'); ?></span>
                  </div>
                  <span style="color:#c5cde0;">|</span>
                  <div style="display:flex; flex-direction:column;">
                     <span style="font-size:.7rem; font-weight:600; text-transform:uppercase; letter-spacing:.4px; color:#7b88a0;">Instituição</span>
                     <span style="font-size:.95rem; font-weight:700; color:#182848;"><?php echo htmlspecialchars($ne, ENT_QUOTES, 'UTF-8'); ?></span>
                  </div>
                  <span style="color:#c5cde0;">|</span>
                  <!-- Link Dashboard da Sala -->
                  <a href="<?php echo SVRURL ?>dashboard_sala.php?si=<?php echo base64_encode($sa); ?>&&ies=<?php echo base64_encode($idescola); ?>"
                     style="display:inline-flex; align-items:center; gap:6px; font-size:.82rem; font-weight:600; color:#4b6cb7; text-decoration:none; padding:4px 10px; background:#eef2fb; border:1.5px solid #c7d4f0; border-radius:7px; transition:all .15s;"
                     onmouseover="this.style.background='#dce6f8';this.style.borderColor='#4b6cb7';"
                     onmouseout="this.style.background='#eef2fb';this.style.borderColor='#c7d4f0';"
                     title="Dashboard da Sala">
                     <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="#4b6cb7" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="3" width="7" height="7"/><rect x="14" y="3" width="7" height="7"/><rect x="14" y="14" width="7" height="7"/><rect x="3" y="14" width="7" height="7"/></svg>
                     Dashboard
                  </a>
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
.gei-badge-red { background:#fde8e6; color:#c0392b; border-color:#f5c0bb; }
.gei-badge-green { background:#e6f9f2; color:#1a7a52; border-color:#a8e6cf; }
.gei-section-label { font-size:.78rem; font-weight:700; text-transform:uppercase; color:#7b88a0; letter-spacing:.4px; margin:18px 0 8px; }
.gei-pagination { display:flex; align-items:center; justify-content:center; gap:4px; flex-wrap:wrap; padding:12px 0; }
.gei-page-btn { display:inline-flex; align-items:center; justify-content:center; min-width:32px; height:32px; padding:0 10px; border-radius:6px; font-size:.8rem; font-weight:600; text-decoration:none !important; border:1.5px solid #e3e8f4; color:#4b6cb7 !important; background:#fff; transition:all .15s; }
.gei-page-btn:hover { background:#eef2fb; border-color:#4b6cb7; }
.gei-page-btn.active { background:#182848; color:#fff !important; border-color:#182848; }
.gei-page-btn.disabled { opacity:.4; pointer-events:none; }
.gei-page-total { font-size:.78rem; color:#7b88a0; font-weight:600; padding:0 10px; }
.gei-iface-table { width:100%; border-collapse:collapse; font-size:.82rem; margin-top:10px; }
.gei-iface-table th { background:#f4f6fb; color:#182848; font-size:.75rem; font-weight:700; text-transform:uppercase; padding:8px 12px; border-bottom:2px solid #e3e8f4; }
.gei-iface-table td { padding:8px 12px; border-bottom:1px solid #eef1f8; color:#1e2a45; }
.gei-iface-table tr:last-child td { border-bottom:none; font-weight:700; }
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
$em=$_SESSION['email'];


  $stmt = $db->prepare("SELECT tipo, COUNT(*) AS qta FROM equipamento WHERE id_sala = ? GROUP BY tipo ORDER BY tipo ASC LIMIT ?, ?");
  $stmt->bind_param("iii", $sa, $paginationStart, $limit);
  $stmt->execute();
  $result = $stmt->get_result();

  $stmt_avariados = $db->prepare("SELECT DISTINCT e.tipo FROM avarias_reparacoes a, equipamento e WHERE a.id_equi = e.id AND a.id_sala = ? AND a.id_escola = ? AND a.datareparacao IS NULL");
  $stmt_avariados->bind_param("ii", $sa, $idescola);
  $stmt_avariados->execute();
  $result_avariados = $stmt_avariados->get_result();
  $tipos_avariados = [];
  while($row_av = $result_avariados->fetch_row()) {
      $tipos_avariados[] = $row_av[0];
  }
  $stmt_avariados->close();


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
                <input type="hidden" name="sala" value="<?php echo (int)$sa; ?>">
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
                <th>Tipo</th>
                <th>Quantidade</th>
                <th>Equipamento</th>
            </tr>
        </thead>
        <tbody>

              

      

                <?php  



                
                $c=0;
                $somaqta=0;
                while($row=mysqli_fetch_array($result)) { 
                    //$n=$row['id'];
                   
              
                      //$c=$c+1;
                      //$totallinhas = $c;
                   
                   
               
                  
                          
               

                    $is_avariado = in_array($row['tipo'], $tipos_avariados);
                    ?>
                <tr>
                    <td data-label="Tipo"><?php echo htmlspecialchars($row['tipo'], ENT_QUOTES, 'UTF-8'); ?></td>
                    <td data-label="Quantidade">
                        <span class="gei-badge"><?php echo htmlspecialchars($row['qta'], ENT_QUOTES, 'UTF-8'); $somaqta=$somaqta+$row['qta']; ?></span>
                    </td>
                    <td data-label="Equipamento">
                        <?php
                        $stmt4 = $db->prepare("SELECT nomeequi FROM equipamento WHERE id_sala = ? AND tipo = ? ORDER BY nomeequi");
                        $stmt4->bind_param("is", $sa, $row['tipo']);
                        $stmt4->execute();
                        $result4 = $stmt4->get_result();
                        // Obter nomes avariados deste tipo nesta sala
                        $nomes_avariados = [];
                        if ($is_avariado) {
                            $stmt_av = $db->prepare("SELECT DISTINCT e.nomeequi FROM equipamento e, avarias_reparacoes a WHERE e.id_sala=? AND a.datareparacao IS NULL AND e.tipo=? AND a.id_equi=e.id");
                            $stmt_av->bind_param("is", $sa, $row['tipo']);
                            $stmt_av->execute();
                            $result_av = $stmt_av->get_result();
                            while($rav = $result_av->fetch_row()) { $nomes_avariados[] = $rav[0]; }
                        }
                        $nomes = [];
                        while($row4=mysqli_fetch_array($result4)) {
                            $nome = htmlspecialchars($row4['nomeequi'], ENT_QUOTES, 'UTF-8');
                            if (in_array($row4['nomeequi'], $nomes_avariados)) {
                                $nomes[] = '<span style="display:inline-block;background:#fde8e6;color:#c0392b;border:1.5px solid #f5c0bb;border-radius:7px;padding:2px 10px;font-weight:700;font-size:.82rem;">'.$nome.'</span>';
                            } else {
                                $nomes[] = $nome;
                            }
                        }
                        $stmt4->close();
                        echo implode(' &nbsp;|&nbsp; ', $nomes);
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
       href="<?php echo $page<=1?'#':'?z='.base64_encode($z1).'&&x='.base64_encode(1).'&&si='.base64_encode($sa).'&&ies='.base64_encode($idescola).'&&page='.$prev; ?>">
        <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round"><polyline points="15 18 9 12 15 6"/></svg>
    </a>
    <?php for($i=1; $i<=$totoalPages; $i++): ?>
    <a class="gei-page-btn <?php if($page==$i) echo 'active'; ?>"
       href="<?php echo SVRURL ?>qta_equipamentos_sala.php?z=<?php echo base64_encode($z1);?>&&x=<?php echo base64_encode(1);?>&&si=<?php echo base64_encode($sa);?>&&ies=<?php echo base64_encode($idescola);?>&&page=<?= $i ?>"><?= $i ?></a>
    <?php endfor; ?>
    <a class="gei-page-btn <?php if($page>=$totoalPages) echo 'disabled'; ?>"
       href="<?php echo $page>=$totoalPages?'#':'?z='.base64_encode($z1).'&&x='.base64_encode(1).'&&si='.base64_encode($sa).'&&ies='.base64_encode($idescola).'&&page='.$next; ?>">
        <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round"><polyline points="9 18 15 12 9 6"/></svg>
    </a>
    <span class="gei-page-total">Total: <?php echo $somaqta; ?></span>
</div>
       



 
        <br>


<?php
  $stmt2 = $db->prepare("SELECT e.tipo AS ti, COUNT(DISTINCT a.id_equi) AS c2 FROM avarias_reparacoes a, equipamento e, salas s WHERE a.id_equi = e.id AND s.id = e.id_sala AND a.id_escola = ? AND a.id_sala = ? AND a.datareparacao IS NULL GROUP BY e.tipo ORDER BY tipo ASC");
  $stmt2->bind_param("ii", $idescola, $sa);
  $stmt2->execute();
  $result2 = $stmt2->get_result();
  $stmt2->close();
  
     if ($result2->fetch_row() <> null)
    {
?>




<div class="gei-section-label">⚠ Avariados</div>
<table class="gei-iface-table">
    <thead>
        <tr>
            <th>Tipo</th>
            <th>Avariados</th>
            <th>Equipamento</th>
        </tr>
    </thead>
    <tbody>


<?php  
$stmt2b = $db->prepare("SELECT e.tipo AS ti, COUNT(DISTINCT a.id_equi) AS c2 FROM avarias_reparacoes a, equipamento e WHERE a.id_sala = ? AND a.id_escola = ? AND a.datareparacao IS NULL AND a.id_equi = e.id GROUP BY e.tipo ORDER BY tipo ASC");
$stmt2b->bind_param("ii", $sa, $idescola);
$stmt2b->execute();
$result2 = $stmt2b->get_result();
$stmt2b->close();


      

while($row2=mysqli_fetch_array($result2)) { 

$tipo=$row2['ti'];
?>
<tr>
    <td><?php echo htmlspecialchars($row2['ti'], ENT_QUOTES, 'UTF-8'); ?></td>
    <td><span class="gei-badge gei-badge-red"><?php echo htmlspecialchars($row2['c2'], ENT_QUOTES, 'UTF-8'); ?></span></td>
    <td><?php
        $stmt_q3 = $db->prepare("SELECT DISTINCT(e.nomeequi) as n FROM equipamento e, avarias_reparacoes a WHERE e.id_sala=? AND a.datareparacao IS NULL AND e.tipo=? AND a.id_equi=e.id ORDER BY e.nomeequi ASC");
        $stmt_q3->bind_param("is", $sa, $tipo);
        $stmt_q3->execute();
        $result3 = $stmt_q3->get_result();
        $nms = [];
        while($row3=$result3->fetch_array()) { $nms[] = htmlspecialchars($row3['n'], ENT_QUOTES, 'UTF-8'); }
        echo implode(' | ', $nms);
    ?></td>
</tr>
<?php }          

?>



    </tbody>
</table>
<?php
    }
    else
    {
?>
<div style="display:flex;align-items:center;gap:6px;font-size:.8rem;color:#1a7a52;margin:8px 0;">
    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="#1cc88a" stroke-width="2.2" stroke-linecap="round"><circle cx="12" cy="12" r="10"/><polyline points="9 12 11 14 15 10"/></svg>
    Sem avarias registadas.
</div>
<?php
    }


   
?>

<br>
<br>

   

<?php include ("jquery_bootstrap.php");
?>




<?php

$stmt5 = $db->prepare("SELECT oe.* FROM outro_equipamento oe, salas s WHERE oe.id_sala = s.id AND s.id = ? AND s.id_escola = ? ORDER BY oe.nomeoutro");
$stmt5->bind_param("ii", $sa, $idescola);
$stmt5->execute();
$result5 = $stmt5->get_result();
$stmt5->close();

  $count = $result5->num_rows;
?>



<?php
$stmt3 = $db->prepare("SELECT COUNT(*) FROM equipamento WHERE id_sala = ? AND ratointerface = 'USB'");
$stmt3->bind_param("i", $sa);
$stmt3->execute();
$result3 = $stmt3->get_result();
$rows3 = $result3->fetch_row();
$stmt3->close();
 $ratosusb = $rows3[0];

 $stmt3a = $db->prepare("SELECT COUNT(*) FROM equipamento WHERE id_sala = ? AND ratointerface = 'PS/2'");
 $stmt3a->bind_param("i", $sa);
 $stmt3a->execute();
 $result3a = $stmt3a->get_result();
 $rows3a = $result3a->fetch_row();
 $stmt3a->close();
  $ratosps2 = $rows3a[0];


  $stmt4c = $db->prepare("SELECT COUNT(*) FROM equipamento WHERE id_sala = ? AND tecladointerface = 'USB'");
$stmt4c->bind_param("i", $sa);
$stmt4c->execute();
$result4c = $stmt4c->get_result();
$rows4 = $result4c->fetch_row();
$stmt4c->close();
 $tecladosusb = $rows4[0];

 $stmt4a = $db->prepare("SELECT COUNT(*) FROM equipamento WHERE id_sala = ? AND tecladointerface = 'PS/2'");
 $stmt4a->bind_param("i", $sa);
 $stmt4a->execute();
 $result4a = $stmt4a->get_result();
 $rows4a = $result4a->fetch_row();
 $stmt4a->close();
  $tecladosps2 = $rows4a[0];


  $stmt7 = $db->prepare("SELECT COUNT(*) FROM equipamento WHERE id_sala = ? AND tecladointerface = 'Sem fios'");
$stmt7->bind_param("i", $sa);
$stmt7->execute();
$result7 = $stmt7->get_result();
$rows7 = $result7->fetch_row();
$stmt7->close();
 $tecladossemfios = $rows7[0];

 $stmt7a = $db->prepare("SELECT COUNT(*) FROM equipamento WHERE id_sala = ? AND ratointerface = 'Sem fios'");
 $stmt7a->bind_param("i", $sa);
 $stmt7a->execute();
 $result7a = $stmt7a->get_result();
 $rows7a = $result7a->fetch_row();
 $stmt7a->close();
  $ratossemfios = $rows7a[0];




?>

<?php
if (($ratosusb+$ratosps2+$ratossemfios<>0) and ($tecladosusb+$tecladosps2+$tecladossemfios<>0))
{

?>


<div class="gei-section-label">Interface — Ratos / Teclados</div>
<table class="gei-iface-table">
    <thead>
        <tr>
            <th>Interface</th>
            <th>Ratos</th>
            <th>Teclados</th>
             

                     
                  
                </tr>
            </thead>
            <tbody>

                 

                
                <tr>
                    <td >
               USB
                                     
                     </td>


                    <td >
                    
              <?php echo($ratosusb);?>
                    
                 
                    </td>

                    

                    <td  >
                    
                    <?php echo($tecladosusb);?>     
           
                            </td>

                
                </tr>

   
                
            <tr>
            <td>
              PS/2
                                     
                     </td>
            <td>
            <?php echo($ratosps2);?>
             </td>
             <td>
             <?php echo($tecladosps2);?>  
             </td>
                </tr>


                <tr>
            <td>
            Sem fios
                                     
                     </td>
            <td>
            <?php echo($ratossemfios);?>
             </td>
             <td>
             <?php echo($tecladossemfios);?>  
             </td>
                </tr>

                <tr>
            <td >
         
                                     
                     </td>
            <td >
            <?php echo('(');echo($ratosusb+$ratosps2+$ratossemfios);echo(')')?>
          
             </td>
             <td>
             <?php echo('(');echo($tecladosusb+$tecladosps2+$tecladossemfios);echo(')')?>
          
             </td>
                </tr>








               

      </tbody>
        </table>     

        <br>  

     
<?php
}
?>


<div class="gei-section-label">Outro equipamento</div>
<div class="gei-table-wrap">
<table class="gei-table">
    <thead>
        <tr>
            <th>Nome</th>
            <th>Quantidade</th>
            <th>Observações</th>
        </tr>
    </thead>
    <tbody>

                 
            <?php
if ($count>0) {

?>
                <?php 
                //$c=0;
                while($row5=mysqli_fetch_array($result5)) { 
                   // $n=$row['id'];
                    //$noeq=$row['nomeequi'];
               
             

                    ?>
<tr>
    <td data-label="Nome"><?php echo htmlspecialchars($row5['nomeoutro'], ENT_QUOTES, 'UTF-8'); ?></td>
    <td data-label="Quantidade"><span class="gei-badge"><?php echo htmlspecialchars($row5['qta'], ENT_QUOTES, 'UTF-8'); ?></span></td>
    <td data-label="Observações"><?php echo htmlspecialchars($row5['observacoes'], ENT_QUOTES, 'UTF-8'); ?></td>
</tr>

 
                <?php } 
                   }
              
                else
                {
                  ?>
                
            <tr>
            <td>
              Sem registos.</td>
                </tr>


                  <?php
               
              }
             
              
                ?>


      </tbody>
        </table>
</div>

<?php mysqli_close($db); ?>








<?php 
$z1 = htmlspecialchars(base64_decode($_GET["z"]), ENT_QUOTES, 'UTF-8');

if ($z1=='eq')
{
$sa = (int)base64_decode($_GET["si"]);
    ?>

<a href="<?php echo SVRURL ?>verequipsala?x=<?php echo base64_encode(2)?>&&ies=<?php echo base64_encode($idescola)?>&&si=<?php echo base64_encode($sa)?>">
<img src="<?php echo SVRURL ?>images/voltar.svg" alt="Voltar">
</a>
<?php 
}
elseif ($z1=='li')
{


    ?>
<a href="<?php echo SVRURL ?>lista">
<img src="<?php echo SVRURL ?>images/voltar.svg" alt="Voltar">
</a>
    <?php
}
    ?>



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