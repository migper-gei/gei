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

//include("verifica_sessao.php");

//print_r(scandir(session_save_path()));


 
  ?>
      
      <!-- about -->
      <div  class="about">
         <div class="container">
            <div class="row">
               <div class="col-md-12">
               <a href="#" class="btn btn-secondary disabled">Configurações >> Salas >> Importação</a>
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
window.location = "<?php echo SVRURL ?>importarsalas";
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
       window.location = "<?php echo SVRURL ?>importarsalas";
       })
       ;
       
       
       </script>
       
       <?php
    
    
    
    }
        else
        {
     
           $idescola=$_POST['escola'];
           //echo $idescola;


                $fileName1 = $_FILES["file"]["tmp_name"];
        
                
                if ($_FILES["file"]["size"] > 0) {
                
                    $row=1;
        
                    $file1 = fopen($fileName1, "r");
                 
                    
                    while (($column = fgetcsv($file1, 10000, ",")) !== FALSE) {
                       
                        //aa
                        $column = array_map("utf8_encode", $column); 
            
                   
                        $column = mb_convert_encoding($column, 'ISO-8859-1');

                   

            
                         if ($row>1)
                         {
                       
                                           
                            $nome=$column[0];
                            $local=$column[1];
                            $depart=$column[2];
                            $eqreq=$column[3];
                            
                      
                            $sql = "select nome from salas where nome = '$nome' and id_escola=$idescola";
                            $result = mysqli_query($db,$sql);
                            $count = mysqli_num_rows($result);  

                            //echo($count);

                           // echo($nome);
                            //echo($local);
                            //echo($depart);
                            
                           if ($count==0)
                           {
                                                               
                            $query = "insert into salas (nome,localizacao,departamento,id_escola,equip_requisitavel) 
                            values ('$nome','$local','$depart',$idescola,'$eqreq')";
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
window.location = "<?php echo SVRURL ?>sala?x=<?php echo base64_encode(1) ?>&&escola=<?php echo base64_encode($idescola) ?>";
})
;


</script>

        <?php
                
            }
        }
        
        
        }






?>

<form class="needs-validation" novalidate enctype="multipart/form-data" method="post" action="<?php echo SVRURL ?>importarsalas">
                
<div class="action-section">
    
    <h2 class="section-title"><i class="fas fa-school btn-icon"></i> Selecione a Instituição</h2>
    
    
<select  name="escola" required class="form-control required-field"  style="width:100%;">


<?php
 $sql = "SELECT * FROM escolas ORDER by nome_escola";
 $result = mysqli_query($db,$sql);
 $rowcount = mysqli_num_rows($result);

while($row=mysqli_fetch_array($result))
{


  echo('<option value="'.$row['id'].'">'.$row['nome_escola'].'</option>');


}


echo('</select>');

?>
</diV>
<br>

                <div class="form-group">

                <div class="action-section">
                    <label for="file">Escolha o ficheiro .CSV para importar (Caso já exista a sala, não é importada)</label>
                    <input name="file" type="file"  class="form-control required-field" required>
</div>
                </div>
                <div class="form-group">
                    <?php echo $message; ?>
                </div>
                <br>
                <div class="form-group">
                <div  style=" text-align:center;width:100%"> 
                    
                <button type="submit" name="submit" class="btn-submit">
                <i class="fa-solid fa-file-import"></i>
                                        &nbsp;Importar salas
                                    </button>
                 
               </div>
            </form>

<bR>
            <div class="text-center mt-3">
                            <a class="btn btn-secondary" title="Voltar" href="<?php echo SVRURL ?>configura">
                                <i class="bi bi-arrow-left"></i> Voltar
                            </a>
                            <br>  <br>
                        </div>

   



                    </div>
               
               </div>
            </div>
         </div>
      </div>
      <!-- end about -->
    

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