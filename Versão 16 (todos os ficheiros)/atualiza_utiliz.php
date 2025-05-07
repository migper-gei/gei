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

 
  ?>
      
      <!-- about -->
      <div  class="about">
         <div class="container">
            <div class="row">
               <div class="col-md-12">
               <a href="#" class="btn btn-secondary disabled">Configurações >> Utilizadores >> Atualizar</a>
               <div class="titlepage">
                    
                  </div>
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
    window.location.href = '<?php echo SVRURL ?>l';
}, 10);
</script>
<?php
}


    





 $sql = "select * from utilizadores where id=".base64_decode($url[0])."";
 $result = mysqli_query($db,$sql);
 $row=mysqli_fetch_array($result);

 //echo($row['nome']);
?>
            <a href="<?php echo SVRURL ?>sair">Sair</a>
              </h3>   


<?php
if (mysqli_num_rows($result)==0)
{

   // header("Refresh:0;url=salas");
?>


   <script>
window.setTimeout(function() {
    window.location.href = '<?php echo SVRURL ?>utiliz';
}, 10);
</script>

<?php
}
else
{ 
?>



<div class="form-container">


<form action = "<?php echo SVRURL ?>atualiza_ok_utiliz.php?ui=<?php echo  base64_encode($row['id']); ?>" method = "post" class="needs-validation" novalidate >
                    <label>Utilizador: </label>  <br>  
                    <input style="width:100%" required class="form-control required-field"  type = "text" name="nome"  value="<?php echo $row['nome']; ?>"/><br /><br />
                 
                     <label>Email: </label>  <br>  
                    <input style="width:100%" required  class="form-control required-field"  type = "text" name="email"   value="<?php echo $row['email']; ?>"/><br /><br />
                
                    <label>Tipo:    </label>  
   
                    &nbsp; 
                   


                    <select style="width:100%" class="form-control required-field" name="tipo" style="width: 10%;">
                    <?php 
                              if ($row['tipo']==1)
                              {
                            ?>
                            <option value="<?php echo $row['tipo']; ?>" selected>
                            <?php echo $row['tipo']; ?></option>
                            <option value=2>2</option>
                            <option value=3>3</option>
                            <option value=4>4</option>
                            <?php 
                              }
                            elseif ($row['tipo']==2)
                            {
                                ?>
                                <option value="<?php echo $row['tipo']; ?>" selected>
                                <?php echo $row['tipo']; ?></option>
                                <option value=1>1</option>
                                 <option value=3>3</option>
                                 <option value=4>4</option>
                            <?php
                            }
                            elseif ($row['tipo']==3)
                            {
                            
                            ?>

                            <option value="<?php echo $row['tipo']; ?>" selected>
                            <?php echo $row['tipo']; ?></option>
                                 <option value=1>1</option>
                                 <option value=2>2</option>
                                 <option value=4>4</option>
                           
                              
                                 <?php
                            }
                            elseif ($row['tipo']==4)
                            {
                            
                            ?>

                            <option value="<?php echo $row['tipo']; ?>" selected>
                            <?php echo $row['tipo']; ?></option>
                                 <option value=2>1</option>
                                 <option value=2>2</option>
                                 <option value=3>3</option>



                            <?php
                            }
                            ?>
                          
                     </select>
                 
                   

                     (1 - Administrador &nbsp; &nbsp;      2 - Utilizador   &nbsp; &nbsp;       3 - Reparador &nbsp; &nbsp;       4 - Funcionário)
              
                                 
                                 
                     <div class="text-center mt-4">
                                    <button type="submit" class="btn-submit">
                                    <i class="fa-solid fa-pen"></i>
                                        &nbsp;Atualizar utilizador
                                    </button>
                                </div>

                 </form>



              


                           </div>
<?php
}


?>

<div class="text-center mt-3">
                            <a class="btn btn-secondary" title="Voltar" href="<?php echo SVRURL ?>utiliz">
                                <i class="bi bi-arrow-left"></i> Voltar
                            </a>
                   
                     <br> <br>
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