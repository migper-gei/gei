<?php
  session_start();
  session_regenerate_id();
  ?>
<!DOCTYPE html>
<html lang="pt">
   <head>
   




<?php
 include ("css_inserir.php");
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
              window.location.href = '<?php echo SVRURL ?>equip';
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
               <a href="#" class="btn btn-secondary disabled">Equipamentos >> Inserir</a>
              <!--
               <div class="titlepage">
              <h1 class="text-primary"><?php echo $ne ?></h1>
                  </div>
              -->


               </div>
            </div>
            
            <div class="container">
               <div class="row">
                  <div class="col-md-10 offset-md-2">
           



           <!-- Welcome Section -->
 <div class="welcome-section">              
<?php
include("msg_bemvindo.php");
?>
</div>


       
                        <div class="form-container">
                
                        <h1 class="text-primary">
                        <?php echo $ne ?></h1>
                        
                         <div class="step-indicator">

                        

                                <i class="bi bi-info-circle-fill me-2"></i>
                                Complete todos os campos obrigatórios (indicados com fundo azul claro)
                            </div>

                            <script language="javascript" type="text/javascript">
                                function showescdig(escola) {
                                    document.frme.submit();
                                }
                            </script>

                            <form name="frme" id="frme" action="" method="post" class="mb-4">
                                <div class="form-group row">
                                    <label for="escdig" class="col-sm-4 col-form-label">Equipamento Escola Digital:</label>
                                    
                                    <div class="col-sm-8">
                                        <select onChange="showescdig(this.value);" required name="escdig" class="form-control required-field" id="escdig">
                                            <?php
                                            if ($_REQUEST["escdig"] == "Sim") {
                                                echo('<option selected value="Sim">Sim</option>');
                                                echo('<option value="Não">Não</option>');
                                            } else {
                                                echo('<option selected value="Não">Não</option>');
                                                echo('<option value="Sim">Sim</option>');
                                            }
                                            ?>     
                                        </select>
                                    </div>
                                </div>
                            </form>

                            <?php
                            if (!empty($_POST["escdig"])) {
                                $escdig = $_POST["escdig"];
                            } else {
                                $escdig = "Não";
                            }
                            ?>

                            <form name="equipamento" action="<?php echo SVRURL ?>gravaequip?ies=<?php echo base64_encode($idescola);?>&&ed=<?php echo base64_encode($escdig);?>" method="post" class="needs-validation" novalidate>
                                <?php if ($escdig == "Sim") { ?>
                                    <div class="form-group row">
                                        <label for="numinv" class="col-sm-4 col-form-label">Nº inventário Dgest:</label>
                                        <div class="col-sm-8">
                                            <input style="width:100%" required type="text" name="numinv" id="numinv" class="form-control required-field" placeholder="Nº inventário Dgest">
                                        </div>
                                    </div>

                                    <div class="form-group row">
                                        <label for="fornecedor" class="col-sm-4 col-form-label">Fornecedor:</label>
                                        <div class="col-sm-8">
                                            <input style="width:100%" required type="text" name="fornecedor" id="fornecedor" class="form-control required-field" placeholder="Fornecedor">
                                        </div>
                                    </div>

                                    <div class="form-group row">
                                        <label for="emailfornecedor" class="col-sm-4 col-form-label">Email do fornecedor:</label>
                                        <div class="col-sm-8">
                                            <input style="width:100%" required type="text" name="email" id="emailfornecedor" class="form-control required-field" placeholder="Email do fornecedor"
                                            pattern="[a-z0-9._%+-]+@[a-z0-9.-]+\.[a-z]{2,63}$">
                                        </div>
                                       
                                    </div>

                                    <div class="form-group row">
                                        <label for="nifpessoa" class="col-sm-4 col-form-label">NIF da pessoa:</label>
                                        <div class="col-sm-8">
                                            <input style="width:100%" required maxlength="9" type="text" 
                                            oninput="this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\..*)\./g, '$1');" 
                                            name="nifpessoa" id="nifpessoa" class="form-control required-field" placeholder="NIF da pessoa">
                                        </div>
                                    </div>
                                <?php } ?>

                                <div class="form-group row">
                                    <label for="tipoeq" class="col-sm-4 col-form-label">Tipo de equipamento:</label>
                                    <div class="col-sm-8">
                                        <div class="input-group">
                                            <select name="tipoeq" id="tipoeq" required  class="form-control required-field">
                                                <?php
                                                $sql = $db->prepare("SELECT DISTINCT(nome) as no FROM tipos_equipamento order by nome");
                                                $sql->execute();
                                                $result = $sql->get_result();

                                                echo('<option value=""> -- Selecione -- </option>');  

                                                while($row = mysqli_fetch_array($result)) {
                                                    echo('<option value="'.$row['no'].'">'.$row['no'].'</option>');
                                                }
                                                ?>
                                            </select>
                                            <a    style="color:Gainsboro;" class="btn btn-outline-secondary" href="<?php echo SVRURL ?>tiposequip" title="Inserir novo tipo de equipamento">
                                                <i class="bi bi-plus-circle"></i> Novo
                                            </a>
                                        </div>
                                    </div>
                                </div>

                                <div class="form-group row">
                                    <label for="sala" class="col-sm-4 col-form-label">Sala:</label>
                                    <div class="col-sm-8">
                                        <div class="input-group">
                                            <?php
                                            $sql = $db->prepare("select * FROM salas where id_escola=? order by nome");
                                            $sql->bind_param("i", $idescola);
                                            $sql->execute();
                                            $result = $sql->get_result();
                                            $rowcount = mysqli_num_rows($result);
                                            ?>

                                            <select name="sala" id="sala"  class="form-control required-field" required>
                                                <?php
                                                if ($rowcount > 0) {
                                                    echo('<option value=""> -- Selecione -- </option>');  
                                                    
                                                    while($row2 = mysqli_fetch_array($result)) {
                                                        echo('<option value="'.$row2['id'].'">'.$row2['nome'].'</option>');
                                                    }
                                                } else {
                                                    echo('<option value="">Sem salas disponíveis</option>');
                                                }
                                                ?>
                                            </select>
                                            <a    style="color:Gainsboro;" class="btn btn-outline-secondary" href="<?php echo SVRURL ?>sala?x=<?php echo base64_encode(1) ?>&&esi=<?php echo base64_encode($idescola) ?>" title="Gerir salas">
                                                <i class="bi bi-door-open"></i> Salas
                                            </a>
                                        </div>
                                        <?php if ($rowcount == 0) { ?>
                                            <div class="alert alert-warning mt-2">
                                                <i class="bi bi-exclamation-triangle"></i> A instituição não tem salas definidas.
                                            </div>
                                        <?php } ?>
                                    </div>
                                </div>

                                <div class="form-group row">
                                    <label for="nomeq" class="col-sm-4 col-form-label">Nome:</label>
                                    <div class="col-sm-8">
                                        <input style="width: 100%;" required type="text" name="nomeq" id="nomeq" class="form-control required-field" placeholder="Nome do equipamento">
                                    </div>
                                </div>

                                <div class="form-group row">
                                    <label for="nserie" class="col-sm-4 col-form-label">Nº de série:</label>
                                    <div class="col-sm-8">
                                        <input style="width: 100%;" type="text" name="nserie" id="nserie" class="form-control" placeholder="Nº de série">
                                    </div>
                                </div>

                                <div class="form-group row">
                                    <label for="marcamod" class="col-sm-4 col-form-label">Marca/Modelo:</label>
                                    <div class="col-sm-8">
                                        <input style="width: 100%;" type="text" name="marcamod" id="marcamod" class="form-control" placeholder="Marca/Modelo">
                                    </div>
                                </div>

                                <div class="form-group row">
                                    <label for="datacompra" class="col-sm-4 col-form-label">Data da compra:</label>
                                    <div class="col-sm-8">
                                        <input  style="width: 100%;" type="date" name="datacompra" id="datacompra" class="form-control" placeholder="Data da compra">
                                    </div>
                                </div>

                                <div class="form-group row">
                                    <label for="obs" class="col-sm-4 col-form-label">Observações:</label>
                                    <div class="col-sm-8">
                                        <textarea style="width: 100%;" rows="4" name="obs" id="obs" class="form-control" placeholder="Observações adicionais"></textarea>
                                    </div>
                                </div>

                                <div class="text-center mt-4">
                                    <button type="submit" class="btn-submit">
                                        <i class="bi bi-check-circle-fill me-2"></i>
                                        &nbsp;Inserir Equipamento
                                    </button>
                                </div>
                            </form>
                        </div>

                        <div class="text-center mt-3">
                            <a class="btn btn-secondary" title="Voltar" href="<?php echo SVRURL ?>equip">
                                <i class="bi bi-arrow-left"></i> Voltar
                            </a>
                            <br>  <br>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>


  
<?php
      // Clear the session
		unset($_SESSION['escola']);
?>

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