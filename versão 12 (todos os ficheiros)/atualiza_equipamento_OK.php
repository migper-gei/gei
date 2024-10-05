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
         <div class="loader"><img src="images/loading.gif" alt="Loading" /></div>
      </div>
      <!-- end loader -->


     <?php include ("header.php");?>
     


     <?php



include("sessao_timeout.php");

 
  ?>
      
      <!-- about -->
      <div  class="about">
         <div class="container">
            <div class="row">
               <div class="col-md-12">
               <a href="#" class="btn btn-secondary disabled">CONFIGURAÇÕES >> EQUIPAMENTO >> ATUALIZAR</a>
               <div class="titlepage">
       
                  </div>
               </div>
            </div>
            
            <div class="container">
               <div class="row">
                  <div class="col-md-10 offset-md-1">
              
                    
<br>


<?php
if($_SERVER["REQUEST_METHOD"] == "POST") {
        


   $id= base64_decode($_GET['ide']);
   $sa= base64_decode($_GET['sai']);
   $ies= base64_decode($_GET['ies']);

   //echo $sa;


   $sql2a = "select escola_digital from equipamento where id=$id";
   $result2a = mysqli_query($db,$sql2a); 
   $rows2a =mysqli_fetch_row($result2a);
   
   
   $ed = $rows2a[0];

   //echo $ed;


   if ($ed=="Sim")
{
   $sql = "update equipamento 
   set nomeequi='".$_POST["nomeq"]."',
   id_sala=".$_POST["sala"].",
   tipo='".$_POST["tipoeq"]."',
   marca_modelo='".$_POST["marcamod"]."',
   numserie='".$_POST["nserie"]."',
   data_compra='".$_POST["datacompra"]."',
   observacoes='".$_POST["obs"]."',
   num_inv_dgest='".$_POST["numinv"]."',
   fornecedor='".$_POST["fornecedor"]."',
   email_fornecedor='".$_POST["emailfornecedor"]."',
   nif_pessoa=".$_POST["nifpessoa"]."
   where id=".$id." ";

}
else
{
$sql = "update equipamento 
set nomeequi='".$_POST["nomeq"]."',
id_sala=".$_POST["sala"].",
tipo='".$_POST["tipoeq"]."',
marca_modelo='".$_POST["marcamod"]."',
numserie='".$_POST["nserie"]."',
data_compra='".$_POST["datacompra"]."',
observacoes='".$_POST["obs"]."'
where id=".$id." ";
}

$result = mysqli_query($db,$sql);




mysqli_close($db);

//$id=$_GET['id'];
?>

<script>
    
    swal({
title: 'Os dados foram atualizados!',
//text: 'Os dados foram guardados!',
icon: 'success',
//buttons: false,

})
.then(function() {
window.location = "<?php echo SVRURL ?>atualiza_dadostecredes.php?ide=<?php echo base64_encode($id);?>&&sa=<?php echo base64_encode($sa);?>&&ies=<?php echo base64_encode($ies);?>";
});



</script>

<br><br><br><br><br><br><br><br><br><br>
<?php

}

?>
<br><br>


                    
                  </div>
               </div>
            </div>
         </div>
      </div>
      <!-- end about -->
    


      <?php include ("footer.php");?>


   </body>
</html>