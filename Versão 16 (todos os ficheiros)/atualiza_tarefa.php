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

if (isset($_GET['url']))
{
$url = explode('/',$_GET['url']);
$url2 = explode('/',$_GET['url2']);

//echo $url[0];

$idta=base64_decode($url[0]);
$idescola=base64_decode($url2[0]);

//echo (isset($_GET['url']));
//echo ($idta);

if (  empty($idescola) ||  !isset($idescola) || !is_numeric($idescola)  
|| empty($idta) ||  !isset($idta) || !is_numeric($idta)  )
{
?>
  <script>

  window.setTimeout(function() {
               window.location.href = '<?php echo SVRURL ?>tarefas.php?x=<?php echo base64_encode(0)?>';
            },10);
            </script>
<?php
}






$sql11 = "select nome_escola from escolas 
where id=$idescola";
$result11 = mysqli_query($db,$sql11); 
$rows11 =mysqli_fetch_row($result11);

$num_rows = mysqli_num_rows($result11);

//echo ($num_rows);

}
else
{
    ?>
<script>
window.setTimeout(function() {
    window.location.href = '<?php echo SVRURL ?>tarefas?x=<?php echo base64_encode(0) ?>';
}, 10);
</script>
<?php
}


if ($num_rows==0)
{
?>

<script>
window.setTimeout(function() {
    window.location.href = '<?php echo SVRURL ?>tarefas?x=<?php echo base64_encode(0) ?>';
}, 10);
</script>


<?php

}




?>


      <!-- about -->
      <div  class="about">
         <div class="container">
            <div class="row">
               <div class="col-md-12">
               <a href="#" class="btn btn-secondary disabled">Configurações >> Tarefas a realizar >> Atualizar</a>
               <div class="titlepage">
               <h2> 
                     <?php echo $rows11[0] ?></h2>
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

 $sql = "select * from tarefas where id=".base64_decode($url[0])."";
 $result = mysqli_query($db,$sql);
 $row=mysqli_fetch_array($result);

 //echo($row['nome']);
 //echo $url[0];
?>
            <!--<a href="<?php echo SVRURL ?>sair">Sair</a>-->
              </h3>   
<br>

<?php
if (mysqli_num_rows($result)==0)
{

   // header("Refresh:0;url=salas");
?>


   <script>
window.setTimeout(function() {
    window.location.href = '<?php echo SVRURL ?>tarefas?x=<?php echo base64_encode(1) ?>&&esi=<?php echo base64_encode($idescola)?>&&x=<?php echo base64_encode(1) ?>&&z=<?php echo base64_encode(1) ?>';
}, 10);
</script>

<?php
}
else
{ 
?>

<div class="form-container">


<form action = "<?php echo SVRURL ?>atualiza_ok_tarefa.php?ti=<?php echo base64_encode ($row['id']); ?>&&esi=<?php echo base64_encode ($idescola)?>" method = "post" class="needs-validation" novalidate>
                    
                
 
<?php


$sqla = "SELECT  DISTINCT(nome),id 
FROM salas 
where id_escola=$idescola
order by nome";

$resulta = mysqli_query($db,$sqla);
?>

<label>Sala: </label>  


<?php 
//echo ("a:") ;
//echo $row['id_sala'];
?>

<select  style="width: 100%;
            height: 35px; "
                        class="form-control required-field" name="salatar" required>


<?php


echo('<option value="">Escolha a sala</option>');
while($rowa=mysqli_fetch_array($resulta))
{
   

    if ($rowa['id']==$row['id_sala'])
    {
        echo('<option selected value="'.$rowa['id'].'">'.$rowa['nome'].'</option>');
    }
    else
      echo('<option value="'.$rowa['id'].'">'.$rowa['nome'].'</option>');

        }




?>     
</select>

<br><br>
<label>Descrição: </label>  <br>  

                <textarea required style="width:100%" rows="5" name="descricao" class="form-control required-field">
                  <?php echo $row['descricao']?>
                </textarea>
                   <br><br>


                    <label>Urgência: </label>  
                   
<br>
<select  class="form-control required-field" style="width: 100%;
            height: 35px; " name="urgencia" required>


<?php

//echo('<option value="">Escolha a urgência</option>');

if ($row['urgencia']=='Alta')
  {  
      echo('<option selected value="Alta">Alta</option>');
      echo('<option value="Média">Média</option>');
      echo('<option value="Baixa">Baixa</option>');
  }
elseif ($row['urgencia']=='Média')
{
      echo('<option selected value="Média">Média</option>');
      echo('<option value="Alta">Alta</option>');
      echo('<option value="Baixa">Baixa</option>');
}
else
{
      echo('<option selected value="Baixa">Baixa</option>');
      echo('<option value="Média">Média</option>');
      echo('<option value="Alta">Alta</option>');
}
?>     
</select>
<br><br>

                    <label>Criado por: </label>  <br>  
                    <input size=50 type = "text" name = "criado_por"  
                    value="<?php echo $row['criado_por']; ?>"
                    required  class="form-control required-field" style="width:100%"/><br /><br />
                   
                    <label>Data criação: </label>  
                    <input  class="form-control required-field"  style="width:100%" required  
                    value="<?php echo $row['data_criacao']; ?>"            
                    size="10" type = "date" name = "data_criacao" >
                   

                    <br /><br />
                   
                    <label>Concluido por: </label>  <br>  
                    <input style="width:100%"  type = "text" name = "concluido_por"  
                    value="<?php echo $row['concluido_por']; ?>"
                     /><br /><br />
                    



                    <label>Data conclusão: </label>  
                    <label><Datal></Datal>: </label>  
                    <input  value="<?php echo $row['data_conclusao']; ?>"            
                    size="10" type = "date" name = "data_conclusao" >




                  <br> <br>


                                    <div  style=" text-align:center;width:100%"> 
                                    <button type="submit" class="btn-submit">
                                    <i class="fa-solid fa-pen"></i>
                                        &nbsp;Atualizar tarefa a realizar
                                    </button>  
                                    </div>
                 </form>

</div>

<div class="text-center mt-3">
                            <a class="btn btn-secondary" title="Voltar" href="<?php echo SVRURL ?>tarefas.php?x=<?php echo base64_encode(1) ?>&&z=<?php echo base64_encode (1)?>&&esi=<?php echo base64_encode($row['id_escola'])?>">
                                <i class="bi bi-arrow-left"></i> Voltar
                            </a>
                   
                        </div>
<!--
                 <form action = "<?php echo SVRURL ?>tarefas.php?x=<?php echo base64_encode(1) ?>&&z=<?php echo base64_encode (1)?>&&esi=<?php echo base64_encode($row['id_escola'])?>" method="post" >
<input type = "hidden" name = "sala" value = "<?php echo $sa?>">
<input title="Voltar" type=image 
src="<?php echo SVRURL ?>images/voltar.svg"  >

</form>
-->

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