<!DOCTYPE html>
<html lang="pt">
   <head>
      

<?php

 include ("head.php");
?>

   </head>


   <!-- body -->
   <body class="main-layout">
      <!-- loader --> 
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

$idescola=base64_decode($_GET["ies"]);

$id=base64_decode($_GET["qi"]);


$z=$_GET["z"];



if ( !isset($_GET["ies"]) || !isset($_GET["qi"]) || !isset($_GET["z"]) 
|| empty($_GET["ies"])  || empty($_GET["qi"])  || empty($_GET["z"]) || ($_GET["z"])>2 || ($_GET["z"])<1
)
{
?>


<script>
window.setTimeout(function() {
    window.location.href = '<?php echo SVRURL ?>equip';
}, 10);
</script>


<?php
}





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
               <a href="#" class="btn btn-secondary disabled">EQUIPAMENTOS >> INSERIR</a>
               <div class="titlepage">
                     <h2>Dados técnicos e de rede
                        <br> <?php echo $ne ?>

                     </h2>
                  </div>
               </div>
            </div>
            
            <div class="container">
               <div class="row">
                  <div class="col-md-10 offset-md-1">
              
                        



<?php



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
       window.location.href = '<?php echo SVRURL ?>inserirequip?x=<?php echo base64_encode(1)?>&&escola=<?php echo $idescola ?>';
   }, 10);
   </script>
<?php
}
else
{
   
$sql3 = "select e.nomeequi as neq,s.nome as nos,e.tipo as ti from equipamento e, salas s
where s.id=e.id_sala
and e.id=".$id."";
$result3 = mysqli_query($db,$sql3); 
$row=mysqli_fetch_array($result3);
?>

<div  style=" text-align:center;width:90%">
<?php
echo('<h4>');
echo $row['ti'];
echo(" | ");
echo $row['neq'];
echo(" | ");
echo $row['nos'];
echo('</h4>');
   ?>
</div>




<br>

<?php
if ($z==1)
{

?>
<a class="underlineHover" title="Dados de rede" href="<?php echo SVRURL ?>dados_tec_redes.php?qi=<?php echo base64_encode($id);?>&z=2&&ies=<?php echo base64_encode($idescola) ?>">
  <h3 style="color:blue;">DADOS DE REDE</h3>
</a>





<br><br>


<h3 style="color:black;">DADOS TÉCNICOS:</h3>





 <form name="equipamento" action = "<?php echo SVRURL ?>gravaequipdadostec.php?qi=<?php echo base64_encode($id)?>&&ies=<?php echo base64_encode($idescola) ?>" method = "post">



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

 


<a class="underlineHover" title="Dados de rede" href="<?php echo SVRURL ?>dados_tec_redes.php?qi=<?php echo base64_encode($id);?>&z=1&&ies=<?php echo base64_encode($idescola) ?>">
  <h3 style="color:blue;">DADOS TÉCNICOS</h3>
</a>
<br><br>

<h3 style="color:black;" >DADOS DE REDE:</h3>



<form name="form2" action = "<?php echo SVRURL ?>gravaequipdadosrede.php?qi=<?php echo base64_encode($id)?>&&esi=<?php echo base64_encode($idescola) ?>" method = "post">
               
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

}

?>


<a href="<?php echo SVRURL ?>equip">
<img src="<?php echo SVRURL ?>images/voltar.svg" alt="Voltar">
</a>
<br><br>





                    
                  </div>
               </div>
            </div>
         </div>
      </div>
      <!-- end about -->
    


      <?php include ("footer.php");?>


   </body>
</html>