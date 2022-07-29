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


if (base64_decode($_GET["esm"])>$maxesc
|| !is_numeric(base64_decode($_GET["esm"]))
|| empty(base64_decode($_GET["esm"]))
|| !isset($_POST["sala"]) || empty($_POST["sala"])
)
{

?>


<script>

window.setTimeout(function() {
              window.location.href = '<?php echo SVRURL ?>manut';
          },10);
          </script>


<?php
}


$sa=$_POST["sala"];
$idescola=base64_decode($_GET["esm"]);







$sql10 = "select nome from salas where id=$sa";
$result10 = mysqli_query($db,$sql10); 
$rows10 =mysqli_fetch_row($result10);

 $ns = $rows10[0];
 $num_ns = mysqli_num_rows($result10);

 $sql11 = "select nome_escola from escolas where id=$idescola";
$result11 = mysqli_query($db,$sql11); 
$rows11 =mysqli_fetch_row($result11);


$ne = $rows11[0];
$num_ne = mysqli_num_rows($result11);
  ?>
      



      <?php
     if ($num_ns==0 || $num_ne==0 )
{
?>

<script>
window.setTimeout(function() {
    window.location.href = '<?php echo SVRURL ?>manut';
}, 10);
</script>


<?php

}

?>








      <!-- about -->
      <div  class="about">
         <div class="container">
            <div class="row">
               <div class="col-md-12">
               <div class="titlepage">
                     <h2>Manutenções >> Inserir manutenção 
                        <br><?php echo $ns ?> 
                  | <?php echo $ne ?>
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
  var checkbox= document.querySelector('input[name="eq[]"]:checked');
  var checkbox2= document.querySelector('input[name="m[]"]:checked');


  if(!checkbox) {
   // alert('Escolha pelo menos um equipamento!');
   event.preventDefault(); // prevent form submit

   swal({

  title: "Escolha pelo menos um equipamento!",
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

  else if(!checkbox2) {
    //alert('Escolha pelo menos um tipo de manutenção!');
    
    event.preventDefault(); // prevent form submit

swal({

title: "Escolha pelo menos um tipo de manutenção!",
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





<form   onsubmit="return validate();"   method="post" 
action = "<?php echo SVRURL ?>grava_manutencao_sala.php?si=<?php echo base64_encode($sa);?>&&esm=<?php echo base64_encode($idescola);?>">

<?php 

//

// $sa=$_POST["sala"];
//$em=$_SESSION['email'];
//echo($sa);
//echo($em);

$sql3 = "select e.nomeequi as noeq,e.id,e.tipo 
from equipamento e, salas s
where e.id_sala=s.id
and s.id_escola=$idescola and s.id=$sa 
order by e.tipo,e.nomeequi";
$result3=mysqli_query($db,$sql3);

?>

<br>

<br>
<label>Data: </label>  
     <input required  style="background-color:#CEF6CE"           
     size="10" type = "date" name = "data" >
     <br>   <br>
     <label>Pessoa que realizou: </label>  <br>
    <input required style="background-color:#CEF6CE" size=40 type = "text" name="pessoa"  />
    <br> 
    <br>

<br>
Escolha os PCs em que deseja efetuar a manutenção:
<!--
<div style="visibility:hidden; color:red; " id="chk_option_error">
Selecionar pelo menos uma opção.
</div>
-->
<br>

<li class="list-group-item">  
<?php 

while($row3=mysqli_fetch_array($result3))
{
  ?>   
  <ul style="display: block; ">

    <input  type="checkbox" name="eq[]" value="<?php echo ($row3['id']);?>" >
<?php echo ($row3['noeq']); echo(' - '.$row3['tipo']);          ?>

</ul>

<?php
}
?>          

</li> 


<br><br>


Escolha o tipo de manutenção:

 <!--
<div style="visibility:hidden; color:red; " id="chk_option_error1">
Selecionar pelo menos uma opção.
</div>
-->


<br>
<li class="list-group-item">  
<input type="checkbox" name="m[]" value="Formatação"> Formatação
<br />    <br />
<input type="checkbox" name="m[]" value="Limpeza interior"> Limpeza interior

<br />
<input type="checkbox" name="m[]" value="Limpeza teclado"> Limpeza teclado
<br />
<input type="checkbox" name="m[]" value="Limpeza rato"> Limpeza rato
<br />
<br />
<input type="checkbox" name="m[]" value="Instalação / atualização software"> Instalação / atualização software
<br />
<input type="checkbox" name="m[]" value="Instalação / atualização hardware"> Instalação / atualização hardware

<br /> <br />
<input type="checkbox" name="m[]" value="Eliminação adware / spyware"> Eliminação adware / spyware

<br />

<input type="checkbox" name="m[]" value="Eliminação de contas / ficheiros / virus"> Eliminação de contas / ficheiros / virus

</li>


<br><br>
                   <label>Observações: </label>  <br>  
                   <textarea  rows="5" cols="80"  name="obs"></textarea>
                  
    <br /><br />
      


                 
    <div  style=" text-align:center;width:90%"> <input  type = "submit" value = "Inserir"/>   
    </div>

              

 </form>
    

 <a href="<?php echo SVRURL ?>manut">
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