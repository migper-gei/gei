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

$sql2a =  $db->prepare("select max(id) as me  from escolas ");
//$result2a = mysqli_query($db,$sql2a); 
//$rows2a =mysqli_fetch_row($result2a);


$sql2a->execute();
$rows2a = $sql2a->get_result()->fetch_row();

$maxesc = $rows2a[0];

//echo $maxesc;
$x=base64_decode($_GET["x"]);

//echo $x;


if ($x>1 || $x<0 || base64_decode($_GET["ies"])>$maxesc)
{

?>


<script>

window.setTimeout(function() {
             // window.location.href = '<?php echo SVRURL ?>equip';
          },10);
          </script>


<?php
}


if ($x==1)
{
$idescola= base64_decode($_GET["ies"]);
}
elseif ($x==0)
{
$idescola= base64_decode($_GET["ies"]);

}
 

$sql11 = $db->prepare("select nome_escola from escolas where id=?");
$sql11->bind_param("i", $idescola);
$sql11->execute();


$rows11= $sql11->get_result()->fetch_row();
$ne = $rows11[0];


  ?>
      
      <!-- about -->
      <div  class="about">
         <div class="container">
            <div class="row">
               <div class="col-md-12">
               <div class="titlepage">
                     <h2>Equipamentos >> Inserir equipamento (identificação)<br>
                     <?php echo $ne ?>
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
<br>
<?php 
                
            
               // $em=$_SESSION['email'];
         
                          
    
                ?>







<form name="equipamento" action = "<?php echo SVRURL ?>gravaequip?ies=<?php echo base64_encode($idescola) ?>" method = "post">



                    <label>Tipo de equipamento: </label> 
                 
                    <br>
                    <?php


      $sql = $db->prepare("SELECT DISTINCT(nome) as no FROM tipos_equipamento order by nome");
      
      //$result = mysqli_query($db,$sql);

      $sql->execute();
      $result= $sql-> get_result();

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
              <a 
              style="color:blue;" class="underlineHover" href="<?php echo SVRURL ?>tiposequip" title="Inserir novo tipo de equipamento">  
             Novo tipo
             </a>
           <br>  <br>


                <label>Sala: </label>  
                <br>
                &nbsp;   
                
              <?php

   $sql = $db->prepare("select * FROM salas 
   where id_escola=? order by nome");
   //$result = mysqli_query($db,$sql);
   
   $sql->bind_param("i", $idescola);
   $sql->execute();
 
   $result = $sql-> get_result();
  

   $rowcount = mysqli_num_rows($result);
             
             
  
             
             ?>  



<select name="sala" style=" background-color:#CEF6CE; "  required   >
  
  <?php

  
  echo('<option value=""> </option>');  
  
       while($row2=mysqli_fetch_array($result))
       {
             
   
           
          echo('<option value="'.$row2['id'].'">'.$row2['nome'].'</option>');

       }
                
            echo('</select>');
       ?>  

<?php
 if ($rowcount==0)
 {


 //echo('<h3 style="color:blue;">SALAS ');
 

echo ("A escola/agrupamento não têm salas.");

?>

&nbsp;&nbsp;&nbsp;
              <a style="color:blue;" class="underlineHover" href="<?php echo SVRURL ?>salas?x=1&&escola=<?php echo $idescola ?>" title="Inserir novo tipo de equipamento">  
             Salas
             </a>
<?php
}
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
                     <br />  

                   <label>Observações: </label>  <br>  
                   <textarea  rows="5" cols="80"  name="obs"></textarea>
                   <br>

                     <div  style=" text-align:center;width:90%"> <input  type = "submit" value = "Inserir"/>   
    </div>
                     
                 </form>


<a title="Voltar" href="<?php echo SVRURL ?>equip">
<img src="<?php echo SVRURL ?>images/voltar.svg" alt="Voltar">
</a>
<br><br>

                    
                  </div>
               </div>
            </div>
         </div>
      </div>
      <!-- end about -->
    
<?php
      // Clear the session
		unset($_SESSION['escola']);
?>

      <?php include ("footer.php");?>


   </body>
</html>