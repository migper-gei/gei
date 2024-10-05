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
               <a href="#" class="btn btn-secondary disabled">MANUTENÇÕES >> INSERIR</a>
               <div class="titlepage">
                     <h2><?php echo $ns ?> 
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


<script>
function Check(){
chk=document.getElementsByName("my_check")[0]
chk2=document.getElementsByName('eq[]')

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
function Check2(){
chk=document.getElementsByName("my_check2")[0]
chk2=document.getElementsByName('m[]')

if(chk.checked==true){
  for (i=0;i<chk2.length;i++)
    chk2[i].checked=true
}else{
  for (i=0;i<chk2.length;i++)
    chk2[i].checked=false
}

}


</script>



<form  name="myform" onsubmit="return validate();"   method="post" 
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
<label>Data: </label>  
     <input required  style="background-color:#CEF6CE"           
     size="10" type = "date" name = "data" >
     <br>   <br>
     <label>Pessoa que realizou: </label>  <br>
    <input required style="background-color:#CEF6CE;Width:100%" size=40 type = "text" name="pessoa"  />
    <br> 
    <br>

<br>
Escolha os equipamentos em que deseja efetuar a manutenção:
<!--
<div style="visibility:hidden; color:red; " id="chk_option_error">
Selecionar pelo menos uma opção.
</div>
-->
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

<?php 
$sql4 = "select nome from tipos_manutencao";
$result4=mysqli_query($db,$sql4);
?>

<li class="list-group-item">  

<div style="text-align: center; color:blue;">
<input  type=checkbox name="my_check2" value="yes" onClick=Check2()>

Selecionar/Desselecionar tudo
</div>



<?php 

while($row4=mysqli_fetch_array($result4))
{
  ?>   
  <ul style="display: block; ">

    <input  type="checkbox" name="m[]" value="<?php echo ($row4['nome']);?>" >
<?php echo ($row4['nome']);        ?>

</ul>

<?php
}
?>          

</li> 


<br><br>
                   <label>Observações: </label>  <br>  
                   <textarea  style="width:100%" rows="5"   name="obs"></textarea>
                  
    <br /><br />
      


                 
    <div  style=" text-align:center;width:100%"> <input  type = "submit" value = "Inserir"/>   
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