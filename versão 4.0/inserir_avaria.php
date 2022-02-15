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

 
  ?>
      
      <!-- about -->
      <div  class="about">
         <div class="container">
            <div class="row">
               <div class="col-md-12">
               <div class="titlepage">
                     <h2>Inserir avaria</h2>
                  </div>
               </div>
            </div>
            
            <div class="container">
               <div class="row">
                  <div class="col-md-5 offset-md-4">
              
                        


<script language="javascript" type="text/javascript">

function showequi(sala) {

    document.frm.submit();

}

</script>



<script language="JavaScript" >
function enviardados(){

//alert("al");

if(document.avaria.data.value=="" || document.avaria.avaria.value=="")
{
      alert( "Preencha a sala, data e a descrição da avaria." );
      //document.avaria.data.value.focus();
      return false;
}

else {
      return true;

  }
}

</script>




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






<form name="frm" id="frm" action = "" method = "post" >
      

      <label>Sala: </label>  
      <br>
      <?php


$sql = "SELECT DISTINCT(nome) as no FROM salas order by nome";

$result = mysqli_query($db,$sql);


//echo('<select name="sala">');



//echo($_REQUEST["sala"]);

?>

<select required style="background-color:#CEF6CE" required name="sala" id="sala" onChange="showequi(this.value);">

<?php




echo('<option value=""> Escolha a sala   </option>');  

while($row=mysqli_fetch_array($result))
{

   

if ($row['no']==$_REQUEST["sala"])
{
echo('<option selected value="'.$_REQUEST["sala"].'">'.$_REQUEST["sala"].'</option>');
}
else
echo('<option value="'.$row['no'].'">'.$row['no'].'</option>');


}

echo('</select>');
?>     



  <br>   <br>

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
               $em=$_SESSION['email'];
              // echo($sa);
               //echo($em);
             

           
               

               ?>

         <!--
            sa=<php echo ($sa);?>
            -->

            <?php
            if($_SERVER["REQUEST_METHOD"] == "POST") {

               ?>

              <form name="avaria" action="grava_avaria.php?sa=<?php echo $sa;?>
              &&em=<?php echo ($em);?>" method = "post" enctype="multipart/form-data" 
              onSubmit="return enviardados();">

                   <label>Equipamento: </label> 
                   <br>
                   <?php
               echo('<select name="equip" required style="background-color:#CEF6CE">');
                      //echo($_POST["sala"]);
                      //sala='".$_REQUEST[sala]."'

                       $sql = "select nomeequi from equipamento where sala='$sa' order by nomeequi";
                       $result = mysqli_query($db,$sql); 
                                           
                   echo('<br>');
                   
   
   while($row=mysqli_fetch_array($result))
   {
    
          echo('<option value="'.$row['nomeequi'].'">'.$row['nomeequi'].'</option>');

            }

           echo('</select>');
   
   mysqli_close($db);
                   
                   ?>
              
            

                   <br />
                   <br>
                    <label>Data: </label>  
                    <input style="background-color:#CEF6CE" required  value="<?php echo date("Y-m-d"); ?>"            
                    size="10" type = "date" name = "data" >
                  
                   <br />
                   <br>
                   <label>Avaria (descrição): </label>  <br>  
                   <textarea  required style="text-align: justify;background-color:#CEF6CE" rows="5" cols="50"  name="avaria"></textarea>
                  
                  
                   <br />
                   <br>
                   <label>Avaria (imagem: JPEG, JPG, PNG, GIF, BMP): </label>  <br>  
                   <input accept="image/png, image/gif, image/jpeg, image/jpg, image/bmp"  size=50 type="file" name = "imgavaria" id="img" onChange="validateImage()" />
                   <br /><br />

                   <label>Avaria (vídeo tamanho máximo 3Mb, tipo MP4): </label>  <br>  
                   <input accept="video/mp4" size=50 type="file" name="v" id="file" onChange="return Filevalidation();">
                   
                   <br /><br />
                                   
                   <div  style=" text-align:center;width:90%"> <input  type = "submit" value = "Inserir"/>   
    </div>

                </form>

<?php

         }
?>


<form action = "<?php echo SVRURL ?>avaria" method="post" >
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
    


      <?php 
           
         
      
      include ("footer.php");?>


   </body>
</html>