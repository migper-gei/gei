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
        
$id=base64_decode($_GET["id"]);
//echo ($id);


if ( $_SESSION['tipo']==1)
{


    $sql0 = "update equipamento set processador='".$_POST["cpu"]."',
        memoria='".$_POST["ram"]."',
        disco='".$_POST["disco"]."',
        placagrafica='".$_POST["grafica"]."',
        placarede='".$_POST["rede"]."',
        placasom='".$_POST["som"]."',
    monitor='".$_POST["monitor"]."',
    teclado='".$_POST["teclado"]."',
    rato='".$_POST["rato"]."',
    colunas='".$_POST["colunas"]."',
    cd_dvd='".$_POST["cddvd"]."'
 
        where id=".$id." ";
     
    
    $result0 = mysqli_query($db,$sql0);




    $sql = "update equipamento set 
    dominio='".$_POST["dominio"]."',
    ip='".$_POST["ip"]."',
    mascara_rede='".$_POST["mascara"]."',
    gateway='".$_POST["gateway"]."',
    dns_principal='".$_POST["dnsp"]."',
    dns_alternativo='".$_POST["dnsa"]."'
    
        where id=".$id." ";
     
    
    $result = mysqli_query($db,$sql);

mysqli_close($db);
?>

<script>
    
    swal({
title: 'Os dados foram atualizados!',
//text: 'Os dados foram guardados!',
icon: 'success',
//buttons: false,

})
.then(function() {
window.location = "<?php echo SVRURL ?>equip";
});


</script>


<br><br><br><br><br><br><br><br><br><br>

<?php
}


else

{
?>
    <script>
window.setTimeout(function() {
    window.location.href = 'equip';
}, 10);
</script>


<?php

}



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