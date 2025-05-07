<?php
  session_start();
  session_regenerate_id();
  ?>
<!DOCTYPE html>
<html lang="pt">
   <head>
 <style>
 .upload-container {
            background-color: #ffffff;
            border-radius: 12px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
            width: 100%;
            max-width: 600px;
            padding: 30px;
        }
        
        .upload-title {
            color: #4a5568;
            font-size: 24px;
            margin-bottom: 30px;
            text-align: center;
        }
        
        .upload-section {
            margin-bottom: 25px;
        }
        
        .upload-label {
            display: block;
            color: #64748b;
            font-size: 16px;
            margin-bottom: 10px;
        }
        
        .file-input-container {
            position: relative;
            margin-bottom: 5px;
        }
        
        .file-input {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            opacity: 0;
            cursor: pointer;
        }
        
        .file-input-button {
            background-color: #f0f2f5;
            border: 2px solid #cbd5e0;
            border-radius: 6px;
            color: #4a5568;
            cursor: pointer;
            font-size: 16px;
            padding: 12px 20px;
            text-align: left;
            transition: all 0.3s ease;
            width: 100%;
        }
        
        .file-input-button:hover {
            background-color: #e2e8f0;
        }
        
        .file-input-status {
            color: #718096;
            font-size: 14px;
            margin-top: 8px;
            font-style: italic;
        }
    </style>

<?php
 include ("css_inserir.php");

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

 
  ?>

<?php

$sql1 = "select count(*) as cs from periodos";
$result1 = mysqli_query($db,$sql1); 
$rows =mysqli_fetch_row($result1);

if ($rows[0]==0)
{
?>


<script>
    
swal({
title: 'Não tem períodos definidos!',
//text: 'Os dados foram guardados!',
icon: 'error',
//buttons: false,

})
.then(function() {
window.location = "<?php echo SVRURL ?>avaria";
});

</script>


<?php

}
?>




<?php

$sql2a = "select max(id) as me  from escolas ";
$result2a = mysqli_query($db,$sql2a); 
$rows2a =mysqli_fetch_row($result2a);


$maxesc = $rows2a[0];


