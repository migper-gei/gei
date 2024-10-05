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

 
  ?>
      
      <!-- about -->
      <div  class="about">
         <div class="container">
            <div class="row">
               <div class="col-md-12">
               <a href="#" class="btn btn-secondary disabled">UTILIZADORES >> ATUALIZAR</a>
               <div class="titlepage">
                    
                  </div>
               </div>
            </div>
            
            <div class="container">
               <div class="row">
                  <div class="col-md-8 offset-md-3">
              
                        

<?php
include("msg_bemvindo.php");
?>
 
 


 <?php

if (isset($_GET['url']))
{
$url = explode('/',$_GET['url']);

//echo $url[0];
}
else
{
    ?>
<script>
window.setTimeout(function() {
    window.location.href = '<?php echo SVRURL ?>l';
}, 10);
</script>
<?php
}


    





 $sql = "select * from utilizadores where id=".base64_decode($url[0])."";
 $result = mysqli_query($db,$sql);
 $row=mysqli_fetch_array($result);

 //echo($row['nome']);
?>
            <a href="<?php echo SVRURL ?>sair">Sair</a>
              </h3>   


<?php
if (mysqli_num_rows($result)==0)
{

   // header("Refresh:0;url=salas");
?>


   <script>
window.setTimeout(function() {
    window.location.href = '<?php echo SVRURL ?>utiliz';
}, 10);
</script>

<?php
}
else
{ 
?>


<form action = "<?php echo SVRURL ?>atualiza_ok_utiliz.php?ui=<?php echo  base64_encode($row['id']); ?>" method = "post" >
                    <label>Utilizador: </label>  <br>  
                    <input class="underlineHover" size=50 type = "text" name="nome"  value="<?php echo $row['nome']; ?>"/><br /><br />
                 
                     <label>Email: </label>  <br>  
                    <input class="underlineHover" size=50 type = "text" name="email"   value="<?php echo $row['email']; ?>"/><br /><br />
                
                    <label>Tipo:    </label>  
   
                    &nbsp; 
                   


                    <select name="tipo" style="width: 10%;">
                    <?php 
                              if ($row['tipo']==1)
                              {
                            ?>
                            <option value="<?php echo $row['tipo']; ?>" selected>
                            <?php echo $row['tipo']; ?></option>
                            <option value=2>2</option>
                            <option value=3>3</option>
                            <option value=4>4</option>
                            <?php 
                              }
                            elseif ($row['tipo']==2)
                            {
                                ?>
                                <option value="<?php echo $row['tipo']; ?>" selected>
                                <?php echo $row['tipo']; ?></option>
                                <option value=1>1</option>
                                 <option value=3>3</option>
                                 <option value=4>4</option>
                            <?php
                            }
                            elseif ($row['tipo']==3)
                            {
                            
                            ?>

                            <option value="<?php echo $row['tipo']; ?>" selected>
                            <?php echo $row['tipo']; ?></option>
                                 <option value=1>1</option>
                                 <option value=2>2</option>
                                 <option value=4>4</option>
                           
                              
                                 <?php
                            }
                            elseif ($row['tipo']==4)
                            {
                            
                            ?>

                            <option value="<?php echo $row['tipo']; ?>" selected>
                            <?php echo $row['tipo']; ?></option>
                                 <option value=2>1</option>
                                 <option value=2>2</option>
                                 <option value=3>3</option>



                            <?php
                            }
                            ?>
                          
                     </select>
                     &nbsp; 
                     &nbsp; 
                     &nbsp; 
                   

                     (1 - Administrador &nbsp; &nbsp;      2 - Utilizador   &nbsp; &nbsp;       3 - Reparador &nbsp; &nbsp;       4 - Funcionário)
              
                                 
                                 
                  <div  style=" text-align:center;width:90%"> 
                  <input  type = "submit" value = "Atualizar"/>     </div>
                 </form>


                 <form action = "<?php echo SVRURL ?>utiliz" method="post" >
<input type = "hidden" name = "" value = "">
<input title="Voltar" type=image 
src="<?php echo SVRURL ?>images/voltar.svg"  >

</form>
<?php
}


?>


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