<?php
  session_start();
  session_regenerate_id();
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
      <!-- loader  -->
      <div class="loader_bg">
         <div class="loader"><img src="<?php echo SVRURL ?>images/loading.gif" alt="Loading" /></div>
      </div>
      <!-- end loader -->


     <?php include ("header.php");?>
     


     <?php
//session_start();

include ("css_inserir.php");

include("sessao_timeout.php");

//include("verifica_sessao.php");

//print_r(scandir(session_save_path()));


 
  ?>
      
      <!-- about -->
      <div  class="about">
         <div class="container">
            <div class="row">
               <div class="col-md-12">
               <a href="#" class="btn btn-secondary disabled">Configurações >> Tipos de equipamento >> Atualizar</a>
               <div class="titlepage">
                    
                  </div>
               </div>
            </div>
            
            <div class="container">
               <div class="row">
                  <div class="col-md-8 offset-md-3">
              
                        







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
    window.location.href = 'configura';
}, 10);
</script>
<?php
}


    



 $sql = "select * from tipos_equipamento where id=".base64_decode($url[0])."";
 $result = mysqli_query($db,$sql);
$row=mysqli_fetch_array($result);


?>
 


<?php
if (mysqli_num_rows($result)==0)
{

   // header("Refresh:0;url=salas");
?>


   <script>
window.setTimeout(function() {
    window.location.href = '<?php echo SVRURL ?>tiposequip';
}, 10);
</script>

<?php
}
else
{ 
?>

    
<div class="form-container">

<form action = "<?php echo SVRURL ?>atualiza_ok_tiposequip.php?tpi=<?php echo base64_encode($row['id']); ?>&nom=<?php echo $row['nome']; ?>" method = "post" class="needs-validation" novalidate>
                    <label>Tipo de equipamento:     </label>  <br>  
                    <input style="width:100%" class="form-control required-field" type = "text" name="nomeeq"  
                    required value="<?php echo urldecode($row['nome']); ?>"/><br>
                    (Ao atualizar, também será atualizado o tipo nos respetivos equipamentos)
                   <br><br>
                    <div class="text-center mt-4">
                                    <button type="submit" class="btn-submit">
                                    <i class="fa-solid fa-pen"></i>
                                        &nbsp;Atualizar tipo de equipamento
                                    </button>
                              
                           
                        </div>   
                 
                                 
                                        
                 </form>
</diV>
                 <div class="text-center mt-3">
                            <a class="btn btn-secondary" title="Voltar" href="<?php echo SVRURL ?>tiposequip">
                                <i class="bi bi-arrow-left"></i> Voltar
                            </a>
                   
                     <br> <br>
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