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
                     <h2>Inserir equipamento <br>(identificação)</h2>
                  </div>
               </div>
            </div>
            
            <div class="container">
               <div class="row">
                  <div class="col-md-8 offset-md-3">
              
                        
<?php
include("msg_bemvindo.php");
?>
<br>
<?php 
                
            
                $em=$_SESSION['email'];
         
                          
    
                ?>

<form name="equipamento" action = "<?php echo SVRURL ?>gravaequip" method = "post">



                    <label>Tipo de equipamento: </label> 
                 
                    <br>
                    <?php


      $sql = "SELECT DISTINCT(nome) as no FROM tipos_equipamento order by nome";
      
      $result = mysqli_query($db,$sql);
     
 
    //echo('<select name="sala">');
?>



&nbsp;    

    <select name="tipoeq" id="tipoeq" required  style=" background-color:#CEF6CE;" >
      
<?php

echo('<option value=""> </option>');  

     while($row=mysqli_fetch_array($result))
     {
           
       
             
         
        echo('<option value="'.$row['no'].'">'.$row['no'].'</option>');
       
        }
              
          echo('</select>');
     ?>     
             
             
             &nbsp;&nbsp;&nbsp;
              <a style="color:blue;" class="underlineHover" href="<?php echo SVRURL ?>tiposequip" title="Inserir novo tipo de equipamento">  
             Novo tipo
             </a>
      
           <br>  <br>
                <label>Sala: </label>  
                <br>
                &nbsp;      
<select name="sala" style=" background-color:#CEF6CE; "  required   >
  
  <?php
   $sql = "select DISTINCT(nome) as no FROM salas order by nome";
   $result = mysqli_query($db,$sql);
  
  echo('<option value=""> </option>');  
  
       while($row2=mysqli_fetch_array($result))
       {
             
   
           
          echo('<option value="'.$row2['no'].'">'.$row2['no'].'</option>');

       }
                
            echo('</select>');
       ?>  



            

                                              
<br>  <br>
<label>Nome: </label>  <br>
<input required type = "text" name = "nomeq" placeholder="Nome" style=" background-color:#CEF6CE;" >
                    
                   
     

      
 
<br>  <br>


             
                      <label>Nº de série: </label>  <br>
     
                     <input  type = "text" name = "nserie" placeholder="Nº de série">  
                     <br>   <br> 
                     <label>Marca/Modelo:</label>  <br>
                     <input    type = "text" name = "marcamod" placeholder="Marca/Modelo"> 

                     <br />       <br />  
                     <label>Data da compra:</label>  
                     <input  size="10"   type = "date" name = "datacompra" placeholder="Data da compra"> 

                     <br />  

                     <div  style=" text-align:center;width:90%"> <input  type = "submit" value = "Inserir"/>   
    </div>
                     
                 </form>


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