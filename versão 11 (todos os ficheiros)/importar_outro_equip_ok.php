<!DOCTYPE html>
<html lang="pt">
   <head>
      

<?php

 include ("head.php");
?>

   </head>


   <!-- body -->
   <body class="main-layout">
      <!-- loader  
      <div class="loader_bg">
         <div class="loader"><img src="<?php echo SVRURL ?>images/loading.gif" alt="Loading" /></div>
      </div>-->
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
               <div class="titlepage">
                     <h2>Importar outros equipamentos</h2>
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
        window.location = "<?php echo SVRURL ?>importar_outro_equip.php";
        })
        ;
        
        
        </script>
        
        <?php

    } else 
    {
 
        include("validar_delimitadorCSV.php");

        if ($d<>",")
        {
        //echo "<font color=navy font face='courier' size='5pt'>O ficheiro CSV não tem como delimitador a , (vírgula).</font>";
        //echo('<br><br>');

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
        window.location = "<?php echo SVRURL ?>importar_outro_equip.php";
        })
        ;
        
        
        </script>
        
        <?php
     


        }
        else
        {
     

            $idsala=$_POST['sala'];

            // echo $idsala;





                $fileName1 = $_FILES["file"]["tmp_name"];
        
                
                if ($_FILES["file"]["size"] > 0) {
                
                    $row=1;
        
                    $file1 = fopen($fileName1, "r");
                 
                    
                    while (($column = fgetcsv($file1, 10000, ",")) !== FALSE) {
                       
                        $column = array_map("utf8_encode", $column); 
                                          
                         if ($row>1)
                         {
                       
                                           
                            $nomeout=$column[0];
                            $qta=$column[1];
                            $obs=$column[2];
                           
                           



                            /*
                            $sql2a = "select count(*) from salas where nome='".$sala."'";
                            $result2a = mysqli_query($db,$sql2a); 
                            $rows2a =mysqli_fetch_row($result2a);
                            
                            $contasa = $rows2a[0];
                             
                            if ($contasa==0)
                            {
                                $sql = "insert into salas (nome) values ('".$sala."')";
                                $result = mysqli_query($db,$sql);

                            }
                           */

                            $sql3 = "select count(*) from outro_equipamento where nomeoutro='".$nomeout."' and id_sala=$idsala";
                            $result3 = mysqli_query($db,$sql3); 
                            $rows3 =mysqli_fetch_row($result3);
                            
                            $contaeq = $rows3[0];

                            if ($contaeq==0)
                            {


                    

                            $query = "insert into outro_equipamento (id_sala,nomeoutro,qta,observacoes) 
                             values ($idsala,'$nomeout',$qta,'$obs') ";
                            
                            
                             $result = mysqli_query($db,$query);
                            }
                          
                        }
                            $row=$row+1;
                      
                           
        
                      
        
        
                    }
        
                    //echo("O ficheiro CSV foi importado."); 
                     fclose($file1);
                }
        
        
        

                
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
window.location = "<?php echo SVRURL ?>configura";
})
;



</script>

        <?php
                
            }
        }
        
        
        }






?>
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