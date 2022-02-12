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
         <div class="loader"><img src="images/loading.gif" alt="Loading" /></div>
      </div>
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
                     <h2>Equipamentos <br>(Dados técnicos e de rede)</h2>
                  </div>
               </div>
            </div>
            
            <div class="container">
               <div class="row">
                  <div class="col-md-12 offset-md-1">
              
                        



<?php
$id=$_GET["id"];
$z=$_GET["z"];
//echo ($id);

$sql2 = "select count(*) from equipamento where id=".$id."";
$result2 = mysqli_query($db,$sql2); 
$rows2 =mysqli_fetch_row($result2);

$conta = $rows2[0];

if ($conta==0) 
{
?>
   <script>
   window.setTimeout(function() {
       window.location.href = '<?php echo SVRURL ?>inserirequip';
   }, 10);
   </script>
<?php
}
else
{
   
$sql3 = "select nomeequi,sala,tipo from equipamento where id=".$id."";
$result3 = mysqli_query($db,$sql3); 
$row=mysqli_fetch_array($result3);
?>

<div  style=" text-align:center;width:90%">
<?php
echo('<h4>');
echo $row['tipo'];
echo(" | ");
echo $row['nomeequi'];
echo(" | ");
echo $row['sala'];
echo('</h4>');
   ?>
</div>




<br>

<?php
if ($z==1)
{

?>
<a class="underlineHover" title="Dados de rede" href="<?php echo SVRURL ?>dados_tec_redes.php?id=<?php echo ($id);?>&z=2">
  <h3 style="color:blue;">DADOS DE REDE</h3>
</a>





<br><br>


<h3 style="color:black;">DADOS TÉCNICOS:</h3>





 <form name="equipamento" action = "gravaequipdadostec.php?id=<?php echo($id)?>" method = "post">



                     <input title="Processador"  type = "text" name = "cpu" placeholder="Processador">  
              
                  
                     <input  title="Memória (GB)" 
                     oninput="this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\..*?)\..*/g, '$1');"
                     maxlength="2" type = "text" name = "ram"  maxlength="2" placeholder="Memória (GB)">  
                   
               
                     <input   title="Disco" 
                     type = "text" name = "disco"  maxlength="4" placeholder="Disco">  
                 
                     <input     title="Placa gráfica"  type = "text" name = "grafica" placeholder="Placa gráfica">  
                
                     <input    title="Placa rede" type = "text" name = "rede" placeholder="Placa rede">  
                   
               
                     <input   title="Placa som" type = "text" name = "som" placeholder="Placa som">  
                   
                     <input  title="Monitor"  type = "text" name = "monitor" placeholder="Monitor"> 
               
                     <input  title="Teclado" type = "text" name = "teclado" placeholder="Teclado">
                    
                     <input  title="Rato:"   type = "text" name = "rato" placeholder="Rato">  
                     <br /> <br />
                     <label>Colunas: </label>  
                     <select  title="Colunas" name="colunas">
                          <option value=""></option>
                          <option value="Sim">Sim</option>
                          <option value="Não">Não</option>
                    </select>
                    &nbsp;&nbsp;&nbsp;&nbsp;
                    <label>CD/DVD: </label>  
                     <select  title="CD/DVD" name="cddvd">
                          <option value=""></option>
                          <option value="Sim">Sim</option>
                          <option value="Não">Não</option>
                    </select>

                    <div  style=" text-align:center;width:90%"> <input  type = "submit" value = "Inserir"/>   
    </div>
                          

</form>

<?php
}
elseif ($z==2)
{

?>

 


<a class="underlineHover" title="Dados de rede" href="<?php echo SVRURL ?>dados_tec_redes.php?id=<?php echo ($id);?>&z=1">
  <h3 style="color:blue;">DADOS TÉCNICOS</h3>
</a>
<br><br>

<h3 style="color:black;" >DADOS DE REDE:</h3>



<form name="form2" action = "gravaequipdadosrede.php?id=<?php echo($id)?>" method = "post">
               
               <input   size="30" type = "text" name = "dominio" placeholder="Domínio"> 
            
            
               <input   size="15" maxlength=15 type = "text" name = "ip" placeholder=" Endereço IP">
            
               <input   size="15" maxlength=15 type = "text" name = "mascara" placeholder="Máscara de rede">  
            
               <input   size="15" maxlength=15 type = "text" name = "gateway" placeholder="Gateway">   
            
            
               <input   size="15" maxlength=15 type = "text" name = "dnsp" placeholder="DNS preferido">
            
               <input   size="15" maxlength=15 type = "text" name = "dnsa" placeholder="DNS alternativo"> 
            
            
            
            
            
            <div  style=" text-align:center;width:90%">
             <input   type = "submit" value = "Inserir"/>   
            </div>
                    
            
            </form>
            



<?php
}
?>




<?php
}

?>








                    
                  </div>
               </div>
            </div>
         </div>
      </div>
      <!-- end about -->
    


      <?php include ("footer.php");?>


   </body>
</html>