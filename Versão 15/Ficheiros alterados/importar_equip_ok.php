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
                     <h2>Importar Equipamentos</h2>
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
        window.location = "<?php echo SVRURL ?>importar_equip.php";
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
                       
                                           
                            $nomeeq=$column[0];
                            $nserie=$column[1];
                            //$sala=$column[2];
                            $marca=$column[2];
                            $tipo=$column[3];

                             $proc=$column[4];
                             $mem=$column[5];
                             $disco=$column[6];
                             $plg=$column[7];
                             $pls=$column[8];
                             $plr=$column[9];
                             $mon=$column[10];
                             $tec=$column[11];
                             $tecinterface=$column[12];
                             $rato=$column[13];
                             $ratointerface=$column[14];
                             $col=$column[15];
                             $cddvd=$column[16];

                            $dom=$column[17];
                            $ip=$column[18];
                            $mas=$column[19];
                            $gat=$column[20];
                            $dns1=$column[21];
                            $dns2=$column[22];
                            $dcomp=$column[23];
                            $obs=$column[24];
    
                            $esdig=$column[25];
                            $numid=$column[26];
                            $forn=$column[27];
                            $emailforn=$column[28];
                            $nifpessoa=$column[29];



                            $sql2 = "select count(*) from tipos_equipamento where nome='".$tipo."'";
                            $result2 = mysqli_query($db,$sql2); 
                            $rows2 =mysqli_fetch_row($result2);
                            
                            $contateq = $rows2[0];
                             
                            if ($contateq==0)
                            {
                                $sql = "insert into tipos_equipamento (nome) values ('".$tipo."')";
                                $result = mysqli_query($db,$sql);

                            }
                            

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

                            $sql3 = "select count(*) from equipamento where nomeequi='".$nomeeq."' and id_sala=$idsala";
                            $result3 = mysqli_query($db,$sql3); 
                            $rows3 =mysqli_fetch_row($result3);
                            
                            $contaeq = $rows3[0];

                            if ($contaeq==0)
                            {


                    

                            $query = "insert into equipamento (nomeequi,id_sala,tipo,marca_modelo,numserie,processador,memoria,
                            disco,placagrafica,placarede,placasom,monitor,teclado,tecladointerface,rato,ratointerface,colunas,cd_dvd,dominio,ip,
                            mascara_rede,gateway,dns_principal,dns_alternativo,data_compra,observacoes,
                            escola_digital,num_inv_dgest,fornecedor,email_fornecedor,nif_pessoa) 
                             values ('$nomeeq',$idsala,'$tipo','$marca','$nserie','$proc','$mem',
                             '$disco','$plg','$plr','$pls','$mon','$tec','$tecinterface','$rato','$ratointerface','$col','$cddvd','$dom','$ip',
                             '$mas','$gat','$dns1','$dns2',STR_TO_DATE('".$dcomp."','%Y-%m-%d'),'$obs',
                             '$esdig','$numid','$forn','$emailforn','$nifpessoa') ";
                            
                            
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