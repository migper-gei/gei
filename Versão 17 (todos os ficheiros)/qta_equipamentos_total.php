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

$x = (int)base64_decode($_GET["x"]);
$idescola = (int)base64_decode($_GET["ies"]);


if ($idescola>$maxesc || $idescola<0 
|| $x>1 || $x<0 || !isset($x)    || !is_numeric($x) 
|| !isset($idescola)  || empty($idescola)  || !is_numeric($idescola)  

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

$idescola = (int)base64_decode($_GET["ies"]);
}
elseif ($x==1)
{

$idescola = (int)base64_decode($_GET["ies"]);
}

$sql11 = "select nome_escola  from escolas where id=$idescola";
$result11 = mysqli_query($db,$sql11); 
$rows11 =mysqli_fetch_row($result11);


$ne = $rows11[0];
$num_ne = mysqli_num_rows($result11);
?>



<?php
     if ($num_ne==0 )
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
                     <li style="color:#1e2a45;">Quantidade total de equipamento</li>
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
               <!-- Cabeçalho com info da instituição -->
               <div style="display:flex; align-items:center; flex-wrap:wrap; gap:16px; margin:14px 0 10px; padding:12px 16px; background:#f4f6fb; border:1px solid #e3e8f4; border-radius:10px;">
                  <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#4b6cb7" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" style="flex-shrink:0;"><path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/><polyline points="9 22 9 12 15 12 15 22"/></svg>
                  <div style="display:flex; flex-direction:column;">
                     <span style="font-size:.7rem; font-weight:600; text-transform:uppercase; letter-spacing:.4px; color:#7b88a0;">Instituição</span>
                     <span style="font-size:.95rem; font-weight:700; color:#182848;"><?php echo htmlspecialchars($ne, ENT_QUOTES, 'UTF-8'); ?></span>
                  </div>
               </div>

<div style="display:flex;justify-content:flex-end;gap:8px;padding:10px 0;margin-bottom:8px;">
    <a href="<?php echo SVRURL ?>qta_equipamentos_total_pdf.php?x=<?php echo base64_encode($x); ?>&&ies=<?php echo base64_encode($idescola); ?>"
       target="_blank" title="Exportar para PDF"
       style="display:inline-flex;align-items:center;gap:7px;padding:7px 16px;border-radius:7px;font-size:.82rem;font-weight:600;color:#fff !important;background-color:#6c757d !important;border:none;text-decoration:none;box-shadow:0 2px 8px rgba(108,117,125,0.20);transition:opacity .15s,transform .15s;">
        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="#fff" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
            <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/>
            <line x1="9" y1="13" x2="15" y2="13"/><line x1="9" y1="17" x2="15" y2="17"/>
        </svg>
        Exportar PDF
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
.gei-iface-table tr:last-child td { border-bottom:none; }
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


$sql = "select tipo,count(*) as qta 
from equipamento e, salas s
where e.id_sala=s.id and s.id_escola=$idescola
group by tipo 
order by tipo asc LIMIT $paginationStart, $limit";
$result = mysqli_query($db,$sql);


// Get total records
$totallinhas=$result->num_rows;


// Prev + Next
$prev = $page - 1;
$next = $page + 1;
?>



