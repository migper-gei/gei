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
                     <h2>Atualizar avaria</h2>
                  </div>
               </div>
            </div>
            
            <div class="container">
               <div class="row">
                  <div class="col-md-8 offset-md-3">
              
                        

<?php
include("msg_bemvindo.php");
?>
    




    <script>

function fot(n) {


var n1;
n1=n;


  event.preventDefault(); // prevent form submit

   swal({

  title: "Deseja eliminar a foto?",
 //text: "Sala: "+s1+" (Escola: "+ne1+")",
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
    
    
        window.setTimeout(function() {
    window.location.href = '<?php echo SVRURL ?>eliminafovi/'+n1+'/f';
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




<script>

function vid(n) {


var n1;
n1=n;


  event.preventDefault(); // prevent form submit

   swal({

  title: "Deseja eliminar o vídeo?",
 //text: "Sala: "+s1+" (Escola: "+ne1+")",
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
    
    
        window.setTimeout(function() {
    window.location.href = '<?php echo SVRURL ?>eliminafovi/'+n1+'/v';
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












    <script type="text/javascript">

function Filevalida () {
        
   //alert("aaa");
        
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
         
                    
    <?php


      $sql = "SELECT DISTINCT(nome) as no FROM salas order by nome";
      $result = mysqli_query($db,$sql);
     
 
$idav=base64_decode($_GET['url']);


if (isset($_GET['url']))
{
$url = explode('/',$_GET['url']);

//echo $url[0];
}
else
{
    ?>
    
<script>
window.setTimeout(function() {
    window.location.href = '<?php echo SVRURL ?>myavarias?op=t';
}, 10);
</script>

<?php
}
?>


<?php
if ( !isset($idav) || empty($idav) || !is_numeric($idav) 
)

{

?>


<script>
window.setTimeout(function() {
    window.location.href = '<?php echo SVRURL ?>myavarias?op=t';
}, 10);
</script>


<?php
}
?>



<script> 
          
   // Function to increase image size 
   function enImg(img) { 
              
              img.style.transform = "scale(2.5)"; 
              img.style.transition = 
                "transform 0.25s ease"; 
          } 
          


          function rImg(img) { 
            //alert("zzz");
              img.style.transform = "scale(1)"; 
              //img.style.width = "40%"; 
              //img.style.height = "auto"; 
              //img.style.transition = "width 0.5s ease"; 
          } 
      </script> 





<script type="text/javascript">
function validaImg() {
    var formData = new FormData();
 
    //alert("bbb");

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










  <?php


$em=$_SESSION['email'];

$sql0 = "
SELECT count(*) as c FROM avarias_reparacoes
where id=".$idav." and autoravaria='".$em."'    ";

//SELECT autoravaria FROM avarias_reparacoes where id=".$url[0]." ";

$result0 = mysqli_query($db,$sql0);
$rows0 =mysqli_fetch_row($result0);
$aut = $rows0[0];

//echo $aut;

if ($aut<>0)
{
      $sql1 = "SELECT ar.*,s.nome,eq.nomeequi FROM avarias_reparacoes ar, salas s, equipamento eq
      where ar.id_sala=s.id and ar.id_equi=eq.id 
      and ar.id=".$idav." ";
      $result1 = mysqli_query($db,$sql1);
      $row1=mysqli_fetch_array($result1);



     $idesc=$row1['id_escola'];
   
     $sql11 = "select nome_escola from escolas where id=$idesc";
     $result11 = mysqli_query($db,$sql11); 
     $rows11 =mysqli_fetch_row($result11);
                       
     $idescola = $rows11[0];

   
?>
<form name="avaria" action = "<?php echo SVRURL ?>atualokavaria/<?php echo base64_encode($idav);?>" method = "post" enctype="multipart/form-data" onSubmit="return enviardados1();">
      
<br>
&nbsp;<label>Escola: </label>  
<br>
<input  value="<?php echo $idescola; ?>" readonly   name = "escola"  type="text" />
         <br><br>
&nbsp;<label>Sala: </label>  
<br>
<input  value="<?php echo $row1['nome']; ?>" readonly   name = "sala"  type="text" />
                    
 
  <br>  <br>
  &nbsp;<label>Equipamento: </label> 
                    <br>
                    <input  value="<?php echo $row1['nomeequi']; ?>" readonly   type = "text" name = "nomeequi"  />
                    
                    
                    <?php
               
    
    mysqli_close($db);
                    
                    ?>
                    <br>
  
                        

                    <br />
                    <br>
                     <label>Data: </label>  
                    
                     <input  required style="background-color:#CEF6CE;" value='<?php echo $row1['dataavaria']; ?>' required             
                     size="10" type = "date" name="data" >

                    <br />
                    <br>
                    <label>Avaria (descrição): </label>  <br>  
                    <textarea required    style="background-color:#CEF6CE;text-align: justify;" rows="4" cols="80" name="avaria"><?php echo $row1['avaria']; ?></textarea>
                   
                        
                 
                    <table >

                  <tr>
                  <td>

                  <?php
                     
                     if ($row1["imgavaria"] == null) 
                     {
                     echo ("");
                     }
                     
                     else {
                  
                         echo '<img name="i1" id="img1" onmouseover="enImg(this)" onmouseout="rImg(this)" 
                         height="150" width="250" src="data:image/jpeg;base64,'.base64_encode($row1['imgavaria']).' ">';
                          
                  
                 ?> 
                 <br>
             
                  <a  onclick="fot(<?php echo $url[0];?>);"  class="underlineHover" href="<?php echo SVRURL ?>eliminafovi/<?php echo $url[0] ?>/f" title="Remover foto" style="color:blue;font-size:16px;">Remover foto</a>

                  <?php
                   }
                  ?>

                  </td>
                  <td>
                  &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                  &nbsp;&nbsp;
                  <?php 
          

                  if ($row1["video"] == null) 
                  {
                  echo ("");
                  }

                  else {
                      echo '              
                      <video 
                      onmouseover="enImg(this)" onmouseout="rImg(this)" 
                      width="250" height="200" alt="test" controls >
                      <source src="data:video/mp4;base64,'.base64_encode($row1['video']).' " >
                   
                  </video>
      
                  ';    
                  



                   ?>
                    <br>
                    &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                  &nbsp;&nbsp;
                  <a  onclick="vid(<?php echo $url[0];?>);"  class="underlineHover" href="<?php echo SVRURL ?>eliminafovi/<?php echo $url[0] ?>/v" title="Remover vídeo" style="color:blue;font-size:16px;">Remover vídeo</a>

                  <?php
                   }
                  ?>



                  </td>

                  </tr>

                  </table>


            

<br>
<label>Avaria (imagem: JPEG, JPG, PNG, GIF, BMP): </label>  <br>  
                    <input 
                    accept="image/png, image/gif, image/jpeg, image/jpg, image/bmp" 
                    size=50 type="file" name = "imgavaria" id="img" onChange="validaImg()" />
                    <br /><br />
									
           

<label>Avaria (vídeo tamanho máximo 3Mb, tipo MP4): </label>  <br>  
<input accept="video/mp4" size=50 type="file" name="v" id="file" onChange="return Filevalida();">

<br /><br />
                           
                <div  style=" text-align:center;width:90%"> 
                    <input  type = "submit" value = "Atualizar"/>    
                </div>
        
 
               


                 </form>
                    
     
                 
<?php
}
else
{
?>

   <script>
    
   swal({
   title: 'Não é o autor da avaria ou avaria não existe!',
   //text: 'Não tem permisssão!',
   icon: 'error',
   //buttons: false,
   
   })
   .then(function() {
   window.location = "<?php echo SVRURL ?>myavarias?op=t";
   });
   
   
   </script>

<?php
}
?>



<form action = "<?php echo SVRURL ?>myavarias?op=t" method="post" >
<input type = "hidden"  >
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
    
      <?php include ("jquery_bootstrap.php");?>

      <?php include ("footer.php");?>


   </body>
</html>