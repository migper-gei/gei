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
               <div class="titlepage">
                     <h2>Importar equipamento</h2>
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
        window.location = "<?php echo SVRURL ?>importarusers";
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
        window.location = "<?php echo SVRURL ?>importar_equip.php";
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
                       
                        $column = array_map("utf8_encode", $column); 
                                          
                         if ($row>1)
                         {
                       
                                           
                            $nomeeq=$column[0];
                            $nserie=$column[1];
                            $sala=$column[2];
                            $marca=$column[3];
                            $tipo=$column[4];

                             $proc=$column[5];
                             $mem=$column[6];
                             $disco=$column[7];
                             $plg=$column[8];
                             $pls=$column[9];
                             $plr=$column[10];
                             $mon=$column[11];
                             $tec=$column[12];
                             $rato=$column[13];
                             $col=$column[14];
                             $cddvd=$column[15];

                            $dom=$column[16];
                            $ip=$column[17];
                            $mas=$column[18];
                            $gat=$column[19];
                            $dns1=$column[20];
                            $dns2=$column[21];
                            $dcomp=$column[22];
                            

                            $sql2 = "select count(*) from tipos_equipamento where nome='".$tipo."'";
                            $result2 = mysqli_query($db,$sql2); 
                            $rows2 =mysqli_fetch_row($result2);
                            
                            $contateq = $rows2[0];
                             
                            if ($contateq==0)
                            {
                                $sql = "insert into tipos_equipamento (nome) values ('".$tipo."')";
                                $result = mysqli_query($db,$sql);

                            }
                            
                            $sql2a = "select count(*) from salas where nome='".$sala."'";
                            $result2a = mysqli_query($db,$sql2a); 
                            $rows2a =mysqli_fetch_row($result2a);
                            
                            $contasa = $rows2a[0];
                             
                            if ($contasa==0)
                            {
                                $sql = "insert into salas (nome) values ('".$sala."')";
                                $result = mysqli_query($db,$sql);

                            }


                            $sql3 = "select count(*) from equipamento where nomeequi='".$nomeeq."'";
                            $result3 = mysqli_query($db,$sql3); 
                            $rows3 =mysqli_fetch_row($result3);
                            
                            $contaeq = $rows3[0];

                            if ($contateq==0)
                            {
                            $query = "insert into equipamento (nomeequi,sala,tipo,marca_modelo,numserie,processador,memoria,
                            disco,placagrafica,placarede,placasom,monitor,teclado,rato,colunas,cd_dvd,dominio,ip,mascara_rede,
                            gateway,dns_principal,dns_alternativo,data_compra) 
                             values ('$nomeeq','$sala','$tipo','$marca','$nserie','$proc','$mem','$disco',
                             '$plg','$pls','$plr','$pmon','$tec','$rato','$col','$cddvd','$dom','$ip',
                             '$mas','$gat','$dns1','$dns2',STR_TO_DATE('".$dcomp."','%Y-%m-%d'))";
                            
                            
                             $result = mysqli_query($db,$query);
                            }
                          
                        }
                            $row=$row+1;
                      
                           
        
                      
        
        
                    }
        
                    //echo("O ficheiro CSV foi importado."); 
                     fclose($file1);
                }
        
        
              /*
                if (!$result = mysqli_query($db, $query)) {
                    exit(mysqli_error($db));
                }
                $message = " O ficheiro CSV foi importado.";
                */
        

                
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


<form enctype="multipart/form-data" method="post" action="<?php echo SVRURL ?>importar_equip.php">
                <div class="form-group">
                    <label for="file">Escolha o ficheiro .CSV para importar</label>
                    <input name="file" type="file" class="form-control">
                </div>
                <div class="form-group">
                    <?php echo $message; ?>
                </div>
                <br>
                <div class="form-group">
                <div  style=" text-align:center;width:90%"> <input name="submit" type = "submit" value = "Importar"/>   
    </div>
                         
                 
                                       
                 </form>

                 <form action = "<?php echo SVRURL ?>periodos" method="post" >
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