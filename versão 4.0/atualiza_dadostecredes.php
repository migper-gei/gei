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

//include("verifica_sessao.php");

//print_r(scandir(session_save_path()));


 
  ?>
      
      <!-- about -->
      <div  class="about">
         <div class="container">
            <div class="row">
               <div class="col-md-12">
               <div class="titlepage">
                     <h2>Atualizar equipamento <br> (dados técnicos e de rede)</h2>
                  </div>
               </div>
            </div>
            
            <div class="container">
               <div class="row">
                  <div class="col-md-12 offset-md-1">
              
                        
<?php
include("msg_bemvindo.php");
?>
<br>


            <?php
               
                 
               $id= $_GET['id'];
              



                $sql = "select * from equipamento where id=".$id."";
                $result = mysqli_query($db,$sql); 
                $row=mysqli_fetch_array($result);
               
               //echo ($row3['nomeequi']);
               ?>
         
                          
    
<form name="equipamento" action = "<?php echo SVRURL ?>atualiza_dadostecredes_OK.php?id=<?php echo $id?>" method = "post">

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



<h3 style="color:black;">DADOS TÉCNICOS:</h3>
           
<label>Processador</label>      <br>
<input title="Processador"  type = "text" name = "cpu" placeholder="Processador" value="<?php echo $row['processador']?>">  
              <br>
                <label>Memória (GB)</label>    <br>  
              <input  title="Memória (GB)"  value="<?php echo $row['memoria']?>"
              oninput="this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\..*?)\..*/g, '$1');"
              maxlength="2" type = "text" name = "ram"  maxlength="2" placeholder="Memória (GB)">  
            
              <br>
                <label>Disco</label>    <br>  
              <input   title="Disco" value="<?php echo $row['disco']?>"
                type = "text" name = "disco"   placeholder="Disco">  
              <br>
                <label>Placa gráfica</label>    <br>  
              <input   value="<?php echo $row['placagrafica']?>"  title="Placa gráfica"  type = "text" name = "grafica" placeholder="Placa gráfica">  
              <br>
                <label>Placa rede</label>    <br>  
              <input     value="<?php echo $row['placarede']?>" title="Placa rede" type = "text" name = "rede" placeholder="Placa rede">  
            
              <br>
                <label>Placa som</label>    <br>  
              <input  value="<?php echo $row['placasom']?>" title="Placa som" type = "text" name = "som" placeholder="Placa som">  
              <br>
                <label>Monitor</label>    <br>  
              <input value="<?php echo $row['monitor']?>" title="Monitor"  type = "text" name = "monitor" placeholder="Monitor"> 
              <br>
                <label>Teclado</label>    <br>  
              <input  value="<?php echo $row['teclado']?>" title="Teclado" type = "text" name = "teclado" placeholder="Teclado">
              <br>
                <label>Rato</label>    <br>  
              <input value="<?php echo $row['rato']?>" title="Rato:"   type = "text" name = "rato" placeholder="Rato">  
              <br /> 
              <br>
         
              <label>Colunas: </label>  
              <select  title="Colunas" name="colunas">
                   <option value=""></option>
                   <?php
                     if ($row['colunas']=='Sim')
                     {
                   ?>
                   <option selected value="Sim">Sim</option>
                   <?php
                     }else
                     {
                   ?>
                   <option value="Não">Não</option>
                   <?php
                     }
                   ?>
             </select>
             &nbsp;&nbsp;&nbsp;&nbsp;
         
        
             <label>CD/DVD: </label>  
              <select  title="CD/DVD" name="cddvd">
                   <option value=""></option>
                   <?php
                     if ($row['cd_dvd']=='Sim')
                     {
                   ?>
                   <option selected value="Sim">Sim</option>
                   <?php
                     }else
                     {
                   ?>
                   <option value="Não">Não</option>
                   <?php
                     }
                   ?>
             </select>
                   
                     <br> 
                     <br>
                     <br>   
<h3 style="color:black;">DADOS REDE:</h3>        
           <br> <label>Domínio</label>  <br>
            <input   size="30" type = "text" name = "dominio" placeholder="Domínio"> 
            
              <br>
                <label>Endereço IP</label>    <br>  
            <input   size="15" maxlength=15 type = "text" name = "ip" placeholder=" Endereço IP">
            <br>
                <label>Máscara de rede</label>    <br> 
            <input   size="15" maxlength=15 type = "text" name = "mascara" placeholder="Máscara de rede">  
            <br>
                <label>Gareway</label>    <br> 
            <input   size="15" maxlength=15 type = "text" name = "gateway" placeholder="Gateway">   
            <br>
                <label>DNS preferido</label>    <br> 
         
            <input   size="15" maxlength=15 type = "text" name = "dnsp" placeholder="DNS preferido">
            <br>DND alternativo</label>    <br> 
            <input   size="15" maxlength=15 type = "text" name = "dnsa" placeholder="DNS alternativo"> 
         
         
         

                     <br />   

                     <div  style=" text-align:center;width:90%"> <input  type = "submit" value = "Atualizar"/>     </div>
  
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