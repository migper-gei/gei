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
               <a href="#" class="btn btn-secondary disabled">EQUIPAMENTOS >> INSERIR</a>
               <div class="titlepage">
                     <h2>Outro equipamento<br>
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







<script language="javascript" type="text/javascript">

function showescdig(escola) {

    document.frme.submit();

}

</script>







<form name="equipamentoout" action = "<?php echo SVRURL ?>gravaoutequip?ies=<?php echo base64_encode($idescola);?>" 
method = "post">



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



<select name="sala" style=" background-color:#CEF6CE;  "  required   >
  
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
<input required type = "text" name = "nomeq" placeholder="Nome" style=" background-color:#CEF6CE;width:100%" >
                    
                   
     

      
 
<br>  <br>


             
                      <label>Quantidade: </label>  <br>
     
                     <input  style=" background-color:#CEF6CE;" required  min="1" type = "number" name = "qta" placeholder="Quantidade">  
                
                     <br />  
                     <br />  

                   <label>Observações: </label>  <br>  
                   <textarea style=" width:100%"   rows="5" cols="80"  name="obs"></textarea>
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