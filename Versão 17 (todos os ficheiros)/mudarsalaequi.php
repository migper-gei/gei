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

include ("css_inserir.php");

include("sessao_timeout.php");

//include("verifica_sessao.php");


 
  ?>

<?php
               

               if (isset($_GET['url']) 
               &&  is_numeric($_GET['url'])  &&  is_numeric($_GET['url2'])  &&  is_numeric($_GET['url3'])  )
               {
               $url = explode('/',$_GET['url']);
               $url2 = explode('/',$_GET['url2']);
               $url3 = explode('/',$_GET['url3']);
               }
               else
               {
                   ?>
                   
               <script>
               window.setTimeout(function() {
                   window.location.href = '<?php echo SVRURL ?>equip';
               }, 10);
               </script>
            
               <?php
               }
               
?>



<?php



   $sql = "SELECT e.nomeequi, s.nome ,es.nome_escola,es.id
   FROM equipamento e, salas s, escolas es
   WHERE e.id_sala=s.id and s.id_escola=es.id
   and e.id=".$url[0]." ";
   $result = mysqli_query($db,$sql);
   $rows1 =mysqli_fetch_row($result);
  
   $conta = $result->num_rows;


if ($conta==0)
{

?>

<script>
window.setTimeout(function() {
    window.location.href = '<?php echo SVRURL ?>equip';
}, 10);
</script>


<?php
}
?>

<?php //echo $rows1[3]; 
$idesc=$rows1[3]; 
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
                        <span style="color:#4b6cb7;">Equipamentos</span>
                     </li>
                     <li style="color:#c5cde0;font-size:.9rem;">&#8250;</li>
                     <li style="color:#1e2a45;">Ver equipamentos da sala >> Mudar de sala</li>
                  </ol>
               </nav>

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

               <!-- Cabeçalho com info do equipamento -->
               <div style="display:flex; align-items:center; flex-wrap:wrap; gap:16px; margin:14px 0 10px; padding:12px 16px; background:#f4f6fb; border:1px solid #e3e8f4; border-radius:10px;">
                  <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#4b6cb7" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" style="flex-shrink:0;"><rect x="2" y="7" width="20" height="14" rx="2"/><path d="M16 7V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v2"/></svg>
                  <div style="display:flex; flex-direction:column;">
                     <span style="font-size:.7rem; font-weight:600; text-transform:uppercase; letter-spacing:.4px; color:#7b88a0;">Equipamento</span>
                     <span style="font-size:.95rem; font-weight:700; color:#182848;"><?php echo htmlspecialchars($rows1[0], ENT_QUOTES, 'UTF-8'); ?></span>
                  </div>
                  <span style="color:#c5cde0;">|</span>
                  <div style="display:flex; flex-direction:column;">
                     <span style="font-size:.7rem; font-weight:600; text-transform:uppercase; letter-spacing:.4px; color:#7b88a0;">Sala</span>
                     <span style="font-size:.95rem; font-weight:700; color:#182848;"><?php echo htmlspecialchars($rows1[1], ENT_QUOTES, 'UTF-8'); ?></span>
                  </div>
                  <span style="color:#c5cde0;">|</span>
                  <div style="display:flex; flex-direction:column;">
                     <span style="font-size:.7rem; font-weight:600; text-transform:uppercase; letter-spacing:.4px; color:#7b88a0;">Instituição</span>
                     <span style="font-size:.95rem; font-weight:700; color:#182848;"><?php echo htmlspecialchars($rows1[2], ENT_QUOTES, 'UTF-8'); ?></span>
                  </div>
               </div>
    


<script language="javascript" type="text/javascript">

function showescola(escola) {

    document.frme.submit();

}

</script>



<?php 


$sql = "SELECT * FROM escolas ORDER by nome_escola";
$result = mysqli_query($db,$sql);
$rowcount = mysqli_num_rows($result);
 ?>


  





<form name="frme" id="frme" action = "" method = "post" >


<div class="action-section">
    
