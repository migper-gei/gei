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



?>


<?php
$cod=base64_decode($_GET["c"]);
$da1=base64_decode($_GET["da1"]);
$da2=base64_decode($_GET["da2"]);



$sa=base64_decode($_GET["sa"]);
$ides=base64_decode($_GET["ides"]);

//echo $da1;
//echo $sa;
//echo $ides;
$sql2a = "select max(id) as me  from escolas ";
$result2a = mysqli_query($db,$sql2a); 
$rows2a =mysqli_fetch_row($result2a);


$maxesc = $rows2a[0];


if ($ides>$maxesc || $ides<0 
||  !isset($cod)   || !is_numeric($cod) 
|| !isset($ides)  || empty($ides)  || !is_numeric($ides)  
|| !isset($da1) || !isset($da2) || !isset($sa)
|| empty($da1) || empty($da2) || empty($sa)
)
{
?>

<script>
window.setTimeout(function() {
   window.location.href = '<?php echo SVRURL ?>manutencoes_sala_entredatas.php?x=<?php echo base64_encode(1)?>&&dmi=<?php echo base64_encode($da1)?>&&dmf=<?php echo base64_encode($da2)?>&&sai=<?php echo base64_encode($sa)?>&&esm=<?php echo base64_encode($ides)?>';
}, 10);
</script>


<?php

}
?>

<?php 


$sql3 = "select e.id,e.nomeequi as noeq,e.tipo, es.nome_escola, s.nome, m.*
from equipamento e, manutencao m, salas s, escolas es
where e.id=m.id_equi and e.id_sala=s.id and s.id_escola=es.id
and m.codigo=$cod
";
$result3=mysqli_query($db,$sql3);
$row3=mysqli_fetch_array($result3)
?>


      <!-- about -->
      <div  class="about">
         <div class="container">
            <div class="row">
               <div class="col-md-12">
               <a href="#" class="btn btn-secondary disabled">Manutenções >> Atualizar</a>
               <div class="titlepage">
                     <h2>
                        <br><?php echo $row3['noeq'] ?> 
                        <br>
                        <?php echo $row3['nome'] ?>
                        | <?php echo $row3['nome_escola'] ?>
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


       <script>
   
function validate() {

  var checkbox2= document.querySelector('input[name="m[]"]:checked');

 if(!checkbox2) {
    //alert('Escolha pelo menos um tipo de manutenção!');
    
    event.preventDefault(); // prevent form submit

swal({

title: "Escolha pelo menos um tipo de manutenção!",
//text: "Sala: "+s1+" (Escola: "+ne1+")",
type: "warning",
//showCancelButton: true,
//confirmButtonColor: "#DD6B55",


confirmButtonText: "OK",
//cancelButtonText: "Não",
closeOnConfirm: false,
closeOnCancel: false

} );
    
    
    return false;
  }

  else 
  return true;//confirm('Deseja continuar?');
}
  </script>


<div class="form-container">


<form  onsubmit="return validate();" method="post" 
action="<?php echo SVRURL ?>atualiza_ok_manutencao.php?c=<?php echo base64_encode($cod);?>&&e=<?php echo base64_encode($row3['id']);?>&&da1=<?php echo base64_encode($da1)?>&&da2=<?php echo base64_encode($da2)?>&&sa=<?php echo base64_encode($sa)?>&&ides=<?php echo base64_encode($ides)?>" class="needs-validation" novalidate>



<label>Data: </label>  <br>
     <input required  style="width:100%"  class="form-control required-field"    value="<?php echo $row3['data_manutencao']; ?>"       
     size="10" type = "date" name = "data" >
     <br>   
     <label>Pessoa que realizou: </label>  <br>
    <input required style="width:100%"  class="form-control required-field" type = "text" name="pessoa"   value="<?php echo $row3['pessoa']; ?>" />
    <br> 
    <br>

 
<?php

//echo $row3['descricao'];
//echo ('<br>');echo ('<br>');
?>


Escolha o tipo de manutenção:

<?php 
$sql4 = "select nome from tipos_manutencao";
$result4=mysqli_query($db,$sql4);
?>

<li class="list-group-item">  
<?php 

while($row4=mysqli_fetch_array($result4))
{
  ?>   
  <ul style="display: block; ">

<?php
//echo $row4['nome'];



if ($row4['nome'] == $row3['descricao'])
{
?>
<input  type="checkbox" checked name="m[]" value="<?php echo ($row4['nome']);?>" >
<?php echo ($row4['nome']);?>

<?php
} 
else 
{
   ?>
    <input  type="checkbox" name="m[]" value="<?php echo ($row4['nome']);?>" >
<?php echo ($row4['nome']);?>

<?php
}
?>


</ul>

<?php
}
?>          

</li> 


<br>
                   <label>Observações: </label>  <br>  
                   <textarea  rows="5" style="width:100%" class="form-select"  name="obs"><?php echo $row3['observacoes']; ?>
                   </textarea>
                  
 <bR>      <bR>   


                 
    <div  style=" text-align:center;width:100%">
    <button type="submit" class="btn-submit">
                                    <i class="fa-solid fa-pen"></i>
                                        &nbsp;Atualizar manutenção
                                    </button>  
    </div>

              

 </form>
    

</diV>


<div class="text-center mt-3">
                            <a class="btn btn-secondary" title="Voltar" href="<?php echo SVRURL ?>manutencoes_sala_entredatas.php?x=<?php echo base64_encode(1)?>&&dmi=<?php echo base64_encode($da1)?>&&dmf=<?php echo base64_encode($da2)?>&&sai=<?php echo base64_encode($sa)?>&&esm=<?php echo base64_encode($ides)?>">
                                <i class="bi bi-arrow-left"></i> Voltar
                            </a>
                   
                        </div>





<br>
<?php include ("jquery_bootstrap.php");?>

                    </div>
               
               </div>
            </div>
         </div>
      </div>
      <!-- end about -->
    
      <!-- Script para validação do formulário -->
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