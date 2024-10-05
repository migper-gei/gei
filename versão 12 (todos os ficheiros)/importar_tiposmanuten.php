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



include("sessao_timeout.php");

//include("verifica_sessao.php");

//print_r(scandir(session_save_path()));


 
  ?>
      
      <!-- about -->
      <div  class="about">
         <div class="container">
            <div class="row">
               <div class="col-md-12">
               <a href="#" class="btn btn-secondary disabled">CONFIGURAÇÕES >> TIPOS DE MANUTENÇÃO >> IMPORTAR</a>
               <div class="titlepage">
              
                  </div>
               </div>
            </div>
            
            <div class="container">
               <div class="row">
                  <div class="col-md-8 offset-md-3">
              
                        

<?php
include("msg_bemvindo.php");
?>
    

  
    <?php

//include("config.php");



$message = "";
if (isset($_POST['submit'])) {
    $allowed = array('csv');
    $filename0 = $_FILES['file']['name'];
    $ext = pathinfo($filename0, PATHINFO_EXTENSION);
    if (!in_array($ext, $allowed)) {
        
        //$message = 'Ficheiro inválido, deve ser .CSV!';

?>




<script>
    
    swal({
title: 'Ficheiro inválido, deve ser .CSV!',
//text: 'Os dados foram guardados!',
icon: 'error',
//buttons: false,    
//position: 'top-rigth',

})
.then(function() {
window.location = "<?php echo SVRURL ?>importar_tiposmanuten.php";
})
;


</script>

<?php



    } else 
    {
 
        include("validar_delimitadorCSV.php");


        if ($d<>",")
        {
      //  echo "<font color=navy font face='courier' size='5pt'>O ficheiro CSV não tem como delimitador a , (vírgula).</font>";
       // echo('<br><br>');
       
       ?>




       <script>
           
           swal({
       title: 'O ficheiro CSV não tem como delimitador a , (vírgula)!',
       //text: 'Os dados foram guardados!',
       icon: 'error',
       //buttons: false,    
       //position: 'top-rigth',
       
       })
       .then(function() {
       window.location = "<?php echo SVRURL ?>importar_tiposmanuten.php";
       })
       ;
       
       
       </script>
       
       <?php
    
    
    
    }
        else
        {
     
                $fileName1 = $_FILES["file"]["tmp_name"];
        
                
                if ($_FILES["file"]["size"] > 0) {
                
                    $row=1;
        
                    $file1 = fopen($fileName1, "r");
                 
                    
                    while (($column = fgetcsv($file1, 10000, ",")) !== FALSE) {
                       
                        //aa
                        $column = array_map("utf8_encode", $column); 
                                          
                         if ($row>1)
                         {
                       
                                           
                            $nome=$column[0];
                         
                           
                            
                      
                            $sql = "select nome from tipos_manutencao where nome = '$nome' ";
                            $result = mysqli_query($db,$sql);
                            $count = mysqli_num_rows($result);  

                            //echo($count);

                           // echo($nome);
                            //echo($local);
                            //echo($depart);
                            
                           if ($count==0)
                           {
                                                               
                            $query = "insert into tipos_manutencao (nome) values ('$nome')";
                            $result = mysqli_query($db,$query);
                            }
                            
                        }
                            $row=$row+1;
                      
                           
        
                        //
        
        
                    }
                        fclose($file1);
                    
                }
   
        
      
        //echo "<font color=navy font face='courier' size='5pt'>O ficheiro foi importado com sucesso.</font>";
        //echo('<br><br>');


        ?>
        


        <script>
    
    swal({
title: 'O ficheiro foi importado com sucesso!',
//text: 'Os dados foram guardados!',
icon: 'success',
//buttons: false,    
//position: 'top-rigth',

})
.then(function() {
window.location = "<?php echo SVRURL ?>tiposmanuten";
})
;


</script>

        <?php
                
            }
        }
        
        
        }






?>
<br><br>

<form enctype="multipart/form-data" method="post" action="<?php echo SVRURL ?>importar_tiposmanuten.php">
                <div class="form-group">
                    <label for="file">Escolha o ficheiro .CSV para importar (Caso já exista o tipo de manutenção, não é importada)</label>
                    <input name="file" type="file" class="form-control">
                </div>
                <div class="form-group">
                    <?php echo $message; ?>
                </div>
                <br><br>
                <div class="form-group">
                <div  style=" text-align:center;width:100%"> <input name="submit" type = "submit" value = "Importar"/>   
               </div>
            </form>




                 <form action = "<?php echo SVRURL ?>configura" method="post" >
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