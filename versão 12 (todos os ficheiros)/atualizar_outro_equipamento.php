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

//include("verifica_sessao.php");

//print_r(scandir(session_save_path()));


 
  ?>


<?php
               
                 
               $id= base64_decode($_GET['ide']);
               $sa= base64_decode($_GET['sai']);
               $idescola=base64_decode($_GET['ies']);

               $sql2a = "select max(id) as me  from escolas ";
               $result2a = mysqli_query($db,$sql2a); 
               $rows2a =mysqli_fetch_row($result2a);
               
               
               $maxesc = $rows2a[0];



               if ($idescola>$maxesc || $idescola<0 
               ||  !isset($id)   || !is_numeric($id) 
               || !isset($idescola)  || empty($idescola)  || !is_numeric($idescola)  
               || !isset($sa)   ||  empty($sa)
               )
               {
               ?>
               
               <script>
               window.setTimeout(function() {
                  window.location.href = '<?php echo SVRURL ?>ver_equipamentos_sala.php?x=<?php echo base64_encode (2) ?>&&si=<?php echo base64_encode ($sa) ?>&&ies=<?php echo base64_encode ($idescola) ?>';
               }, 10);
               </script>
               
               
               <?php
               
               }



                $sql3 ="select oe.*,s.*, es.nome_escola
                from outro_equipamento oe, salas s, escolas es
                where oe.id_sala=s.id and s.id_escola=es.id
                and oe.id=$id and s.id=$sa and es.id=$idescola";
                $result3 = mysqli_query($db,$sql3); 
                $row3=mysqli_fetch_array($result3);
               
                $nr = mysqli_num_rows($result3);

             
               ?>
      

      <?php
      //echo $nr;

     if ($nr==0 )
{
?>

<script>
window.setTimeout(function() {
    window.location.href = '<?php echo SVRURL ?>verequipsala?x=<?php echo base64_encode(1) ?>&&si=<?php echo base64_encode($sa)?>&&ies=<?php echo base64_encode($idescola)?>';
}, 10);
</script>


<?php

}

?>




      <!-- about -->
      <div  class="about">
         <div class="container">
            <div class="row">
               <div class="col-md-12">
               <div class="titlepage">
                     <h2>Atualizar outro equipamento <br>
                     <?php echo $row3['nomeoutro'] ?>  | <?php echo $row3['nome'] ?> | <?php echo $row3['nome_escola'] ?>
                  </h2>

                    <br>
                  </div>
               </div>
            </div>
            
            <div class="container">
               <div class="row">
                  <div class="col-md-10 offset-md-1">
              
                        
<?php
include("msg_bemvindo.php");
?>
<br>

  

<form name="equipamento" action = "<?php echo SVRURL ?>atualiza_outro_equipamento_OK.php?ide=<?php echo base64_encode($id)?>&&sai=<?php echo base64_encode($sa)?>&&ies=<?php echo base64_encode($idescola)?>" method = "post">


                    


      

            

                                              
<br>

<input required type = "text" name = "nomeq" value="<?php echo $row3['nomeoutro']?>" style=" background-color:#CEF6CE;" >
                    
              
<br>


<label>Quantidade: </label>  <br>
     
     <input  style=" background-color:#CEF6CE;" value="<?php echo $row3['qta']?>" required  min="1" type = "number" name = "qta" placeholder="Quantidade">  

     <br />  
     
                

                     
                 
                  <br>
                   <textarea  type = "text" rows="3" cols="100"  name="obs" placeholder="Observações" ><?php echo $row3['observacoes']?></textarea>
                   <br>
     




<div  style=" text-align:center;width:90%"> <input  type = "submit" value = "Atualizar"/>     </div>
  
                 </form>

<form action = "<?php echo SVRURL ?>ver_equipamentos_sala.php?x=<?php echo base64_encode (2) ?>&&si=<?php echo base64_encode ($sa) ?>&&ies=<?php echo base64_encode ($idescola) ?>" method="post" >
<input type = "hidden" name = "sala" value = "">
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