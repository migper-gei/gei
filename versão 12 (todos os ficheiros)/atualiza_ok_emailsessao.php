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
      <!-- loader --> 
      <div class="loader_bg">
         <div class="loader"><img src="<?php echo SVRURL ?>images/loading.gif" alt="Loading" /></div>
      </div>
      <!-- end loader -->


     <?php include ("header.php");?>
     


     <?php
//session_start();



include("sessao_timeout.php");

//include("verifica_sessao.php");

//print_r(scandir(session_save_path()));


 
  ?>
      
      <!-- about -->
      <div  class="about">
         <div class="container">
            <div class="row">
               <div class="col-md-12">
               <a href="#" class="btn btn-secondary disabled">CONFIGURAÇÕES >> EMAIL/SESSÃO >> ATUALIZAR</a>
               <div class="titlepage">
                     <h2>Atualizar email/sessao</h2>
                  </div>
               </div>
            </div>
            
            <div class="container">
               <div class="row">
                  <div class="col-md-8 offset-md-3">
              
                        

<?php
include("msg_bemvindo.php");
?>
    
<br>




<?php

$id=base64_decode($_GET['i']);
//echo $id;


if ( !isset($id)  || !is_numeric($id)  || empty($id) 
)
{
    
?>


<script>
window.setTimeout(function() {
    window.location.href = '<?php echo SVRURL ?>emsess';
}, 10);
</script>

<?php

}
else
{





$pa=$_POST["pass"];


$sql0 = "update settings set email_user='".$_POST["email"]."',
nome_app='".$_POST["nome"]."',
pass=AES_ENCRYPT('$pa', 'secret'),
email_smtp='".$_POST["smtp"]."',
email_smtpport='".$_POST["smtpport"]."',
sessao_timeout='".$_POST["sessao"]."'
where id=".$id." ";
$result = mysqli_query($db,$sql0);


mysqli_close($db);

}
?>

<script>
    
    swal({
title: 'Os dados foram atualizados!',
//text: 'Os dados foram guardados!',
icon: 'success',
//buttons: false,

})
.then(function() {
window.location = "<?php echo SVRURL ?>emsess";
})
;


</script>



<br><br><br><br><br><br><br>
                    </div>
               
               </div>
            </div>
         </div>
      </div>
      <!-- end about -->
    


      <?php include ("footer.php");?>


   </body>
</html>