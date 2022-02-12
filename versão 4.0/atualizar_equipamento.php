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
                     <h2>Atualizar equipamento (identificação)</h2>
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
               $sa= $_GET['sa'];




                $sql3 = "select nomeequi,sala,tipo,numserie,marca_modelo from equipamento where id=".$id."";
                $result3 = mysqli_query($db,$sql3); 
                $row3=mysqli_fetch_array($result3);
               
               //echo ($row3['nomeequi']);
               ?>
         
                          
    
            

<form name="equipamento" action = "<?php echo SVRURL ?>atualiza_equipamento_OK.php?id=<?php echo $id?>&&nomeant=<?php echo $row3['nomeequi']?>" method = "post">

                    <label>Tipo de equipamento: </label> 
                 
               
                    <?php


      $sql = "SELECT DISTINCT(nome) as no FROM tipos_equipamento order by nome";
      
      $result = mysqli_query($db,$sql);
     
 
    //echo('<select name="sala">');
?>





    <select name="tipoeq" id="tipoeq" required  style=" background-color:#CEF6CE;" >
      
<?php

echo('<option value=""> </option>');  

     while($row=mysqli_fetch_array($result))
     {
           
       
        if ($row['no']==$row3['tipo'])
        {
            echo('<option selected value="'.$row['no'].'">'.$row['no'].'</option>');
        }
        else
        
         
        echo('<option value="'.$row['no'].'">'.$row['no'].'</option>');
       
        }
              
          echo('</select>');
     ?>     
             
             
             &nbsp;&nbsp;&nbsp;
              <a style="color:blue;" class="underlineHover" href="<?php echo SVRURL ?>tiposequip" title="Inserir novo tipo de equipamento">  
             Novo tipo
             </a>
      
           <br>
                <label>Sala: </label>  

                    
<select name="sala" style=" background-color:#CEF6CE; width: 290px; "  required   >
  
  <?php
   $sql2 = "select DISTINCT(nome) as no FROM salas order by nome";
   $result2 = mysqli_query($db,$sql2);
  
  echo('<option value=""> </option>');  
  
       while($row2=mysqli_fetch_array($result2))
       {
             
        if ($row2['no']==$row3['sala'])
        {
            echo('<option selected value="'.$row2['no'].'">'.$row2['no'].'</option>');
        }
        else
        
           
          echo('<option value="'.$row2['no'].'">'.$row2['no'].'</option>');

       }
                
            echo('</select>');
       ?>  



            

                                              
<br>

<input required type = "text" name = "nomeq" value="<?php echo $row3['nomeequi']?>" style=" background-color:#CEF6CE;" >
                    
                   
     

      
 
<br>


             
                 
     
                     <input  type = "text" name = "nserie" placeholder="Nº de série">  
                     <br>  
          
                     <input    type = "text" name = "marcamod" placeholder="Marca/Modelo"> 

                     <br />   

                     <div  style=" text-align:center;width:90%"> <input  type = "submit" value = "Atualizar"/>     </div>
  
                 </form>

<form action = "<?php echo SVRURL ?>ver_equipamentos_sala.php?x=0" method="post" >
<input type = "hidden" name = "sala" value = "<?php echo $sa?>">
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
    


      <?php include ("footer.php");?>


   </body>
</html>