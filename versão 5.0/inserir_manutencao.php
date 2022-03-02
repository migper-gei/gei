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

 

$sa=$_POST["sala"];
$idescola=$_GET["escola"];


$sql10 = "select nome from salas where id=$sa";
$result10 = mysqli_query($db,$sql10); 
$rows10 =mysqli_fetch_row($result10);

 $ns = $rows10[0];


 $sql11 = "select nome_escola from escolas where id=$idescola";
$result11 = mysqli_query($db,$sql11); 
$rows11 =mysqli_fetch_row($result11);


$ne = $rows11[0];

  ?>
      
      <!-- about -->
      <div  class="about">
         <div class="container">
            <div class="row">
               <div class="col-md-12">
               <div class="titlepage">
                     <h2>Inserir manutenção (<?php echo $ns ?>)
                  <br><?php echo $ne ?>
                  </h2>

                  </div>
               </div>
            </div>
            
            <div class="container">
               <div class="row">
                  <div class="col-md-8 offset-md-3">
              
                        



                  <script>
       function r() {
           
        var form_data = new FormData(document.querySelector("form"));
    
        if(!form_data.has("eq[]"))
    {
        document.getElementById("chk_option_error").style.visibility = "visible";
       

      return false;
    }


   else if (!form_data.has("m[]"))
   {
    document.getElementById("chk_option_error1").style.visibility = "visible";
       

       return false;

   }





    else
    {
        document.getElementById("chk_option_error").style.visibility = "hidden";
        document.getElementById("chk_option_error1").style.visibility = "hidden";
      return true;
    }


    



        }
  </script>



<?php
//include("header.php");
//include("config.php");

//echo $_SESSION['login_user'];


//session_start();




include("verifica_sessao.php");


//echo ($_POST["sala"]);

/*
 $sql2 = "select id from salas where id_sala=".$sa." ";
 $result2 = mysqli_query($db,$sql2);
 $rows2 = mysqli_fetch_row($result2);
 $id=$rows2[0];
*/


    ?>





<form id="sectionForm" onsubmit="return r()"  name=""  method="post" 
action = "<?php echo SVRURL ?>grava_manutencao_sala.php?sa=<?php echo ($sa);?>&&escola=<?php echo ($idescola);?>">

<?php 



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
    <input style="background-color:#CEF6CE" size=40 type = "text" name = "pessoa"  required/>
    <br> 
    <br>

<br>
Escolha os PCs em que deseja efetuar a manutenção (se vazio, sala sem equipamento):
<div style="visibility:hidden; color:red; " id="chk_option_error">
Selecionar pelo menos uma opção.
</div>
<br>
<?php 

while($row3=mysqli_fetch_array($result3))
{
  ?>   
  <ul style="display: block;  ">

    <input   type="checkbox" name="eq[]" value="<?php echo ($row3['id']);?>" >
<?php echo ($row3['noeq']); echo(' - '.$row3['tipo']);          ?>

</ul>

<?php
}
?>          

    


<br><br><br><br>


Escolha o tipo de manutenção:

 
<div style="visibility:hidden; color:red; " id="chk_option_error1">
Selecionar pelo menos uma opção.
</div>



<br>

<input type="checkbox" name="m[]" value="Formatação">Formatação
<br />    <br />
<input type="checkbox" name="m[]" value="Limpeza interior">Limpeza interior

<br />
<input type="checkbox" name="m[]" value="Limpeza teclado">Limpeza teclado
<br />
<input type="checkbox" name="m[]" value="Limpeza rato">Limpeza rato
<br />
<br />
<input type="checkbox" name="m[]" value="Instalação / atualização software">Instalação / atualização software
<br />
<input type="checkbox" name="m[]" value="Instalação / atualização software">Instalação / atualização hardware

<br /> <br />
<input type="checkbox" name="m[]" value="Eliminação adware / spyware">Eliminação adware / spyware

<br />

<input type="checkbox" name="m[]" value="Eliminação de contas / ficheiros / virus">Eliminação de contas / ficheiros / virus
<br />

<br><br>
                   <label>Observações: </label>  <br>  
                   <textarea  rows="5" cols="80"  name="obs"></textarea>
                  
    <br /><br />
      


                 
    <div  style=" text-align:center;width:90%"> <input  type = "submit" value = "Inserir"/>   
    </div>

              

 </form>
    



                    </div>
               
               </div>
            </div>
         </div>
      </div>
      <!-- end about -->
    


      <?php include ("footer.php");?>


   </body>
</html>