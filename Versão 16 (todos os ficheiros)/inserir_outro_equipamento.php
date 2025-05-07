<?php
  session_start();
  session_regenerate_id();
  ?>
<!DOCTYPE html>
<html lang="pt">
   <head>
      


   <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.8.0/font/bootstrap-icons.css">

<style>
     .form-container {
         background-color: #f8f9fa;
         border-radius: 10px;
         padding: 25px;
         box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
         margin-bottom: 30px;
     }
     
     .form-title {
         color: #0d6efd;
         margin-bottom: 20px;
         font-weight: 600;
     }
     
     .form-group {
         margin-bottom: 20px;
     }
     
     .form-control:focus {
         border-color: #0d6efd;
         box-shadow: 0 0 0 0.2rem rgba(13, 110, 253, 0.25);
     }
     
     .required-field {
         background-color: #e8f4f8 !important;
     }
     
     .btn-submit {
         background-color: #0d6efd;
         color: white;
         padding: 10px 30px;
         border-radius: 5px;
         border: none;
         font-weight: 500;
         transition: all 0.3s;
     }
     
     .btn-submit:hover {
         background-color: #0b5ed7;
         transform: translateY(-2px);
     }
     
     .step-indicator {
         margin-bottom: 20px;
         padding: 10px;
         background-color: #f1f8ff;
         border-radius: 5px;
         border-left: 4px solid #0d6efd;
     }
     
     .help-icon {
         color: #6c757d;
         font-size: 16px;
         cursor: pointer;
     }
     
     .help-icon:hover {
         color: #0d6efd;
     }
     
     .loader_bg {
         background-color: rgba(255, 255, 255, 0.9);
     }
 </style>


<?php

 include ("head.php");
?>

   </head>


   <!-- body -->
   <body class="main-layout">
      <!-- loader  -->
      <div class="loader_bg">
         <div class="loader"><img src="images/loading.gif" alt="Loading" /></div>
      </div>
      <!-- end loader -->


     <?php include ("header.php");?>
     


     <?php
//session_start();



include("sessao_timeout.php");

//include("verifica_sessao.php");

//print_r(scandir(session_save_path()));

$sql2a =  $db->prepare("select max(id) as me  from escolas ");
//$result2a = mysqli_query($db,$sql2a); 
//$rows2a =mysqli_fetch_row($result2a);


$sql2a->execute();
$rows2a = $sql2a->get_result()->fetch_row();

$maxesc = $rows2a[0];

//echo $maxesc;
$x=base64_decode($_GET["x"]);

//echo $x;


if ($x>1 || $x<0 || base64_decode($_GET["ies"])>$maxesc)
{

?>


<script>

window.setTimeout(function() {
             // window.location.href = '<?php echo SVRURL ?>equip';
          },10);
          </script>


<?php
}


if ($x==1)
{
$idescola= base64_decode($_GET["ies"]);
}
elseif ($x==0)
{
$idescola= base64_decode($_GET["ies"]);

}
 

$sql11 = $db->prepare("select nome_escola from escolas where id=?");
$sql11->bind_param("i", $idescola);
$sql11->execute();


$rows11= $sql11->get_result()->fetch_row();
$ne = $rows11[0];


  ?>
      
      <!-- about -->
      <div  class="about">
         <div class="container">
            <div class="row">
               <div class="col-md-12">
               <a href="#" class="btn btn-secondary disabled">Outro Equipamento >> Inserir</a>
               <!--
               <div class="titlepage">
                     <h2>Outro equipamento<br>
                     <?php echo $ne ?>
                  </h2>
                  </div>
-->

               </div>
            </div>
            
            <div class="container">
               <div class="row">
                  <div class="col-md-9 offset-md-2">
              
<div class="welcome-section">                   
<?php
include("msg_bemvindo.php");
?>
 </div>


 <form name="equipamentoout" action = "<?php echo SVRURL ?>gravaoutequip?ies=<?php echo base64_encode($idescola);?>" 
 method = "post" class="needs-validation" novalidate>



 <div class="form-container">
                
                <h1 class="text-primary">
                <?php echo $ne ?></h1>
                
                 <div class="step-indicator">

                

                        <i class="bi bi-info-circle-fill me-2"></i>
                        Complete todos os campos obrigatórios (indicados com fundo azul claro)
                    </div>

                    <script language="javascript" type="text/javascript">
                        function showescdig(escola) {
                            document.frme.submit();
                        }
                    </script>


<div class="form-group row">
                                    <label for="sala" class="col-sm-4 col-form-label">Sala:</label>
                                    <div class="col-sm-8">
                                        <div class="input-group">
                                            <?php
                                            $sql = $db->prepare("select * FROM salas where id_escola=? order by nome");
                                            $sql->bind_param("i", $idescola);
                                            $sql->execute();
                                            $result = $sql->get_result();
                                            $rowcount = mysqli_num_rows($result);
                                            ?>

                                            <select name="sala" id="sala"  required class="form-control required-field" >
                                                <?php
                                                if ($rowcount > 0) {
                                                    echo('<option value=""> -- Selecione -- </option>');  
                                                    
                                                    while($row2 = mysqli_fetch_array($result)) {
                                                        echo('<option value="'.$row2['id'].'">'.$row2['nome'].'</option>');
                                                    }
                                                } else {
                                                    echo('<option value="">Sem salas disponíveis</option>');
                                                }
                                                ?>
                                            </select>
                                            <a style="color:Gainsboro;" class="btn btn-outline-secondary" href="<?php echo SVRURL ?>sala?x=<?php echo base64_encode(1) ?>&&esi=<?php echo base64_encode($idescola) ?>" title="Gerir salas">
                                                <i class="bi bi-door-open"></i> Salas
                                            </a>
                                        </div>
                                        <?php if ($rowcount == 0) { ?>
                                            <div class="alert alert-warning mt-2">
                                                <i class="bi bi-exclamation-triangle"></i> A instituição não tem salas definidas.
                                            </div>
                                        <?php } ?>
                                    </div>
                                </div>

                                <div class="form-group row">
                                    <label for="nomeq" class="col-sm-4 col-form-label">Nome:</label>
                                    <div class="col-sm-8">
                                        <input required type="text" name="nomeq" id="nomeq" class="form-control required-field" placeholder="Nome do equipamento">
                                    </div>
                                </div>

                                <div class="form-group row">
                                    <label for="quantidade" class="col-sm-4 col-form-label">Quantidade:</label>
                                    <div class="col-sm-7">
                                        <input required type="number" name="qta" id="quantidade" class="form-control required-field" placeholder="Quantidade">
                                    </div>
                                </div>







                                <div class="form-group row">
                                    <label for="obs" class="col-sm-4 col-form-label">Observações:</label>
                                    <div class="col-sm-7">
                                        <textarea rows="4" name="obs" id="obs" class="form-control" placeholder="Observações adicionais"></textarea>
                                    </div>
                                </div>


                                <div class="text-center mt-4">
                                    <button type="submit" class="btn-submit">
                                        <i class="bi bi-check-circle-fill me-2"></i>
                                        &nbsp;Inserir outro equipamento
                                    </button>
                                </div>
                            </form>
                        </div>

                        <div class="text-center mt-3">
                            <a class="btn btn-secondary" title="Voltar" href="<?php echo SVRURL ?>equip">
                                <i class="bi bi-arrow-left"></i> Voltar
                            </a>
                            <br>  <br>
                        </div>








                    
                  </div>
               </div>
            </div>
         </div>
      </div>
      <!-- end about -->
    
<?php
      // Clear the session
		unset($_SESSION['escola']);
?>

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