if (base64_decode($_GET["aves"])>$maxesc)
{

?>


<script>

window.setTimeout(function() {
              window.location.href = '<?php echo SVRURL ?>avaria';
          },10);
          </script>


<?php
}


   $idescola=base64_decode($_GET["aves"]);
   


   if ( !isset($idescola)    || empty($idescola)     || !is_numeric($idescola) 
   )
   
   {
      //echo "aaaaaa";
   ?>
   
   
   <script>
   window.setTimeout(function() {
       window.location.href = '<?php echo SVRURL ?>avaria';
   }, 10);
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
               <a href="#" class="btn btn-secondary disabled">Avarias >> Inserir</a>
              
               </div>
            </div>
            
            <div class="container">
               <div class="row">
                  <div class="col-md-10 offset-md-2">
              
                   <!-- Welcome Section -->
 <div class="welcome-section">              
<?php
include("msg_bemvindo.php");
?>
</div>               


<script language="javascript" type="text/javascript">

function showequi(sala) {

    document.frm.submit();

}

</script>






<div class="form-container">
                
                        <h1 class="text-primary">
                        <?php echo $ne ?></h1>
                        
                         <div class="step-indicator">

                        

                                <i class="bi bi-info-circle-fill me-2"></i>
                                Complete todos os campos obrigatórios (indicados com fundo azul claro)
                            </div>





                            <form name="frm" id="frm" action = "" method = "post" >

                            <div class="form-group row">
                                
                                    <label for="sala" class="col-sm-4 col-form-label">Sala:</label>
                                    <br> 
                           
                                     
                                            <?php
                                            $sql = $db->prepare("SELECT DISTINCT(nome) as no,s.id 
FROM salas s, equipamento eq
where s.id_escola=? and s.id=eq.id_sala
order by s.nome");
                                            $sql->bind_param("i", $idescola);
                                            $sql->execute();
                                            $result = $sql->get_result();
                                            $rowcount = mysqli_num_rows($result);

                                           // echo $rowcount;
                                            ?>

                                            <select  name="sala" id="sala" class="form-select required-field" required
                                            onChange="showequi(this.value);"
                                            >
                                                <?php
                                                if ($rowcount > 0) {


                                                    echo('<option value=""> -- Selecione -- </option>');  
                                                    
                                                    while($row = mysqli_fetch_array($result)) {



                                                      if ($row['id']==$_REQUEST["sala"])
                                                      {
                                                      /*echo('<option selected value="'.$row['no'].'">'.$row['no'].'</option>');*/
                                                      echo('<option selected value="'.$_REQUEST["sala"].'">'.$row['no'].'</option>');
                                                      }
                                                      else
                                                      
                                                      
                                                      echo('<option value="'.$row['id'].'">'.$row['no'].'</option>');
                                                      
                                                      }
                                                      
                                       






                                                                                                    } else {
                                                    echo('<option value="">Sem salas disponíveis</option>');
                                                }
                                                ?>
                                            </select>




                                            <a 
                                            style="color:Gainsboro;"
                                            class="btn btn-outline-secondary" href="<?php echo SVRURL ?>sala?x=<?php echo base64_encode(1) ?>&&esi=<?php echo base64_encode($idescola) ?>" title="Gerir salas">
                                                <i class="bi bi-door-open"></i> 
                                                
                                                Salas
                                            </a>
                                        </div>
                                        <?php if ($rowcount == 0) { ?>
                                            <div class="alert alert-warning mt-2">
                                                <i class="bi bi-exclamation-triangle"></i> A instituição não tem salas definidas.
                                            </div>
                                        <?php } ?>
                               
                               


</form>



<?php 
                
               
               
                //$sa=$_POST["sala"];
           
          
              if (!empty($_POST["sala"])) {
              
              $sa=$_POST["sala"];
              
              }
              else{
               $sa=" ";
              }
             
  
         



              // $sa=$_POST["sala"];
               //$em=$_SESSION['email'];
               
               
               //echo($sa);
               //echo($em);
             

           
               

               ?>

        







<script type="text/javascript">
function validateImage() {
    var formData = new FormData();
 
    var file = document.getElementById("img").files[0];
 
    formData.append("Filedata", file);
    var t = file.type.split('/').pop().toLowerCase();
    if (t != "jpeg" && t != "jpg" && t != "png" && t != "bmp" && t != "gif") {
       // alert('Inserir um tipo de ficheiro válido.');
        
             
       swal({
       title: 'Inserir um tipo de ficheiro válido!',
       text: 'tipo: JPEG, JPG, PNG, BMP ou GIF',
       icon: 'error',
       //buttons: false,    
       //position: 'top-rigth',
       
       })
     
       ;
       
        
        
        document.getElementById("img").value = '';
        return false;
    }
  /*  if (file.size > 1024000) {
        alert('Max Upload size is 1MB only');
        document.getElementById("img").value = '';
        return false;
    }*/
    return true;
}
</script>




<script type="text/javascript">

function Filevalidation () {
        const fi = document.getElementById('file').files[0];;
   
   //alert(fi.size);
                const fsize = fi.size;

                const file = Math.round((fsize / 1024));
         
                var fileIsMp4 = (fi.type === "video/mp4");
 
                // alert(fileIsMp4);    

                if (file >= 3000 || !fileIsMp4) {
                    //alert("O vídeo deve ter menos de 3Mb!");
                       

                    swal({
       title: 'Tamanho máximo de 3Mb!',
       text: 'Tipo MP4',
       icon: 'error',
        
       })   
       ;

                      document.getElementById("file").value = '';
                      return false;

        
                } 
            
                    return true;
             
   
   
   
    }

</script>


<!--

<br>

<form name="frm" id="frm" action = "" method = "post" >
      




      <label>Sala: </label>  
      &nbsp; 
      <?php


$sql = "SELECT DISTINCT(nome) as no,s.id 
FROM salas s, equipamento eq
where s.id_escola=$idescola and s.id=eq.id_sala
order by s.nome";

$result = mysqli_query($db,$sql);


//echo('<select name="sala">');
?>


<select required style="background-color:#CEF6CE"  name="sala" id="sala" onChange="showequi(this.value);">

<?php

echo('<option value=""> Escolha a sala   </option>');  

while($row=mysqli_fetch_array($result))
{


if ($row['id']==$_REQUEST["sala"])
{
/*echo('<option selected value="'.$row['no'].'">'.$row['no'].'</option>');*/
echo('<option selected value="'.$_REQUEST["sala"].'">'.$row['no'].'</option>');
}
else


echo('<option value="'.$row['id'].'">'.$row['no'].'</option>');

}

echo('</select>');
?>     



  <br>   <br>

  </form>

-->





         <!--
            sa=<php echo ($sa);?>
            -->

            <?php



            if($_SERVER["REQUEST_METHOD"] == "POST") {

               
               
               ?>

             



              <form name="avaria" action="<?php echo SVRURL ?>grava_avaria.php?ai=<?php echo base64_encode($sa);?>&&esi=<?php echo  base64_encode($idescola);?>"   
               method = "post" enctype="multipart/form-data" 
               class="needs-validation" novalidate>

                   <?php
                  // if ($sa<>" ")
                   //{
                   ?>
              
              <br>
                   <label >Equipamento: (se lista vazia, escolha a sala)</label> 
                  <br>
                  
          
               <select 
               style="width: 100%;
            height: 35px; 
                       "

class="form-control required-field" name="equip" id="equip" required >
                    
 <?php  //echo($_POST["sala"]);
                      //sala='".$_REQUEST[sala]."'
                       $sql = "select id,nomeequi from equipamento 
                       where id_sala='$sa' and id
                       not in (select id_equi from avarias_reparacoes where datareparacao is  NULL)
                       order by nomeequi;";
                       $result = mysqli_query($db,$sql); 
                                           
                   echo('<br>');
                   
   
   while($row=mysqli_fetch_array($result))
   {
    
          echo('<option value="'.$row['id'].'">'.$row['nomeequi'].'</option>');

            }

           echo('</select>');
   
   
   mysqli_close($db);

                   
                   //}
                   ?>
               
            



                   <br />
                   <br>
                    <label>Data: </label>  
                    <input class="form-control required-field" required  value="<?php echo date("Y-m-d"); ?>"            
                    size="10" type = "date" name = "data" >
                  
                   <br />
                   <br>
                   <label>Avaria (descrição): </label>  <br>  
                   <textarea  class="form-control required-field" placeholder="Descrição da avaria" required  rows="4" cols="60"  name="avaria"></textarea>
                  
                  <br><br>

                  <div class="upload-section">
                <label class="upload-label">Imagem:</label>
                <div class="file-input-container">
                    <button type="button" class="file-input-button">Escolher ficheiro</button>
                    <input type="file" class="file-input" name = "imgavaria" id="img" onChange="validateImage()"  accept="image/jpeg,image/jpg,image/png,image/gif,image/bmp">
                </div>
                <div class="file-input-status">Nenhum ficheiro selecionado</div>
                <div class="file-type-info">Formatos aceites: JPEG, JPG, PNG, GIF, BMP</div>
            </div>
            <br>

            <div class="upload-section">
                <label class="upload-label">Vídeo:</label>
                <div class="file-input-container">
                    <button type="button" class="file-input-button">Escolher ficheiro</button>
                    <input type="file" class="file-input" name="v" id="file" onChange="return Filevalidation();" accept="video/mp4">
                </div>
                <div class="file-input-status">Nenhum ficheiro selecionado</div>
                <div class="file-type-info">Formato aceite: MP4</div>
                <div class="max-size-info">Tamanho máximo: 3MB</div>
            </div>

<!--
                   <br />
                   <br>
                   <label>Avaria (imagem: JPEG, JPG, PNG, GIF, BMP): </label>  <br>  
                   <input accept="image/png, image/gif, image/jpeg, image/jpg, image/bmp"  size=50 type="file" name = "imgavaria" id="img" onChange="validateImage()" />
                   <br /><br />

                   <label>Avaria (vídeo tamanho máximo 3Mb, tipo MP4): </label>  <br>  
                   <input accept="video/mp4" size=50 type="file" name="v" id="file" onChange="return Filevalidation();">
                   
     


                   <br /><br />
                                   
                   <div  style=" text-align:center;width:100%"> <input  type = "submit" value = "Inserir"/>   
    </div>   --> 

                           <div class="text-center mt-4">
                                    <button type="submit" class="btn-submit">
                                        <i class="bi bi-check-circle-fill me-2"></i>
                                        &nbsp;Inserir Avaria
                                    </button>
                                </div>

                </form>

<?php

         }
?>




<script>
        // Script para atualizar o texto de status quando um arquivo é selecionado
        document.querySelectorAll('.file-input').forEach(input => {
            input.addEventListener('change', function() {
                const status = this.parentElement.nextElementSibling;
                if (this.files.length > 0) {
                    status.textContent = this.files[0].name;
                } else {
                    status.textContent = 'Nenhum ficheiro selecionado';
                }
            });
        });
        
        // Script para fazer o botão personalizado funcionar como o input file
        document.querySelectorAll('.file-input-button').forEach(button => {
            button.addEventListener('click', function() {
                this.nextElementSibling.click();
            });
        });
    </script>









                    </div>
               

<div class="text-center mt-3">
                            <a class="btn btn-secondary" title="Voltar" href="<?php echo SVRURL ?>avaria">
                                <i class="bi bi-arrow-left"></i> Voltar
                            </a>
                            <br>  <br>
                        </div>

<!--
<input type = "hidden"  value = "<?php echo $sa?>">
<input title="Voltar" type=image 
src="<?php echo SVRURL ?>images/voltar.svg"  >
    -->





               </div>
            </div>
         </div>
      </div>
      <!-- end about -->
    
    <!-- Script para validação do formulário -->
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


      <?php 
           
         
      
      include ("footer.php");?>


   </body>
</html>