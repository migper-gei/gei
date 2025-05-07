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
      <!-- loader --> 
      <div class="loader_bg">
         <div class="loader"><img src="<?php echo SVRURL ?>images/loading.gif" alt="Loading" /></div>
      </div>
      <!-- end loader -->


     <?php include ("header.php");?>
     


     <?php
//session_start();

include ("css_inserir.php");

include("sessao_timeout.php");

//include("verifica_sessao.php");

//print_r(scandir(session_save_path()));


 
  ?>

<?php
               

               if (isset($_GET['url']) 
               &&  is_numeric($_GET['url'])  &&  is_numeric($_GET['url2'])  &&  is_numeric($_GET['url3'])  )
               {
               $url = explode('/',$_GET['url']);
               $url2 = explode('/',$_GET['url2']);
               $url3 = explode('/',$_GET['url3']);
               
               
               //echo $url[0];
               //echo $url2[0];
               //echo $url3[0];
               }
               else
               {
                //echo ("aaa");
                   ?>
                   
               <script>
               window.setTimeout(function() {
                   window.location.href = '<?php echo SVRURL ?>equip';
               }, 10);
               </script>
            
               <?php
               }
               
?>



<?php



   $sql = "SELECT e.nomeequi, s.nome ,es.nome_escola,es.id
   FROM equipamento e, salas s, escolas es
   WHERE e.id_sala=s.id and s.id_escola=es.id
   and e.id=".$url[0]." ";
   $result = mysqli_query($db,$sql);
   $rows1 =mysqli_fetch_row($result);
  
   $conta = $result->num_rows;

//echo $row_cnt;


if ($conta==0)
{

?>

<script>
window.setTimeout(function() {
    window.location.href = '<?php echo SVRURL ?>equip';
}, 10);
</script>


<?php
}
?>

<?php //echo $rows1[3]; 
$idesc=$rows1[3]; 

//echo $idesc;
?>

      
      <!-- about -->
      <div  class="about">
         <div class="container">
            <div class="row">
               <div class="col-md-12">


               <a href="#" class="btn btn-secondary disabled">Equipamentos >> Ver equipamentos da sala >> Mudar de sala </a>
               <div class="titlepage">
                     <h2>

         
                     <?php echo($rows1[0]);?> | <?php echo $rows1[1]; ?> | <?php echo $rows1[2]; ?>
                     </h2>
                  </div>
               </div>
            </div>
            
            <div class="container">
               <div class="row">
                  <div class="col-md-10 offset-md-2">
              
                        

                  <div class="welcome-section">
<?php
include("msg_bemvindo.php");
?>
     </div>
    


<script language="javascript" type="text/javascript">

function showescola(escola) {

    document.frme.submit();

}

</script>



<?php 


$sql = "SELECT * FROM escolas ORDER by nome_escola";
$result = mysqli_query($db,$sql);
$rowcount = mysqli_num_rows($result);
 ?>


  





<form name="frme" id="frme" action = "" method = "post" >


<div class="action-section">
    
<h2 class="section-title"><i class="fas fa-school btn-icon"></i> Selecione a Instituição</h2>



   <div style="text-align: left;">


   <select required  title="Escolha a instituição" name="escola" onChange="showescola(this.value);" class="form-control required-field" >
   
 
   
  
   
   
   <?php
   
   
   //echo $idescola;
   
   
   
   $sql2 = "SELECT * FROM escolas ORDER by nome_escola";
   $result2 = mysqli_query($db,$sql2);
   
   //echo('<option value=""> Escolha a escola  </option>');  
   
   while($row2=mysqli_fetch_array($result2))
   {
   
      if ($row2['id']==$_REQUEST["escola"])
      {
        //'.$row2['nome_escola'].'
         echo('<option selected value="'.$row2['id'].'">'.$row2['nome_escola'].'</option>');
   
   
      }
      else
   
     echo('<option value="'.$row2['id'].'">'.$row2['nome_escola'].'</option>');
   
   
   }
   
   
   echo('</select>');
   
   ?>
   
   </div>
   
</form>
    
<?php
$sql4 = "select id from escolas limit 1";
$result4 = mysqli_query($db,$sql4); 
$rows4 =mysqli_fetch_row($result4);


$nes = $rows4[0];

//echo $nes;

//$nes=$idesc;


if (!empty($_POST["escola"])) {
              
   $esc=$_POST["escola"];
   
   }
   else{
    $esc=$nes;  //1;
   }

   //echo $esc;


?>

<?php

$sql1 = "select nome_escola
 from escolas 
 where id=$esc";
 $result1 = mysqli_query($db,$sql1); 
 $rows =mysqli_fetch_row($result1);
 
 
 $ne = $rows[0];
            
 //echo $ne;
            ?>
     
     <div class="text-center mt-3">
                <span class="badge badge-primary p-2" style="font-size: 1rem;">
                    <i class="fas fa-building btn-icon"></i> <?php echo $ne; ?>
                </span>
            </div>
        </div>







<br>


  




    <?php
    
//echo($_SESSION['tipo']);

    if ($_SESSION['tipo']==1 )
    {

       
   
   ?>
  








<?php
//echo $url3[0];



$sqla = "SELECT  DISTINCT(s.nome) as no, s.id as sid
FROM escolas e, salas s
where e.id=s.id_escola and s.id<>$url3[0]
and e.id=$esc 
order by s.nome";

$resulta = mysqli_query($db,$sqla);
?>



<form action="<?php echo SVRURL ?>mudarsalaequi_ok.php?id=<?php echo($url[0]);?>&&sala=<?php echo($url3[0]);?>&&escola=<?php echo $idesc?>" method="post" class="needs-validation" novalidate>



<div class="action-section">


<label>Sala:</label>  


    
<select  class="form-control required-field" name="sala" required>


<?php
while($rowa=mysqli_fetch_array($resulta))
{

      echo('<option value="'.$rowa['sid'].'">'.$rowa['no'].'</option>');

        }
?>     
</select>
</div>

<?php } ?>









    <div class="form-group">
              
                </div>
                <br>
                <div class="form-group">
                <div  style=" text-align:center;width:100%">
                <button type="submit" name="submit" class="btn-submit">
                <i class="fa-solid fa-file-import"></i>
                                        &nbsp;Mudar equipamento de sala
                                    </button>
    </div>



   </form>
       

<br>
   
   <div class="text-center mt-3">
                            <a class="btn btn-secondary" title="Voltar" href="<?php echo SVRURL ?>verequipsala?x=<?php echo base64_encode(1) ?>&&ies=<?php echo base64_encode($url2[0])?>&&si=<?php echo base64_encode($url3[0])?>">
                                <i class="bi bi-arrow-left"></i> Voltar
                            </a>
                            <br>  <br>
                        </div>





                    </div>
               
               </div>
            </div>
         </div>
      </div>
      <!-- end about -->
       
      <script>
      // Example starter JavaScript for disabling form submissions if there are invalid fields
        (function() {
            'use strict';
            window.addEventListener('load', function() {
                // Fetch all the forms we want to apply custom Bootstrap validation styles to
                var forms = document.getElementsByClassName('needs-validation');
                // Loop over them and prevent submission
                var validation = Array.prototype.filter.call(forms, function(form) {
                    form.addEventListener('submit', function(event) {
                        if (form.checkValidity() === false) {
                            event.preventDefault();
                            event.stopPropagation();
                        }
                        form.classList.add('was-validated');
                    }, false);
                });
            }, false);
        })();
    </script>



      <?php include ("footer.php");?>


   </body>
</html>