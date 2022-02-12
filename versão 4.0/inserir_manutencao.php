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
$sa=$_POST["sala"];


include("sessao_timeout.php");

 
  ?>
      
      <!-- about -->
      <div  class="about">
         <div class="container">
            <div class="row">
               <div class="col-md-12">
               <div class="titlepage">
                     <h2>Inserir manutenção<br>
                     <?php echo $sa ?></h2>

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



include("sessao_timeout.php");


include("verifica_sessao.php");


//echo ($_POST["sala"]);

 $sql2 = "select id from salas where nome='".$_POST["sala"]."' ";
 $result2 = mysqli_query($db,$sql2);
 $rows2 = mysqli_fetch_row($result2);
 $id=$rows2[0];

 $sa=$_POST["sala"];

    ?>





<form id="sectionForm" onsubmit="return r()"  name=""  method="post" action = "<?php echo SVRURL ?>gravamanutsala/<?php echo ($id);?>">

<?php 



// $sa=$_POST["sala"];
$em=$_SESSION['email'];
//echo($sa);
//echo($em);

$sql3 = "select nomeequi as noeq,tipo from equipamento where sala='$sa' order by tipo,nomeequi";
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
Escolha os PCs em que deseja efetuar a manutenção:
<div style="visibility:hidden; color:red; " id="chk_option_error">
Selecionar pelo menos uma opção.
</div>
<br>
<?php 

while($row3=mysqli_fetch_array($result3))
{
  ?>   
  <ul style="display: block;  ">

    <input   type="checkbox" name="eq[]" value="<?php echo ($row3['noeq']);?>" >
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