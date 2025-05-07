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

 



$sql2a = "select max(id) as me  from escolas ";
$result2a = mysqli_query($db,$sql2a); 
$rows2a =mysqli_fetch_row($result2a);


$maxesc = $rows2a[0];


$idescola=base64_decode($_GET['ie']);

//echo $idescola;

if (base64_decode($_GET['ie'])>$maxesc || !is_numeric(base64_decode($_GET['ie'])) )
{

?>


<script>

window.setTimeout(function() {
              window.location.href = '<?php echo SVRURL ?>salas.php?x=0';
          },40);
          </script>


<?php
}




$sql11 = "select nome_escola  from escolas where id=$idescola";
$result11 = mysqli_query($db,$sql11); 
$rows11 =mysqli_fetch_row($result11);


$ne = $rows11[0];
  ?>
      
      <!-- about -->
      <div  class="about">
         <div class="container">
            <div class="row">
               <div class="col-md-12">
               <a href="#" class="btn btn-secondary disabled">Configurações >> Salas >> Inserir</a>
               <div class="titlepage">
                     <h2><?php echo $ne ?></h2>
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

<?php
$token=md5(uniqid(rand(), TRUE));
$_SESSION['token']=$token;
?>

              
<?php

//echo base64_encode($idescola);
//$idescry=base64_encode($idescola);
//echo '<br>';

  // echo base64_decode($cry);


?>


<div class="form-container">
                      
<div class="step-indicator">
                  

<i class="bi bi-info-circle-fill me-2"></i>
Complete todos os campos obrigatórios (indicados com fundo azul claro)
</div>



   <form action = "<?php echo SVRURL ?>gravasala?ie=<?php echo base64_encode($idescola)?>" method = "post" class="needs-validation" novalidate>

   <input type="hidden" name="token" value="<?php echo $token; ?>" >
<br>  
                    <label>Nome da sala: </label>  <br>  
                    <input class="form-control required-field" type = "text" name ="nome"  required style="width:100%;"/><br /><br />
                 
                     <label>Localização: </label>  <br>  
                    <input  class="form-control required-field"type = "text" name ="localizacao"  required style="width:100%;"/><br /><br />
                   
                    <label>Departamento / Grupo / Serviço: </label>  <br>  
                    <input required class="form-control required-field" style="width:100%;" type = "text" name ="departamento"  /><br /><br />
                    
                    <label>Equipamento requisitável: </label>                

                    <select required name="eqreq" required style="width: 100%;
            height: 35px; 
                       "
                      class="form-control required-field">
            
<?php
      echo('<option selected value="">-- Selecione --</option>');
      echo('<option value="Sim">Sim</option>');
      echo('<option  value="Não">Não</option>');
   
?>     
</select>

<br><br>
                    
                    <div  style=" text-align:center;width:100%"> 
                     
                    <button type="submit" class="btn-submit">
                                        <i class="bi bi-check-circle-fill me-2"></i>
                                        &nbsp;Inserir sala
                                    </button>
    </div>
                 </form>
</div>
                
<div class="text-center mt-3">
                            <a class="btn btn-secondary" title="Voltar" href="<?php echo SVRURL ?>sala?x=<?php echo base64_encode(1)?>&&esi=<?php echo base64_encode($idescola) ?>">
                                <i class="bi bi-arrow-left"></i> Voltar
                            </a>
                            <br>  <br>
                        </div>


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