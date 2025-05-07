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
      

      <?php
      //echo base64_decode($_GET['url']);

if (isset($_GET['url'])  &&  is_numeric(base64_decode($_GET['url'])) )
{
$url = explode('/',$_GET['url']);

//echo $url[0];
}
else
{
    ?>
<script>
window.setTimeout(function() {
    window.location.href = '<?php echo SVRURL ?>sala?x=0';
}, 10);
</script>
<?php
}
?>


<?php
$sql2 = "select count(*) from salas where id=".base64_decode($url[0])."";
$result2 = mysqli_query($db,$sql2); 
$rows2 =mysqli_fetch_row($result2);

$conta = $rows2[0];


if ($conta==0)
{

?>

<script>
window.setTimeout(function() {
    window.location.href = '<?php echo SVRURL ?>sala?x=0';
}, 10);
</script>




<?php
}

$sql11 = "select e.nome_escola from escolas e, salas s
where s.id_escola=e.id and
s.id=".base64_decode($url[0])."";
$result11 = mysqli_query($db,$sql11); 
$rows11 =mysqli_fetch_row($result11);




?>


      <!-- about -->
      <div  class="about">
         <div class="container">
            <div class="row">
               <div class="col-md-12">
               <a href="#" class="btn btn-secondary disabled">Configurações >> Salas >> Atualizar</a>
               <div class="titlepage">
                     <h2> <?php echo $rows11[0] ?></h2>
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

 $sql = "select * from salas where id=".base64_decode($url[0])."";
 $result = mysqli_query($db,$sql);
 $row=mysqli_fetch_array($result);

 //echo($row['nome']);
 //echo $url[0];
?>
            <a href="<?php echo SVRURL ?>sair">Sair</a>
              </h3>   
<br>

<?php
if (mysqli_num_rows($result)==0)
{

   // header("Refresh:0;url=salas");
?>


   <script>
window.setTimeout(function() {
    window.location.href = '<?php echo SVRURL ?>sala?x=<?php echo base64_encode(1);?>&&escola=<?php echo $row['id_escola']?>';
}, 10);
</script>

<?php
}
else
{ 
?>

<div class="form-container">
<form 
action = "<?php echo SVRURL ?>atualiza_ok_sala.php?sai=<?php echo base64_encode($row['id']); ?> " method = "post" class="needs-validation" novalidate>
                    <label>Nome da sala: </label>  <br>  
                    <input class="form-control required-field" style="width:100%;" type = "text" name ="nome"  required value="<?php echo $row['nome']; ?>"/><br /><br />
                 
                     <label>Localização: </label>  <br>  
                    <input class="form-control required-field" style="width:100%;" type = "text" name ="localizacao"  required value="<?php echo $row['localizacao']; ?>"/><br /><br />
                
                    <label>Departamento/Grupo/Serviço: </label>  <br>  
                    <input class="form-control required-field" style="width:100%;" type = "text" name ="departamento"   value="<?php echo $row['departamento']; ?>"/><br /><br />
                    <label>Sala com equipamento requisitável: </label>                

                    <select name="eqreq" required class="form-control required-field">

<?php
if ($row['equip_requisitavel']=="Sim")
{

      echo('<option selected value="Sim">Sim</option>');
      echo('<option value="Não">Não</option>');
}
else
{
      echo('<option selected value="Não">Não</option>');
      echo('<option value="Sim">Sim</option>');
} 
?>     
</select>
                                 
                         <!--           (Ao atualizar, também será atualizado a sala nos respetivos equipamentos)  
                          -->
                         <br><br>

                                    <div  style=" text-align:center;width:100%"> 
                                    <button type="submit" class="btn-submit">
                                    <i class="fa-solid fa-pen"></i>
                                        &nbsp;Atualizar sala
                                    </button>
                                 
                                 </div>
                 </form>
</div>

                 <div class="text-center mt-3">
                            <a class="btn btn-secondary" title="Voltar" href="<?php echo SVRURL ?>sala?x=<?php echo base64_encode(1)?>&&esi=<?php echo base64_encode($row['id_escola'])?>">
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