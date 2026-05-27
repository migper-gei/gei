<?php
// Sessão segura
if (session_status() === PHP_SESSION_NONE) {
    $isHttps = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off');
    session_name('gei_session');
    session_set_cookie_params([
        'lifetime' => 0,
        'path'     => '/',
        'secure'   => $isHttps,
        'httponly' => true,
        'samesite' => 'Lax',
    ]);
    session_start();
    // Regenerar ID periodicamente (previne session fixation)
    if (!isset($_SESSION['_created'])) {
        $_SESSION['_created'] = time();
    } elseif (time() - $_SESSION['_created'] > 1800) {
        session_regenerate_id(true);
        $_SESSION['_created'] = time();
    }
}

// Gerar token CSRF se não existir
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}
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
      <?php include("loader.php"); ?>


     <?php include ("header.php");?>
     


     <?php
//session_start();



include("sessao_timeout.php");

//include("verifica_sessao.php");


 
  ?>
      
      <!-- about -->
      <div  class="about">
         <div class="container">
            <div class="row">
               <div class="col-md-12">
               <!-- Breadcrumb melhorado -->
               <nav style="margin-bottom:10px;">
                  <ol style="display:flex;align-items:center;flex-wrap:wrap;gap:4px;list-style:none;padding:0;margin:0;font-size:.8rem;font-weight:600;color:#7b88a0;">
                     <li style="display:flex;align-items:center;gap:4px;">
                        <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="#4b6cb7" stroke-width="2.3" stroke-linecap="round" stroke-linejoin="round"><line x1="8" y1="6" x2="21" y2="6"/><line x1="8" y1="12" x2="21" y2="12"/><line x1="8" y1="18" x2="21" y2="18"/><line x1="3" y1="6" x2="3.01" y2="6"/><line x1="3" y1="12" x2="3.01" y2="12"/><line x1="3" y1="18" x2="3.01" y2="18"/></svg>
                        <span style="color:#4b6cb7;">—</span>
                     </li>
                     <li style="color:#c5cde0;font-size:.9rem;">&#8250;</li>
                     <li style="color:#1e2a45;">Avarias</li>
                  </ol>
               </nav>
            
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


$sql3 = "select max(data_fim) as md, max(ano_lectivo) as mal from periodos";
$result3 = mysqli_query($db,$sql3); 
$rows3 =mysqli_fetch_array($result3);

$maxdata=$rows3[0];
$dataatual=date('Y-m-d');
$ma=date('n');
$da=date('d');

if(strtotime($dataatual) > strtotime($maxdata)  && $ma>=8 && $da>=1)
{

$al=$rows3['mal'];
$rest = substr($al, -4); 
$rest1=$rest+1;

$novoal=$rest."/".$rest1; //.$rest+1;

$di=$rest."-"."09"."-"."01";
$df=$rest."-"."12"."-"."31";


$di2=$rest1."-"."01"."-"."01";
$df2=$rest1."-"."03"."-"."31";
$di3=$rest1."-"."04"."-"."01";
$df3=$rest1."-"."08"."-"."31";

$sql4 = "insert into periodos (ano_lectivo,num_periodo,data_inicio,data_fim) 
values ('$novoal',1,STR_TO_DATE('$di','%Y-%m-%d'),STR_TO_DATE('$df','%Y-%m-%d'))";
$result4 = mysqli_query($db,$sql4);


$sql5 = "insert into periodos (ano_lectivo,num_periodo,data_inicio,data_fim) 
values ('$novoal',2,STR_TO_DATE('$di2','%Y-%m-%d'),STR_TO_DATE('$df2','%Y-%m-%d'))";
$result5 = mysqli_query($db,$sql5);

$sql6 = "insert into periodos (ano_lectivo,num_periodo,data_inicio,data_fim) 
values ('$novoal',3,STR_TO_DATE('$di3','%Y-%m-%d'),STR_TO_DATE('$df3','%Y-%m-%d'))";
$result6 = mysqli_query($db,$sql6);


}


?>


<?php 

$sql2 = "select max(ano_lectivo) from periodos";
$result2 = mysqli_query($db,$sql2); 
$rows2 =mysqli_fetch_row($result2);

$conta = $rows2[0];





 ?>







<?php
   $sql = "SELECT * FROM escolas ORDER by id";
   $result = mysqli_query($db,$sql);
   $rowcount = mysqli_num_rows($result);
   ?>

<div class="action-section">
<h2 class="section-title"><i class="fas fa-school btn-icon"></i> Selecione a Instituição</h2>




<form name="frme" id="frme" action = "" method = "post" >
   <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($_SESSION['csrf_token'], ENT_QUOTES, 'UTF-8'); ?>">

   <div style="text-align: left;">


