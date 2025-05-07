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


     <?php include ("header.php");
  
     ?>
     


     <?php


include("sessao_timeout.php");



 
  ?>
      
      <!-- about -->
      <div  class="about">
         <div class="container">
            <div class="row">
               <div >
      
               <a href="#" class="btn btn-secondary disabled">Equipamentos</a>



               </div>
            </div>
            
            <div class="container">
               <div class="row">
                  <div class="col-md-10 offset-md-2">
              
                        

 <!-- Welcome Section -->
 <div class="welcome-section">
               
               <div>
             
                   <?php include("msg_bemvindo.php"); ?>
               </div>
      
       </div>
    




    
<script language="javascript">
	function clickMe3()
	{
	
    var datar = (document.forms.requisi.elements.datareq.value);
    var dr=new Date(Date.parse(datar));

    var da=Date.now();

//    alert (da);

  //  alert(dr);



		if ( dr <= da )
				{
			//alert(" A data final deve ser superior à data inicial");
   
         event.preventDefault(); // prevent form submit

swal({

title: "A data da requisição deve ser superior à data atual!",
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
		
    else {
      return true;

  }
	}
</script>



<script language="javascript" type="text/javascript">

function showescola(escola) {

    document.frme.submit();

}

</script>





<script>


function a2(ne,es) {

  var es1,ne1;

es1=es;
ne1=ne;



 //alert(es1);

  event.preventDefault(); // prevent form submit

   swal({

  title: "Deseja eliminar todos os equipamentos informáticos (todas as avarias serão eliminadas)?",
 text: "Instituição: "+es1+" ",
  type: "warning",
  showCancelButton: true,
  //confirmButtonColor: "#DD6B55",


  confirmButtonText: "Sim",
  cancelButtonText: "Não",
  closeOnConfirm: false,
  closeOnCancel: false
 
},
function(isConfirm){
  if (isConfirm) {
    
    
        window.setTimeout(function() {
    window.location.href = '<?php echo SVRURL ?>eliminaequiesc/'+ne1;
}, 10);


          
  } else {
    swal("Cancelado.");

 

  }

});

}
</script>






<?php
   $sql = "SELECT * FROM escolas ORDER by id";
   $result = mysqli_query($db,$sql);
   $rowcount = mysqli_num_rows($result);
   ?>


<div class="action-section">
    
<h2 class="section-title"><i class="fas fa-school btn-icon"></i> Selecione a Instituição</h2>






<form name="frme" id="frme" action = "" method = "post" >

   <div style="text-align: left;">


<select class="custom-select" title="Escolha a instituição" name="escola" onChange="showescola(this.value);">
   
   <?php
   
   
   //echo $idescola;
   

   
   $sql2 = "SELECT * FROM escolas ORDER by id";
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
$limit = 1;

$sql4 =  $db->prepare("select id from escolas limit ?");
$sql4->bind_param("i", $limit);
$sql4->execute();
$rows4 = $sql4->get_result()->fetch_row();

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








        
<?php
  
if ( $_SESSION['tipo']==1)
{
   
   

   $sql3 = "SELECT count(eq.id) FROM 
   equipamento eq, salas s
   WHERE eq.id_sala=s.id
   and s.id_escola=$esc";
 
   $result3 = mysqli_query($db,$sql3);

   $rows3 =mysqli_fetch_row($result3);


   $contaeq = $rows3[0];
   
   
   ?>
   <div style="text-align: right;">
   <a href="<?php echo SVRURL ?>equipamentos_csv.php?id=<?php echo base64_encode($esc);?>" target="_blank">
                    <button title="Exportar para CSV" type="submit" class="btn btn-outline-secondary">
                        <i class="fas fa-file-csv btn-icon"></i> Exportar CSV
                    </button>
                </a>


   <?php
if ($contaeq>0) {
?>
   &nbsp;&nbsp;&nbsp;

   <a onclick="a2('<?php echo $esc;?>','<?php echo $ne;?>')" href="<?php echo SVRURL ?>elimina_equi_esc.php?id=<?php echo base64_encode($esc);?>" target="_blank">
                    <button title="Eliminar todos os equipamentos informáticos da escola" type="submit" class="btn btn-danger-action">
                        <i class="fas fa-trash-alt btn-icon"></i> Eliminar Todos
                    </button>
                </a>

   <?php
}
?>


</div>
<br>



<div class="action-section">
<h2 class="section-title"><i class="fas fa-plus-circle btn-icon"></i> Adicionar Equipamentos</h2>

<div class="row">
                <div class="col-md-6 mb-3">
                    <form action="<?php echo SVRURL ?>inserirequip?x=<?php echo base64_encode(0) ?>&&ies=<?php echo base64_encode($esc) ?>" method="post">
                        <button type="submit" class="action-button btn-primary-action">
                            <i class="fas fa-laptop btn-icon"> </i> 
                            &nbsp; Inserir Equipamento Informático
                        </button>
                    </form>
                 </div>
      

                <div class="col-md-6 mb-3">
                    <form action="<?php echo SVRURL ?>inseriroutequip?x=<?php echo base64_encode(0) ?>&&ies=<?php echo base64_encode($esc) ?>" method="post">
                        <button type="submit" class="action-button btn-primary-action">
                            <i class="fas fa-plug btn-icon"></i> &nbsp;Inserir Outro Equipamento
                        </button>
                    </form>
             
        </div>
   
        </div>

        </div>



<?php
}
?>


<?php



$sqla =  $db->prepare("SELECT  DISTINCT(s.nome) as no, s.id as sid
FROM escolas e, salas s, equipamento eq
where e.id=s.id_escola and eq.id_sala=s.id 
and e.id=?
");

$sqla->bind_param("i", $esc);
$sqla->execute();
$resulta = $sqla-> get_result();
//$resulta = mysqli_query($db,$sqla);
?>


<div class="action-section">
            <h2 class="section-title"><i class="fas fa-search btn-icon"></i> Visualizar Equipamentos</h2>
            
            <?php
            $sqla = $db->prepare("SELECT DISTINCT(s.nome) as no, s.id as sid
                FROM escolas e, salas s, equipamento eq
                WHERE e.id=s.id_escola AND eq.id_sala=s.id 
                AND e.id=?");
            
            $sqla->bind_param("i", $esc);
            $sqla->execute();
            $resulta = $sqla->get_result();
            $rowcount = mysqli_num_rows($resulta);
            ?>
            
            <form action="<?php echo SVRURL ?>verequipsala?x=<?php echo base64_encode(0) ?>&&ies=<?php echo base64_encode($esc)?>" method="post" class="needs-validation" novalidate>
                <div class="row align-items-end">
                    <div class="col-md-8 mb-3">
                        <label for="salaSelect" class="form-label"><i class="fas fa-door-open btn-icon"></i> Selecione a Sala:</label>
                        
                        
                        <select id="salaSelect" class="form-control required-field" name="sala" required>
                            <?php
                            if ($rowcount > 0) {
                            while($rowa = mysqli_fetch_array($resulta)) {
                                echo('<option value="'.$rowa['sid'].'">'.$rowa['no'].'</option>');
                            }
                        } else {
                            echo('<option value="">Sem salas disponíveis</option>');
                        }
                   
                            ?>
                        </select>
                    </div>
                    
                    <div class="col-md-4 mb-1">
                        <button type="submit" class="action-button btn-secondary-action">
                            <i class="fas fa-eye btn-icon"></i> &nbsp;Ver Equipamentos
                        </button>
                    </div>
                </div>
            </form>
        </div>
        
        <?php if ($_SESSION['tipo'] == 1) { ?>
        <!-- Labels and Barcodes -->
        <div class="action-section">
            <h2 class="section-title"><i class="fas fa-tags btn-icon"></i> Etiquetas e Códigos de Barras</h2>
            
            <div class="row">
                <div class="col-md-6 mb-3">
                    <?php
                    $sqla2 = $db->prepare("SELECT DISTINCT(s.nome) as no, s.id as sid
                        FROM escolas e, salas s, equipamento eq
                        WHERE e.id=s.id_escola AND eq.id_sala=s.id 
                        AND e.id=?
                        ORDER BY s.nome");
                    
                    $sqla2->bind_param("i", $esc);
                    $sqla2->execute();
                    $resulta2 = $sqla2->get_result();
                    $rowcount = mysqli_num_rows($resulta2);
                    ?>
                    
                    <form action="<?php echo SVRURL ?>criar_etiq.php?escola=<?php echo base64_encode($esc) ?>" method="post" target="_new"  class="needs-validation" novalidate>
                        <div class="form-group">
                            <label><i class="fas fa-tag btn-icon"></i> Sala para Etiquetas:</label>
                            <select class="form-control required-field"  name="salaet" required>
                                <?php
                                if ($rowcount > 0) {
                                while($rowa2 = mysqli_fetch_array($resulta2)) {
                                    echo('<option value="'.$rowa2['sid'].'">'.$rowa2['no'].'</option>');
                                }
                            } else {
                                echo('<option value="">Sem salas disponíveis</option>');
                            }
                       
                                ?>
                            </select>
                        </div>
                        <button type="submit" class="action-button btn-outline-action">
                            <i class="fas fa-print btn-icon"></i> &nbsp;Ver Etiquetas
                        </button>
                    </form>
                </div>
                
                <div class="col-md-6 mb-3">
                    <?php
                    $sqla3 = $db->prepare("SELECT DISTINCT(s.nome) as no, s.id as sid
                        FROM escolas e, salas s, equipamento eq
                        WHERE e.id=s.id_escola AND eq.id_sala=s.id
                        AND e.id=?
                        ORDER BY s.nome");
                    
                    $sqla3->bind_param("i", $esc);
                    $sqla3->execute();
                    $resulta3 = $sqla3->get_result();
                    $rowcount = mysqli_num_rows($resulta3);
                    ?>
                    
                    <form action="<?php echo SVRURL ?>criar_codbar.php?escola=<?php echo base64_encode($esc) ?>" method="post" target="_new"
                     class="needs-validation" novalidate>
                        <div class="form-group">
                            <label><i class="fas fa-barcode btn-icon"></i> Sala para Códigos de Barras:</label>
                            <select class="form-control required-field"  name="salacod" required>
                                <?php
                                if ($rowcount > 0) {
                                while($rowa3 = mysqli_fetch_array($resulta3)) {
                                    echo('<option value="'.$rowa3['sid'].'">'.$rowa3['no'].'</option>');
                                }
                            } else {
                                echo('<option value="">Sem salas disponíveis</option>');
                            }
                       
                                ?>
                            </select>
                        </div>
                        <button type="submit" class="action-button btn-outline-action">
                            <i class="fas fa-qrcode btn-icon"></i>&nbsp;Ver Códigos de Barras
                        </button>
                    </form>
                </div>
            </div>
        </div>
        <?php } ?>



 
        <?php if ($_SESSION['tipo'] != 4) { ?>
        <!-- Requisitions -->
        <div class="action-section">
            <h2 class="section-title"><i class="fas fa-clipboard-list btn-icon"></i> Requisições</h2>
            
            <?php
            $date = date("Y-m-d");
            $mod_date = strtotime($date."+ 1 days");
            ?>
            
            <div class="row">
                <div class="col-md-6 mb-3">
                    <form name="requisi" action="<?php echo SVRURL ?>reqequip?x=<?php echo base64_encode(0) ?>&&rei=<?php echo base64_encode($esc) ?>" method="post">
                        <div class="form-group">
                            <label><i class="far fa-calendar-alt btn-icon"></i> Data de Requisição:</label>
                            <input value="<?php echo date("Y-m-d", $mod_date); ?>" class="date-input" required type="date" name="datareq">
                        </div>
                        <button onclick="return clickMe3();" type="submit" class="action-button btn-primary-action">
                            <i class="fas fa-plus-circle btn-icon"></i> &nbsp;Nova Requisição
                        </button>
                    </form>
              
                    </div>
                    
                    <div class="col-md-6 mb-3">
             <br><bR>
                    <form action="<?php echo SVRURL ?>myrequi" method="post">
                       
                        <button type="submit" class="action-button btn-secondary-action">
                            <i class="fas fa-history btn-icon"></i>&nbsp; Minhas Requisições
                        </button>
                    </form>
            </div>
            </div>
        </div>
        <?php } ?>




</div>
             
            </div>
         </div>
      </div>

 
      <!-- end about -->
    
      <?php include ("jquery_bootstrap.php");?>

      <?php include ("footer.php");?>

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


   </body>
</html>