<h2 class="section-title"><i class="fas fa-school btn-icon"></i> Selecione a Instituição</h2>



   <div style="text-align: left;">


   <select required  title="Escolha a instituição" name="escola" onChange="showescola(this.value);" class="form-control required-field" >
   
 
   
  
   
   
   <?php
   
   
   
   $sql2 = "SELECT * FROM escolas ORDER by nome_escola";
   $result2 = mysqli_query($db,$sql2);
   
   while($row2=mysqli_fetch_array($result2))
   {
   
      if ($row2['id']==$_REQUEST["escola"])
      {
        //'.$row2['nome_escola'].'
         echo('<option selected value="'.$row2['id'].'">'.$row2['nome_escola'].'</option>');
   
   
      }
      else
   
     echo('<option value="'.$row2['id'].'">'.$row2['nome_escola'].'</option>');
   
   
   }
   
   
   echo('</select>');
   
   ?>
   
   </div>
   
</form>
    
<?php
$sql4 = "select id from escolas limit 1";
$result4 = mysqli_query($db,$sql4); 
$rows4 =mysqli_fetch_row($result4);


$nes = $rows4[0];

//$nes=$idesc;


if (!empty($_POST["escola"])) {
              
   $esc=$_POST["escola"];
   
   }
   else{
    $esc=$nes;  //1;
   }


?>

<?php

$sql1 = "select nome_escola
 from escolas 
 where id=$esc";
 $result1 = mysqli_query($db,$sql1); 
 $rows =mysqli_fetch_row($result1);
 
 
 $ne = $rows[0];
            ?>
     
     <div class="text-center mt-3">
                <span class="badge badge-primary p-2" style="font-size: 1rem;">
                    <i class="fas fa-building btn-icon"></i> <?php echo $ne; ?>
                </span>
            </div>
        </div>







<br>


  




    <?php

    if ($_SESSION['tipo']==1 )
    {

       
   
   ?>
  








<?php



$sqla = "SELECT  DISTINCT(s.nome) as no, s.id as sid
FROM escolas e, salas s
where e.id=s.id_escola and s.id<>$url3[0]
and e.id=$esc 
order by s.nome";

$resulta = mysqli_query($db,$sqla);
?>



<form action="<?php echo SVRURL ?>mudarsalaequi_ok.php?id=<?php echo($url[0]);?>&&sala=<?php echo($url3[0]);?>&&escola=<?php echo $idesc?>" method="post" class="needs-validation" novalidate>



<div class="action-section">


<label>Sala:</label>  


    
<select  class="form-control required-field" name="sala" required>


<?php
while($rowa=mysqli_fetch_array($resulta))
{

      echo('<option value="'.$rowa['sid'].'">'.$rowa['no'].'</option>');

        }
?>     
</select>
</div>

<?php } ?>









    <div class="form-group">
              
                </div>
                <br>
                <div class="form-group">
                <div  style=" text-align:center;width:100%">
                <button type="submit" name="submit" class="btn-submit">
                <i class="fa-solid fa-file-import"></i>
                                        &nbsp;Mudar equipamento de sala
                                    </button>
    </div>



   </form>
       

<br>
   


<a href="<?php echo SVRURL ?>verequipsala?x=<?php echo base64_encode(1) ?>&&ies=<?php echo base64_encode($url2[0])?>&&si=<?php echo base64_encode($url3[0])?>">
                    
    <img src="<?php echo SVRURL ?>images/voltar.svg" alt="Voltar">
</a>

<br><br>




                    </div>
               
               </div>
            </div>
         </div>
      </div>
      <!-- end about -->
       
      <script>
      // Example starter JavaScript for disabling form submissions if there are invalid fields
        (function() {
            'use strict';
            window.addEventListener('load', function() {
                // Fetch all the forms we want to apply custom Bootstrap validation styles to
                var forms = document.getElementsByClassName('needs-validation');
                // Loop over them and prevent submission
                var validation = Array.prototype.filter.call(forms, function(form) {
                    form.addEventListener('submit', function(event) {
                        if (form.checkValidity() === false) {
                            event.preventDefault();
                            event.stopPropagation();
                        }
                        form.classList.add('was-validated');
                    }, false);
                });
            }, false);
        })();
    </script>



      <?php include ("footer.php");?>


   </body>
</html>