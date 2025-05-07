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
include ("css_inserir.php");


include("sessao_timeout.php");

//include("verifica_sessao.php");

//print_r(scandir(session_save_path()));


 
  ?>
      
      <!-- about -->
      <div  class="about">
         <div class="container">
            <div class="row">
               <div class="col-md-12">
               <a href="#" class="btn btn-secondary disabled">Configurações >> Outros equipamentos >> Importação</a>
               <div class="titlepage">
                    
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

function showesc(escola) {

    document.frm.submit();

}

</script>







<form name="frm" id="frm" action = "" method = "post" >

<div class="action-section">
    
    <h2 class="section-title"><i class="fas fa-school btn-icon"></i> Selecione a Instituição</h2>



<select  required class="form-control required-field"  style="width:100%;" name="escola" onChange="showesc(this.value);">


<?php


//echo $idescola;



$sql2 = "SELECT * FROM escolas ORDER by nome_escola";
$result2 = mysqli_query($db,$sql2);






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

</form>

<br>


<?php

$sql4 = "select id from escolas limit 1";
$result4 = mysqli_query($db,$sql4); 
$rows4 =mysqli_fetch_row($result4);


$nes = $rows4[0];




if (!empty($_POST["escola"])) {
              
              $esc=$_POST["escola"];
              
              }
              else{
               $esc=$nes;  //1;
            
              }
          
  //echo $esc;
?>



<?php


$sqla = "SELECT  DISTINCT(s.nome) as no, s.id as sid
FROM escolas e, salas s
where e.id=s.id_escola
and e.id=$esc
order by s.nome";

$resulta = mysqli_query($db,$sqla);
?>


<?php

$sql1 = "select nome_escola
from escolas 
where id=$esc";
$result1 = mysqli_query($db,$sql1); 
$rows =mysqli_fetch_row($result1);


$ne = $rows[0];
           
           ?>
    
    <div class="text-center mt-3">
<span class="badge badge-primary p-2" style="font-size: 1rem;">
    <i class="fas fa-building btn-icon"></i> <?php echo $ne; ?>
</span>
</div>
</div>


<form enctype="multipart/form-data" method="post" action="<?php echo SVRURL ?>importar_outro_equip_ok.php" class="needs-validation" novalidate>
               
             


<div class="action-section">
<label>Sala:</label>  






<select name="sala" required class="form-control required-field" style="width:100%">


<?php
while($rowa=mysqli_fetch_array($resulta))
{

      echo('<option value="'.$rowa['sid'].'">'.$rowa['no'].'</option>');

        }




?>     
</select>

<!--
  
(Se lista vazia, inserir salas)
      -->

      </div>


<br>



<div class="form-group">
<div class="action-section">
                    <label for="file">Escolha o ficheiro .CSV para importar (Caso já exista o outro equipamento, não é importado)</label>
                    <input name="file" type="file" class="form-control required-field" required>
      </div>
                </div>
                <div class="form-group">
                    <?php //echo $message; ?>
                </div>
                <br>
                <div class="form-group">
                <div  style=" text-align:center;width:100%"> 
                  
                <button type="submit" name="submit" class="btn-submit">
                <i class="fa-solid fa-file-import"></i>
                                        &nbsp;Importar outros equipamentos
                                    </button>
    </div>
                         
                 
                                       
                 </form>
                 <br>
                 <div class="text-center mt-3">
                            <a class="btn btn-secondary" title="Voltar" href="<?php echo SVRURL ?>configura">
                                <i class="bi bi-arrow-left"></i> Voltar
                            </a>
                            <br> 
                        </div>




<br>

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