<div class="gei-table-wrap">
 
    <table class="gei-table" id="js-sort-table">
        <thead>
            <tr>
                <th>Tipo</th>
                <th>Quantidade</th>
            </tr>
        </thead>
        <tbody>

              

      

                <?php  
                
                $c=0;
                while($row=mysqli_fetch_array($result)) { 
                    //$n=$row['id'];
                   
              
                     // $c=$c+1;
                      //$totallinhas = $c;
                   
                   
                   
               

                    ?>
                <tr>
                    <td data-label="Tipo"><?php echo htmlspecialchars($row['tipo'], ENT_QUOTES, 'UTF-8'); ?></td>
                    <td data-label="Quantidade"><span class="gei-badge"><?php echo htmlspecialchars($row['qta'], ENT_QUOTES, 'UTF-8'); ?></span></td>
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

<div class="gei-pagination">
    <a class="gei-page-btn <?php if($page<=1) echo 'disabled'; ?>"
       href="<?php echo $page<=1?'#':'?x='.base64_encode(1).'&&ies='.base64_encode($idescola).'&&page='.$prev; ?>">
        <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round"><polyline points="15 18 9 12 15 6"/></svg>
    </a>
    <?php for($i=1; $i<=$totoalPages; $i++): ?>
    <a class="gei-page-btn <?php if($page==$i) echo 'active'; ?>"
       href="<?php echo SVRURL ?>qta_equipamentos_total.php?x=<?php echo base64_encode(1);?>&&ies=<?php echo base64_encode($idescola);?>&&page=<?= $i ?>"><?= $i ?></a>
    <?php endfor; ?>
    <a class="gei-page-btn <?php if($page>=$totoalPages) echo 'disabled'; ?>"
       href="<?php echo $page>=$totoalPages?'#':'?x='.base64_encode(1).'&&ies='.base64_encode($idescola).'&&page='.$next; ?>">
        <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round"><polyline points="9 18 15 12 9 6"/></svg>
    </a>
    <span class="gei-page-total">Total: <?php echo $totallinhas; ?></span>
</div>





        
 
        <br>

<?php

$sql2 = "SELECT e.tipo as ti ,count(DISTINCT(a.id_equi)) as c2 
FROM avarias_reparacoes a,equipamento e 
WHERE  a.datareparacao is null and a.id_equi=e.id and a.id_escola=$idescola
group by e.tipo order by tipo asc"; 
      $result2 = mysqli_query($db,$sql2);

      if (mysqli_fetch_row($result2)<>null)
      {
?>


<div class="gei-section-label">⚠ Avariados </div>
<table class="gei-iface-table">
    <thead>
        <tr>
            <th>Tipo</th>
            <th>Avariados / Equipamento</th>
        </tr>
    </thead>
    <tbody>


<?php  

$sql2 = "SELECT e.tipo as ti ,count(DISTINCT(a.id_equi)) as c2 
FROM avarias_reparacoes a,equipamento e 
WHERE  a.datareparacao is null 
and a.id_equi=e.id 
and a.id_escola=$idescola
group by e.tipo order by tipo asc"; 
      $result2 = mysqli_query($db,$sql2);

while($row2=mysqli_fetch_array($result2)) { 

?>
<tr>
<td width="40%"  scope="row"><?php echo htmlspecialchars($row2['ti'], ENT_QUOTES, 'UTF-8'); echo('<br>');

?>

</td>
<td width="45%" >

<?php echo htmlspecialchars($row2['c2'], ENT_QUOTES, 'UTF-8'); 

?>

<?php
$sql3 = "SELECT DISTINCT(e.nomeequi) as n 
FROM equipamento e, avarias_reparacoes a  
WHERE a.id_equi=e.id
and a.id_escola=$idescola
and a.datareparacao is null and e.tipo='".$row2['ti']."' 
and a.id_equi=e.id
 order by e.nomeequi asc;"; 
$result3 = mysqli_query($db,$sql3);

echo ('<br>');


$nomes3 = [];
while($row3=mysqli_fetch_array($result3)) { 
    $nomes3[] = htmlspecialchars($row3['n'], ENT_QUOTES, 'UTF-8');
}
echo implode(' &nbsp; ', $nomes3);



?>




</td>












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



<?php


$sql3 = "select count(*) 
from equipamento e, salas s
where e.id_sala=s.id and s.id_escola=$idescola
and e.ratointerface='USB'";
$result3 = mysqli_query($db,$sql3); 
$rows3 =mysqli_fetch_row($result3);

 $ratosusb = $rows3[0];

 $sql3a = "select count(*) 
from equipamento e, salas s
where e.id_sala=s.id and s.id_escola=$idescola
and e.ratointerface='PS/2'";
 $result3a = mysqli_query($db,$sql3a); 
 $rows3a =mysqli_fetch_row($result3a);
 
  $ratosps2 = $rows3a[0];



  $sql4 = "select count(*) 
from equipamento e, salas s
where e.id_sala=s.id and s.id_escola=$idescola
and  e.tecladointerface='USB'";
$result4 = mysqli_query($db,$sql4); 
$rows4 =mysqli_fetch_row($result4);

 $tecladosusb = $rows4[0];

 $sql4a = "select count(*) 
from equipamento e, salas s
where e.id_sala=s.id and s.id_escola=$idescola
and e.tecladointerface='PS/2'";
 $result4a = mysqli_query($db,$sql4a); 
 $rows4a =mysqli_fetch_row($result4a);
 
  $tecladosps2 = $rows4a[0];




  $sql7 = "select count(*) 
from equipamento e, salas s
where e.id_sala=s.id and s.id_escola=$idescola
and  e.tecladointerface='Sem fios'";
$result7 = mysqli_query($db,$sql7); 
$rows7 =mysqli_fetch_row($result7);

 $tecladossemfios = $rows7[0];

 $sql7a = "select count(*) 
from equipamento e, salas s
where e.id_sala=s.id and s.id_escola=$idescola
and e.ratointerface='Sem fios'";
 $result7a = mysqli_query($db,$sql7a); 
 $rows7a =mysqli_fetch_row($result7a);
 
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
            <th>Ratos &nbsp;<a href="<?php echo SVRURL ?>qta_ratos_total_sala.php?x=<?php echo base64_encode(0) ?>&&ies=<?php echo base64_encode($idescola) ?>" style="font-size:.72rem;color:#4b6cb7;font-weight:400;">(ver por sala)</a></th>
            <th>Teclados &nbsp;<a href="<?php echo SVRURL ?>qta_teclados_total_sala.php?x=<?php echo base64_encode(0) ?>&&ies=<?php echo base64_encode($idescola) ?>" style="font-size:.72rem;color:#4b6cb7;font-weight:400;">(ver por sala)</a></th>
             

                     
                  
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


<br>





<?php

        $sql5 = "
  select oe.nomeoutro, oe.observacoes, sum(oe.qta) as so from outro_equipamento oe, salas s
where oe.id_sala=s.id
and  s.id_escola=$idescola
group by oe.nomeoutro, oe.observacoes
order by oe.nomeoutro ";
 
  $result5 = mysqli_query($db,$sql5);

  $count = mysqli_num_rows($result5);
?>




<div class="gei-section-label">Outro equipamento</div>
<div class="gei-table-wrap">
<table class="gei-table" id="js-sort-table">
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
    <td data-label="Quantidade"><span class="gei-badge"><?php echo htmlspecialchars($row5['so'], ENT_QUOTES, 'UTF-8'); ?></span></td>
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

<br>
<a href="<?php echo SVRURL ?>lista">
<img src="<?php echo SVRURL ?>images/voltar.svg" alt="Voltar">
</a>
<br><br>

 <?php  
                mysqli_close($db);
                ?>

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