<select class="custom-select" title="Escolha a instituição" name="escola" onChange="showescola(this.value);">
   
   <?php
   

   
   $sql2 = "SELECT * FROM escolas ORDER by id";
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
    // Validar CSRF antes de aceitar o valor do formulário
    $csrf_post = $_POST['csrf_token'] ?? '';
    if (empty($_SESSION['csrf_token']) || !hash_equals($_SESSION['csrf_token'], $csrf_post)) {
        header('Location: ' . (defined('SVRURL') ? SVRURL : '') . 'avarias');
        exit;
    }
    $esc = (int)$_POST["escola"]; // cast para inteiro — previne SQL injection
} else {
    $esc = (int)$nes;
}


?>

<?php
 

 $stmt_ne = $db->prepare("SELECT nome_escola FROM escolas WHERE id = ? LIMIT 1");
 $stmt_ne->bind_param("i", $esc);
 $stmt_ne->execute();
 $ne = $stmt_ne->get_result()->fetch_row()[0] ?? '';
 $stmt_ne->close();
            
            ?>
     
     <div class="text-center mt-3">
                <span class="badge badge-primary p-2" style="font-size: 1rem;">
                    <i class="fas fa-building btn-icon"></i> <?php echo $ne; ?>
                </span>
            </div>
        </div>


  



        <div class="action-section">
        <h2 class="section-title">
        <i class="fas fa-plus-circle btn-icon"></i> 

Avarias</h2>

<div class="row">
                <div class="col-md-12 mb-3">
                <form action="<?php echo SVRURL ?>insereavaria?aves=<?php echo base64_encode($esc) ?>" method="post">
                        <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($_SESSION['csrf_token'], ENT_QUOTES, 'UTF-8'); ?>">
                        <button  title="Inserir avaria" type="submit" class="action-button btn-primary-action">
                        <i class="fa-solid fa-cash-register"></i>
                            &nbsp; Inserir nova avaria
                        </button>
                    </form>
                 </div>
      




                <div class="col-md-6 mb-3">
                  

                <form action="<?php echo SVRURL ?>myavarias?op=t" method="post" >
                    <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($_SESSION['csrf_token'], ENT_QUOTES, 'UTF-8'); ?>">
                    <button title="Minhas avarias" type="submit" class="action-button btn-secondary-action">
                            <i class="fas fa-eye btn-icon"></i> &nbsp;Minhas avarias
                        </button>
</form>
             


             
        </div>
   
        <?php
    

    
    if ($_SESSION['tipo']==1 || $_SESSION['tipo']==3)
    {
       ?>
        <div class="col-md-6 mb-3">
                  

        <form action="<?php echo SVRURL ?>last5avarias?aves=<?php echo base64_encode($esc)?>" method="post" >
<input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($_SESSION['csrf_token'], ENT_QUOTES, 'UTF-8'); ?>">
<button  type="submit" class="action-button btn-secondary-action" title="Últimas 5 avarias registadas">
<i class="fas fa-eye btn-icon"></i>   &nbsp;
Últimas 5 avarias registadas</button>
        </form >





           
      <?php 
      }
       ?>
 
  
  
               
          </div>



        </div>
        </div>



        <?php

if ($_SESSION['tipo']==1 || $_SESSION['tipo']==3)
{

   

?>

        <div class="action-section">
            <h2 class="section-title"><i class="fas fa-search btn-icon"></i> Visualizar avarias</h2>
            
            <?php



            $sqla = $db->prepare("SELECT  DISTINCT(s.nome) as no, s.id as sid
FROM escolas e, salas s, avarias_reparacoes ar
where e.id=s.id_escola and ar.id_sala=s.id
and e.id=? and ar.datareparacao is null
order by s.nome");
            
            $sqla->bind_param("i", $esc);
            $sqla->execute();
            $resulta = $sqla->get_result();
            $rowcount = mysqli_num_rows($resulta);
            ?>
            
            <form action="<?php echo SVRURL ?>reparacoes_efetuar_sala.php?x=<?php echo base64_encode(0) ?>&&op=t&&ies=<?php echo base64_encode($esc)?>" method="post" >
                <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($_SESSION['csrf_token'], ENT_QUOTES, 'UTF-8'); ?>">

                <div class="row align-items-end">
                    <div class="col-md-8 mb-3">
                        <label for="salaSelect" class="form-label"><i class="fas fa-door-open btn-icon"></i> Selecione a Sala:</label>
                        <select id="salaSelect" class="custom-select" name="sala" required>
                            
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
                            <i class="fas fa-eye btn-icon"></i> &nbsp;Ver avarias
                        </button>
                    </div>
                </div>
            </form>


            
        </div>
        






        <?php } ?>



   



                    </div>
               
               </div>
            </div>
         </div>
      </div>
      <!-- end about -->
    


      <?php include ("footer.php");?>


   </body>
</html>