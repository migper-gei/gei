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

<script>
         function myFunction() {
           var x = document.getElementById("mypass");
           if (x.type === "password") {
             x.type = "text";
           } else {
             x.type = "password";
           }
         } 
         </script>





<script>
function a() {


  event.preventDefault(); // prevent form submit

   swal({

  title: "Deseja eliminar?",
  //text: "Tarefa da sala "+ns1+" da "+esc1,
  type: "warning",
  showCancelButton: true,
  //confirmButtonColor: "#DD6B55",


  confirmButtonText: "Sim",
  cancelButtonText: "Não",
  closeOnConfirm: false,
  closeOnCancel: false
 
},
function(isConfirm){
  if (isConfirm) {
    
        //alert(n1);
        window.setTimeout(function() {
    window.location.href = '<?php echo SVRURL ?>eliminaemsess.php';
}, 10);


          
  } else {
    swal("Cancelado.");

//    window.setTimeout(function() {
  //  window.location.href = '<?php echo SVRURL ?>salas?x=1&&escola='+es1;
//}, 10);
  

  }

});

}

</script>   



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




//echo $idescola;


?>


      
      <!-- about -->
      <div  class="about">
         <div class="container">
            <div class="row">
               <div class="col-md-12">
               <a href="#" class="btn btn-secondary disabled">Configurações >> Email/Sessão</a>
               <div class="titlepage">
                
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


      
   <?php $sql = "select * from settings";
$result = mysqli_query($db,$sql);


$sql = $db->prepare("select AES_DECRYPT(`pass`, 'secret') from settings");

$sql->execute();

$row0 = $sql->get_result()->fetch_row();
?>
    

        <!-- Datatable class="table table-striped"-->
        <table class="table table-striped" id="js-sort-table"  >
            <thead>
                <tr class="table-primary">
                   
                    <th  scope="col">Email / Sigla</th>
                    <th   scope="col">Smtp / Porta</th>
                               <th   scope="col">Duração da sessão (segundos)</th>
                    <th   scope="col">Duração da password (dias)</th>
                  
                    <th></th>
                     
                     <?php
                     $sql0 = "select count(*) from settings ";
                     $result0 = mysqli_query($db,$sql0);
                     
                     $count0 = mysqli_fetch_array($result0);
                     //echo $count0[0];
                     
                      if ($_SESSION['tipo']==1 && $count0[0]==0)
                      {
                     ?>

                    <th  colspan="3" style="text-align: center;">
                    <a class="btn btn-primary"   title="Inserir" href="<?php echo SVRURL ?>inseriremse">
                    <p style="color:white;"> 
                 
                 <i class="fa-solid fa-plus"></i> </p> </th>
                              <?php
                      }
                     ?>
                    
                </tr>
            </thead>
            <tbody>
                <?php  while($row=mysqli_fetch_array($result)) { 
            
               $pa=preg_replace("|.|","*",$row0[0]);     
                   

                    ?>
                    <td width="20%"  scope="row"><?php echo $row['email_user']; ?>
                     <br>
                     <?php echo $row['nome_app']; ?>

                <!--
                    <br>
                  <?php echo $pa; ?>
                -->
                  </td>
                
            
                
                     <td width="25%" ><?php echo $row['email_smtp']; ?> 
                     <br>
                     <?php echo $row['email_smtpport']; ?>
                  </td>
                
                
            
                    <br>
                    <td width="15%" ><?php echo $row['sessao_timeout']; ?></td>
                    <td width="15%" ><?php echo $row['tempoduracaopass']; ?></td>
                </td>
                
                    


                    <?php
                      if ($_SESSION['tipo']==1 )
                      {
                     ?>
                    
                    <td width="10%" >
                    <a title="Atualizar" href="<?php echo SVRURL ?>atualtemse/<?php echo base64_encode ($row['id']) ?>">
                    <img src="<?php echo SVRURL ?>images/atualizar.svg" alt="Atualizar" >  </a>
                  
           &nbsp;&nbsp;&nbsp;

                    <a onclick="a();" title="Eliminar" href="<?php echo SVRURL ?>eliminaemsess.php">
                  
                  <img src="<?php echo SVRURL ?>images/eliminar.svg" alt="Eliminar" > </a>
                  
                  
                  </td>
                   
                   
                
                    
                    <?php
                   }
                     ?>
                    
               
                    
                    <?php
                      }
                     ?>
                
                </tr>
               
            </tbody>
        </table>

        <!--
        <input title="Mostrar password" type="checkbox" onclick="myFunction()"> Mostrar password 
         <br> <br>
                     -->
                     <br>
<?php
include "realcelinhatabela.php";
?>


     
       
       

        <a href="<?php echo SVRURL ?>configura">
<img src="<?php echo SVRURL ?>images/voltar.svg" alt="Voltar">
</a>



<br>


        <?php include ("jquery_bootstrap.php");?>


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