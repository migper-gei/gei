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




$sql2a = "select max(id) as me from escolas";
$result2a = mysqli_query($db,$sql2a); 
$rows2a =mysqli_fetch_row($result2a);


$maxesc = $rows2a[0];

$idescola=base64_decode($_GET['ti']);



if (  empty($idescola) ||  !isset($idescola) || !is_numeric($idescola)    )
{
?>
  <script>

  window.setTimeout(function() {
               window.location.href = '<?php echo SVRURL ?>tarefas.php?x=<?php echo base64_encode(0)?>';
            },10);
            </script>
<?php
}







if ($idescola>$maxesc )
{

?>


<script>

window.setTimeout(function() {
              window.location.href = '<?php echo SVRURL ?>tarefas.php?x=<?php echo base64_encode(0)?>';
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
               <a href="#" class="btn btn-secondary disabled">Configurações >> Tarefas a realizar >> Inserir</a>
               <div class="titlepage">
                     <h2> <?php echo $ne ?></h2>
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

<div class="form-container">
<div class="step-indicator">
               

<i class="bi bi-info-circle-fill me-2"></i>
Complete todos os campos obrigatórios (indicados com fundo azul claro)
</div>


   <form name="tar" class="needs-validation" novalidate action = "<?php echo SVRURL ?>grava_tarefa.php?ti=<?php echo base64_encode($idescola)?>" method = "post" >





   <input type="hidden" name="token" value="<?php echo $token; ?>" >


<br>  
<?php


$sqla = "SELECT  DISTINCT(nome),id 
FROM salas 
where id_escola=$idescola
order by nome";

$resulta = mysqli_query($db,$sqla);
?>

<label>Sala: </label>  



<select  style="width: 100%;
            height: 35px; "
                        class="form-control required-field" name="salatar" required>


<?php
echo('<option value="">-- Selecione --</option>');
while($rowa=mysqli_fetch_array($resulta))
{

      echo('<option value="'.$rowa['id'].'">'.$rowa['nome'].'</option>');

        }




?>     
</select>

<br><br>
<label>Descrição: </label>  <br>  
                   <textarea  class="form-control required-field" required style="width:100%" rows="5" cols="70"  name="descricao"></textarea>
                   <br><br>
                    <label>Urgência: </label>  
                   <br>

<select  style="width:100%; height:35px; "  class="form-control required-field" name="urgencia" required>


<?php
echo('<option value="">-- Selecione --</option>');

      echo('<option value="Alta">Alta</option>');
      echo('<option value="Média">Média</option>');
      echo('<option value="Baixa">Baixa</option>');
        

?>     
</select>
<br><br>

                     <label>Criado por: </label>  <br>  
                    <input  class="form-control required-field" type = "text" name = "criado_por"  required style="width:100%"/><br /><br />
                   
                    <label>Data: </label>  
                    <br>
                    <input style="width:100%"  class="form-control required-field" required  value="<?php echo date("Y-m-d"); ?>"            
                    size="10" type = "date" name = "data_criacao" >
                    
<br><br>

                    
                    <div  style=" text-align:center;width:100%"> 
                     
                    <button type="submit" class="btn-submit">
                                        <i class="bi bi-check-circle-fill me-2"></i>
                                        &nbsp;Inserir tarefa a realizar
                                    </button>
                     </div>
                 </form>
    </div>

                 <div class="text-center mt-3">
                            <a class="btn btn-secondary" title="Voltar" href="<?php echo SVRURL ?>tarefas.php?x=<?php echo base64_encode (1)?>&&z=<?php echo base64_encode (1)?>&&esi=<?php echo base64_encode ($idescola) ?>">
                                <i class="bi bi-arrow-left"></i> Voltar
                            </a>
                            <br>  <br>
                        </div>


                
               
               </div>
            </div>
         </div>
      </div>
      <!-- end about -->
    



      <?php include ("footer.php");?>



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

   </body>
</html>