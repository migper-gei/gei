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



include("sessao_timeout.php");

 



$sql2a = "select max(id) as me  from escolas ";
$result2a = mysqli_query($db,$sql2a); 
$rows2a =mysqli_fetch_row($result2a);


$maxesc = $rows2a[0];


$idescola=base64_decode($_GET['ie']);

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
               <a href="#" class="btn btn-secondary disabled">CONFIGURAÇÕES >> SALAS >> INSERIR</a>
               <div class="titlepage">
                     <h2><?php echo $ne ?></h2>
                  </div>
               </div>
            </div>
            
            <div class="container">
               <div class="row">
                  <div class="col-md-7 offset-md-3">
              

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




   <form action = "<?php echo SVRURL ?>gravasala?ie=<?php echo base64_encode($idescola)?>" method = "post" >

   <input type="hidden" name="token" value="<?php echo $token; ?>" >
<br>  
                    <label>Nome da sala: </label>  <br>  
                    <input  type = "text" name ="nome"  required style="background-color:#CEF6CE;width:100%;"/><br /><br />
                 
                     <label>Localização: </label>  <br>  
                    <input  type = "text" name ="localizacao"  required style="background-color:#CEF6CE;width:100%;"/><br /><br />
                   
                    <label>Departamento / Grupo / Serviço: </label>  <br>  
                    <input  style="background-color:#CEF6CE;width:100%;" type = "text" name ="departamento"  /><br /><br />
                    
                    <label>Equipamento requisitável: </label>                

                    <select required name="eqreq" required style="background-color:#CEF6CE;">

<?php
      echo('<option selected value=""></option>');
      echo('<option value="Sim">Sim</option>');
      echo('<option  value="Não">Não</option>');
   
?>     
</select>


                    
                    <div  style=" text-align:center;width:100%"> <input  type = "submit" value = "Inserir"/>   
    </div>
                 </form>

<form action = "<?php echo SVRURL ?>sala?x=<?php echo base64_encode(1)?>&&esi=<?php echo base64_encode($idescola) ?>" method="post" >
<input type = "hidden"  value = "<?php echo $sa?>">
<input title="Voltar" type=image 
src="<?php echo SVRURL ?>images/voltar.svg"  >

</form>



<br>
                    </div>
               
               </div>
            </div>
         </div>
      </div>
      <!-- end about -->
    


      <?php include ("footer.php");?>


   </body>
</html>