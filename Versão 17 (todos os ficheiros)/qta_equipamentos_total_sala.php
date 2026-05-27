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

$x= (int)base64_decode($_GET["x"]);
$idescola= (int)base64_decode($_GET["ies"]);


if ( !isset($_GET["x"]) || empty($_GET["x"]) || !isset($_GET["ies"]) || empty($_GET["ies"])
|| $idescola>$maxesc || $idescola<0 
|| $x>1 || $x<0 || !is_numeric($x) 
|| empty($idescola) || !is_numeric($idescola)  
)
{
?>

<script>
window.setTimeout(function() {
              window.location.href = '<?php echo SVRURL ?>lista';
          },10);
</script>

<?php
exit;
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
exit;
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
                     <li style="color:#1e2a45;">Quantidade de equipamento total por sala</li>
                  </ol>
               </nav>
               </div>
            </div>
            
            <div class="container">
               <div class="row">
                  <div class="col-md-9 offset-md-2">
               <!-- Cabeçalho com info da instituição -->

   <!-- Welcome Section -->
   <div class="welcome-section"> 
<?php
include("msg_bemvindo.php");
?>
</div>
               <div style="display:flex; align-items:center; flex-wrap:wrap; gap:16px; margin:14px 0 10px; padding:12px 16px; background:#f4f6fb; border:1px solid #e3e8f4; border-radius:10px;">
                  <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#4b6cb7" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" style="flex-shrink:0;"><path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/><polyline points="9 22 9 12 15 12 15 22"/></svg>
                  <div style="display:flex; flex-direction:column;">
                     <span style="font-size:.7rem; font-weight:600; text-transform:uppercase; letter-spacing:.4px; color:#7b88a0;">Instituição</span>
                     <span style="font-size:.95rem; font-weight:700; color:#182848;"><?php echo htmlspecialchars($ne, ENT_QUOTES, 'UTF-8'); ?></span>
                  </div>
               </div>

<div style="display:flex;justify-content:flex-end;gap:8px;padding:10px 0;margin-bottom:8px;">
    <a href="<?php echo SVRURL ?>qta_equipamentos_total_sala_pdf.php?x=<?php echo base64_encode($x); ?>&&ies=<?php echo base64_encode($idescola); ?>"
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
.gei-table-wrap { background:#fff; border-radius:10px; box-shadow:0 2px 12px rgba(75,108,183,.10); border:1px solid #e3e8f4; overflow:hidden; margin-bottom:20px; }
.gei-table-section-header { padding:10px 16px; background:#182848; color:#fff; font-size:.78rem; font-weight:700; text-transform:uppercase; letter-spacing:.5px; }
.gei-table { width:100%; border-collapse:collapse; font-size:.84rem; }
.gei-table thead th { padding:10px 14px; background:#253d6e; color:#fff; font-size:.75rem; font-weight:700; text-transform:uppercase; letter-spacing:.5px; border:none; }
.gei-table tbody tr { border-bottom:1px solid #eef1f8; transition:background .15s; }
.gei-table tbody tr:last-child { border-bottom:none; }
.gei-table tbody tr:hover { background:#f0f4fb; }
.gei-table tbody tr:nth-child(even) { background:#f7f9fe; }
.gei-table td { padding:10px 14px; vertical-align:middle; color:#1e2a45; }
.gei-badge { display:inline-flex; align-items:center; padding:2px 10px; border-radius:5px; font-size:.78rem; font-weight:700; background:#e8f0fe; color:#4b6cb7; border:1.5px solid #c7d4f0; }
@media (max-width: 768px) {
    .gei-table thead { display:none; }
    .gei-table tbody tr { display:block; border:1px solid #e3e8f4; border-radius:8px; margin-bottom:10px; padding:10px 12px; background:#fff; box-shadow:0 1px 6px rgba(75,108,183,.08); }
    .gei-table tbody tr:nth-child(even) { background:#fff; }
    .gei-table td { display:flex; align-items:flex-start; gap:8px; padding:5px 2px; border:none; }
    .gei-table td::before { content: attr(data-label); min-width:120px; font-size:.72rem; font-weight:700; text-transform:uppercase; letter-spacing:.4px; color:#7b88a0; padding-top:2px; flex-shrink:0; }
}
</style>


<br>

      


<?php
$sql0 = "select distinct s.id, s.nome
from salas s
where s.id_escola=$idescola
and (
    exists (select 1 from equipamento e where e.id_sala=s.id)
    or exists (select 1 from outro_equipamento oe where oe.id_sala=s.id)
)
order by s.nome asc";
$result0 = mysqli_query($db,$sql0);
?>





    
<?php  
                
                $c=0;
                while($row0=mysqli_fetch_array($result0)) { 
                    $idsa=$row0['id'];
                    $nos=$row0['nome'];
                  //  echo htmlspecialchars($row0['nome'], ENT_QUOTES, 'UTF-8'); 
                  //echo $idsa;
                    
                
?>

<?php

$sql001 = "select id_escola
from  salas s
where s.id=$idsa";
$result001 = mysqli_query($db,$sql001);
$rows001 =mysqli_fetch_row($result001);

/*
echo $rows001[0];
echo '<br>';
echo $idescola;
*/


//ver se a sala tem equipamento ou material
$sql2a = "select count(*)  from equipamento where id_sala=$idsa";
$result2a = mysqli_query($db,$sql2a); 
$rows2a =mysqli_fetch_row($result2a);
$eqsala = $rows2a[0];


$sql3a = "select count(*)  from outro_equipamento where id_sala=$idsa";
$result3a = mysqli_query($db,$sql3a); 
$rows3a =mysqli_fetch_row($result3a);
$oequip = $rows3a[0];

/*
echo $idsa;
echo '<br>';
echo $eqsala;
*/

if ( ($rows001[0]==$idescola) and ($eqsala>0 or $oequip>0) )
{
?>

<div class="gei-table-wrap">
    <div class="gei-table-section-header">
        SALA: <?php echo htmlspecialchars($nos, ENT_QUOTES, 'UTF-8'); ?>
    </div>
    <table class="gei-table">
        <thead>
            <tr>
                <th>Tipo / Outro equipamento</th>
                <th>Quantidade</th>
            </tr>
        </thead>
        <tbody>

              

      

                <?php  







$sql01 = "select s.nome,tipo,count(*) as qta 
from equipamento e, salas s
where e.id_sala=s.id and s.id_escola=$idescola and s.id=$idsa
group by tipo 
order by tipo asc";
$result01 = mysqli_query($db,$sql01);

                
              //  $c=0;
                while($row=mysqli_fetch_array($result01)) { 
                    //$n=$row['id'];
                   
              
                     // $c=$c+1;
                      //$totallinhas = $c;
                   
                   
                   
               

                    ?>
                <tr>
                    <!--
                <td   width="20%"  scope="row"><?php echo htmlspecialchars($row['nome'], ENT_QUOTES, 'UTF-8'); 
                    
                    ?>
                 
                    </td>
                -->
                <td data-label="Tipo"><?php echo htmlspecialchars($row['tipo'], ENT_QUOTES, 'UTF-8'); ?></td>
                <td data-label="Quantidade"><span class="gei-badge"><?php echo htmlspecialchars($row['qta'], ENT_QUOTES, 'UTF-8'); ?></span></td>

                
                </tr>
                <?php }     
                
                
                
                
                ?>




<?php

//echo '<br>';
//echo $idescola;

        $sql5 = "
  select oe.nomeoutro, sum(oe.qta) as so from outro_equipamento oe, salas s
where oe.id_sala=s.id
and  s.id_escola=$idescola and s.id=$idsa
group by oe.nomeoutro
order by oe.nomeoutro ";
 
  $result5 = mysqli_query($db,$sql5);

  $count = mysqli_num_rows($result5);
?>




            
      
            <?php 
                
                while($row5=mysqli_fetch_array($result5)) { 
                   // $n=$row['id'];
                    //$noeq=$row['nomeequi'];
               
             

                    ?>


<tr>
                <td data-label="Tipo"><?php echo htmlspecialchars($row5['nomeoutro'], ENT_QUOTES, 'UTF-8'); ?></td>
                <td data-label="Quantidade"><span class="gei-badge"><?php echo htmlspecialchars($row5['so'], ENT_QUOTES, 'UTF-8'); ?></span></td>

                    
                
                </tr>
               



                <?php } // fim while row5
?>

        </tbody>
    </table>
</div>

<br>

<?php
            } // fim if eqsala/oequip
        } // fim while row0
        ?>







<?php include ("jquery_bootstrap.php");?>

<?php mysqli_close($db); ?>




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