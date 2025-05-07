<?php
  session_start();
  session_regenerate_id();
  ?>

<!DOCTYPE html>
<html lang="pt">
   <head>
      

<?php

 include ("head.php");
 include ("css_inserir.php");
?>

   </head>


   <!-- body -->
   <body class="main-layout">
      <!-- loader  -->
      <div class="loader_bg">
         <div class="loader"><img src="<?php echo SVRURL ?>images/loading.gif" alt="Loading" /></div>
      </div>
      <!-- end loader -->


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
               <a href="#" class="btn btn-secondary disabled">Configurações gerais</a>
             
               </div>
            </div>
            
            <div class="container">
               <div class="row">
                  <div class="col-md-10 offset-md-2">
              
     <!--                   
                  <div class="welcome-section">   
<?php
include("msg_bemvindo.php");
?>
 </div>
-->

 <div class="titlepage">
                     <h2>Atualizar instituição</h2>
                  </div>

 <?php

if (isset($_GET['url']))
{
$url = explode('/',$_GET['url']);

//echo $url[0];
}
else
{
    ?>
<script>
window.setTimeout(function() {
    window.location.href = '<?php echo SVRURL ?>dadosescola';
}, 10);
</script>
<?php
}



    





 $sql = "select * from escolas where id=".base64_decode ($url[0])."";
 $result = mysqli_query($db,$sql);
 $row=mysqli_fetch_array($result);

 //echo($row['nome']);
?>
           <!-- <a href="<?php echo SVRURL ?>sair">Sair</a>-->
              </h3>   
<br>

<?php
if (mysqli_num_rows($result)==0 )
{

   // header("Refresh:0;url=salas");
?>


   <script>
window.setTimeout(function() {
    window.location.href = '<?php echo SVRURL ?>dadosescola';
}, 10);
</script>

<?php
}
else
{ 
?>


<form class="needs-validation" novalidate action = "<?php echo SVRURL ?>atualizaescok/<?php echo base64_encode ($row['id']); ?> " method = "post" >
                    <label>Nome da instituição: </label>  <br>  
                    <input       style="width: 100%;" placeholder="Nome da instituição"
                    class="form-control required-field" 
                    size=50 type = "text" name="nome"  required value="<?php echo $row['nome_escola']; ?>"/><br /><br />
                 
                   
                    <div class="text-center mt-4">
                                    <button type="submit" class="btn-submit">
                                    <i class="fa-solid fa-pen"></i>
                                        &nbsp;Atualizar instituição
                                    </button>
                                </div>
                 </form>


                 <div class="text-center mt-3">
                            <a class="btn btn-secondary" title="Voltar" href="<?php echo SVRURL ?>dadosescola">
                                <i class="bi bi-arrow-left"></i> Voltar
                            </a>
                   
                        </div>

 
<?php
}
?>


<br>
                    </div>
               
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