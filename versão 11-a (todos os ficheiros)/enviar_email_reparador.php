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





$sql2a = "select max(id) as me  from escolas ";
$result2a = mysqli_query($db,$sql2a); 
$rows2a =mysqli_fetch_row($result2a);


$maxesc = $rows2a[0];


if (!is_numeric(base64_decode($_GET["ia"]))
|| empty(base64_decode($_GET["ia"]))
|| empty(base64_decode($_GET["em"]))

)
{

?>


<script>

window.setTimeout(function() {
              window.location.href = '<?php echo SVRURL ?>avaria';
          },10);
          </script>


<?php
}



$ia=base64_decode($_GET["ia"]);
$em=base64_decode($_GET["em"]);
$sa=base64_decode($_GET["sa"]);


?>








      <!-- about -->
      <div  class="about">
         <div class="container">
            <div class="row">
               <div class="col-md-12">
               <div class="titlepage">
                     <h2>AVARIA - Enviar email reparador 
                       
                  </h2>

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
   


function validate() {
  var checkbox= document.querySelector('input[name="rep[]"]:checked');



  if(!checkbox) {
   // alert('Escolha pelo menos um equipamento!');
   event.preventDefault(); // prevent form submit

   swal({

  title: "Escolha pelo menos um reparador!",
 //text: "Sala: "+s1+" (Escola: "+ne1+")",
  type: "warning",
  //showCancelButton: true,
  //confirmButtonColor: "#DD6B55",


  confirmButtonText: "OK",
  //cancelButtonText: "Não",
  closeOnConfirm: false,
  closeOnCancel: false
 
} );
   
   return false;
  }


  else 
  return true;//confirm('Deseja continuar?');
}
  </script>


<script>
function Check(){
chk=document.getElementsByName("my_check")[0]
chk2=document.getElementsByName('rep[]')

if(chk.checked==true){
  for (i=0;i<chk2.length;i++)
    chk2[i].checked=true
}else{
  for (i=0;i<chk2.length;i++)
    chk2[i].checked=false
}
}


</script>

<script> 
          
          // Function to increase image size 
          function enlargeImg(img) { 
                     img.style.transform = "scale(2.5)"; 
                     img.style.transition = 
                       "transform 0.25s ease"; 
                 } 
     
     
                 function resetImg(img) { 
                     img.style.transform = "scale(1)"; 
                     //img.style.width = "40%"; 
                     //img.style.height = "auto"; 
                     //img.style.transition = "width 0.5s ease"; 
                 } 
             </script> 
<?php
$sql2 = "select ar.*, e.nomeequi, s.nome, esc.nome_escola, esc.id as ide
from avarias_reparacoes ar, equipamento e, salas s, escolas esc
where ar.id_equi=e.id and ar.id_sala=s.id and ar.id_escola=esc.id
and ar.id=$ia and ar.datareparacao is null";
$result2=mysqli_query($db,$sql2);
$row2=mysqli_fetch_array($result2);


$sql2a = "select nome from utilizadores where email='$em' ";
$result2a = mysqli_query($db,$sql2a); 
$rows2a =mysqli_fetch_row($result2a);


//echo $row2['ide'];

?>




<form  name="myform" onsubmit="return validate();"   method="post" 
action = "<?php echo SVRURL ?>enviar_email_avaria.php?r=<?php echo base64_encode(1) ?>&&ia=<?php echo base64_encode($row2['id']);?>">

<?php 



$sql3 = "select id,nome,email from utilizadores where tipo=3";
$result3=mysqli_query($db,$sql3);

?>

<br>
Dados da avaria:
<br>
<li class="list-group-item">  
<b>Escola / Sala / Equipamento:</b>

<?php echo $row2['nome_escola']; echo(' / ');
                    echo $row2['nome']; echo(' / '); echo $row2['nomeequi'];?>
<br>



                    <label><b>Autor / Email:</b> </label> 
                    <?php echo ($rows2a[0]); 
                    echo(' / '); 
                    echo $em; 
                    echo('<br>'.'<b>'.'Data avaria: '.'</b>'); 
                     echo $row2['dataavaria']; 
                    echo('<br>'.'<b>'.'Descrição: '.'</b>'); 
                    echo $row2['avaria']; echo('<br>');
                    ?>

                    <br>
<?php
                     
                     if ($row2["imgavaria"] == null) 
                     {
                     echo ("");
                     }
                     
                     else {?>
                    
                   
                    <?php 
                    echo '<img onmouseover="enlargeImg(this)" onmouseout="resetImg(this)" 
                    height="150" width="250" src="data:image/jpeg;base64,'.base64_encode($row2['imgavaria']).' ">';
                     }
                     
                     echo ("<br>");
                     
                     
                     if ($row2["video"] == null) 
                     {
                     echo ("");
                     }
 
                     else {
                         echo '              
                         <video 
                         onmouseover="enlargeImg(this)" onmouseout="resetImg(this)" 
                         width="250" height="200" alt="test" controls >
                         <source src="data:video/mp4;base64,'.base64_encode($row2['video']).' " >
                      
                     </video>
         
                     ';    
                     }
                    ?>

</li>




    <br>

<br>
Escolha o(s) reparador(es) para envio de email:

<br>

<li class="list-group-item">  

<div style="text-align: center; color:blue;">
<input  type=checkbox name="my_check" value="yes" onClick=Check()>

Selecionar/Desselecionar tudo
</div>

<br>
<?php 

while($row3=mysqli_fetch_array($result3))
{
  ?>   
  <ul style="display: block; ">

    <input  type="checkbox" name="rep[]" value="<?php echo ($row3['id']);?>" >
<?php echo ($row3['nome']); echo(' - '.$row3['email']);          ?>

</ul>

<?php
}
?>          

</li> 


<br>

      


                 
    <div  style=" text-align:center;width:90%"> <input  type = "submit" value = "Enviar email"/>   
    </div>

              

 </form>
    
<!--
 <a href="<?php echo SVRURL ?>avaria">
-->

<a href="<?php echo SVRURL ?>reparacoes_efetuar_sala.php?x=<?php echo base64_encode(1)?>&&op=t&&ies=<?php echo base64_encode($row2['ide'])?>&&sai=<?php echo base64_encode($sa)?>">

<img src="<?php echo SVRURL ?>images/voltar.svg" alt="Voltar">
</a>


<br><br>
<?php include ("jquery_bootstrap.php");?>

                    </div>
               
               </div>
            </div>
         </div>
      </div>
      <!-- end about -->
    
  

      <?php include ("footer.php");?>


   </body>
</html>