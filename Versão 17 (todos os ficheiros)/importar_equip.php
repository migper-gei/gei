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
      
      <!-- about -->
      <div  class="about">
         <div class="container">
            <div class="row">
               <div class="col-md-12">
               <!-- Breadcrumb melhorado -->
               <nav style="margin-bottom:10px;">
                  <ol style="display:flex;align-items:center;flex-wrap:wrap;gap:4px;list-style:none;padding:0;margin:0;font-size:.8rem;font-weight:600;color:#7b88a0;">
                     <li style="display:flex;align-items:center;gap:4px;">
                        <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="#4b6cb7" stroke-width="2.3" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="3"/><path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 0 1-2.83 2.83l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-4 0v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 0 1-2.83-2.83l.06-.06A1.65 1.65 0 0 0 4.68 15a1.65 1.65 0 0 0-1.51-1H3a2 2 0 0 1 0-4h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 0 1 2.83-2.83l.06.06A1.65 1.65 0 0 0 9 4.68a1.65 1.65 0 0 0 1-1.51V3a2 2 0 0 1 4 0v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 0 1 2.83 2.83l-.06.06A1.65 1.65 0 0 0 19.4 9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 0 1 0 4h-.09a1.65 1.65 0 0 0-1.51 1z"/></svg>
                        <span style="color:#4b6cb7;">Configurações</span>
                     </li>
                     <li style="color:#c5cde0;font-size:.9rem;">&#8250;</li>
                     <li style="color:#1e2a45;">Equipamentos >> Importação</li>
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
    



    <script language="javascript" type="text/javascript">

function showesc(escola) {

    document.frm.submit();

}

</script>






<form name="frm" id="frm" action = "" method = "post" >








<div class="action-section">
    
    <h2 class="section-title"><i class="fas fa-school btn-icon"></i> Selecione a Instituição</h2>


<select  
required class="form-control required-field"  style="width:100%;" name="escola" onChange="showesc(this.value);">


<?php



$sql2 = "SELECT * FROM escolas ORDER by nome_escola";
$result2 = mysqli_query($db,$sql2);






while($row2=mysqli_fetch_array($result2))
{

   if ($row2['id']==$_POST["escola"] ?? 0)
   {
     //'.$row2['nome_escola'].'
      echo('<option selected value="'.$row2['id'].'">'.$row2['nome_escola'].'</option>');


   }
   else

  echo('<option value="'.$row2['id'].'">'.$row2['nome_escola'].'</option>');


}


echo('</select>');

?>

</form>


<?php

$sql4 = "select id from escolas limit 1";
$result4 = mysqli_query($db,$sql4); 
$rows4 =mysqli_fetch_row($result4);


$nes = $rows4[0];




if (!empty($_POST["escola"])) {
              
              $esc = (int)$_POST["escola"];
              
              }
              else{
               $esc=$nes;  //1;
            
              }
?>



<?php


$stmt_salas = $db->prepare("SELECT DISTINCT s.nome as no, s.id as sid FROM escolas e, salas s WHERE e.id=s.id_escola AND e.id=? ORDER BY s.nome");
$stmt_salas->bind_param("i", $esc);
$stmt_salas->execute();
$resulta = $stmt_salas->get_result();
$stmt_salas->close();
?>

<?php

$stmt_ne = $db->prepare("SELECT nome_escola FROM escolas WHERE id=?");
$stmt_ne->bind_param("i", $esc);
$stmt_ne->execute();
$rows = $stmt_ne->get_result()->fetch_row();
$stmt_ne->close();


$ne = $rows[0];
           
           ?>
    
    <div class="text-center mt-3">
<span class="badge badge-primary p-2" style="font-size: 1rem;">
    <i class="fas fa-building btn-icon"></i> <?php echo $ne; ?>
</span>
</div>
</div>




<form enctype="multipart/form-data" method="post" action="<?php echo SVRURL ?>importar_equip_ok.php" class="needs-validation" novalidate>
               
             


<div class="action-section">


<label>Sala:</label>  






<select name="sala" required class="form-control required-field" style="width:100%">


<?php
while($rowa=mysqli_fetch_array($resulta))
{

      echo('<option value="'.$rowa['sid'].'">'.$rowa['no'].'</option>');

        }




?>     
</select>

  <!--
(Se lista vazia, inserir salas)
      -->
      </div>




<br>




<div class="form-group">

<div class="action-section">
                    <label for="file">Escolha o ficheiro .CSV para importar (Caso já exista o equipamento, não é importado)</label>
                    <input class="form-control required-field" required name="file" type="file" class="form-control" accept=".csv">
                    <small style="display:block;margin-top:6px;color:#7b88a0;font-size:.75rem;">
                        <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="vertical-align:middle;margin-right:3px;"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
                        Formato: <strong>.CSV</strong> com delimitador <strong>vírgula ( , )</strong> ou <strong>ponto e vírgula ( ; )</strong> &nbsp;&middot;&nbsp; Tamanho máximo: <strong>2 MB</strong>
                    </small>

                    </div>

                </div>
                <div class="form-group">
                    <?php //echo $message; ?>
                </div>
                <br>
                <div class="form-group">
                <div  style=" text-align:center;width:100%">
                <button type="submit" name="submit" class="btn-submit">
                <i class="fa-solid fa-file-import"></i>
                                        &nbsp;Importar equipamentos
                                    </button>
    </div>
                         
                 
                                       
                 </form>

<br>

               <a href="<?php echo SVRURL ?>configura">
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