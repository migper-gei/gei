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

// Validar token CSRF em todos os pedidos POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $csrf_post = $_POST['csrf_token'] ?? '';
    if (empty($_SESSION['csrf_token'])
        || !hash_equals($_SESSION['csrf_token'], $csrf_post)) {
        http_response_code(403);
        exit('Pedido inválido.');
    }
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
                        <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="#4b6cb7" stroke-width="2.3" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="3"/><path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 0 1-2.83 2.83l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-4 0v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 0 1-2.83-2.83l.06-.06A1.65 1.65 0 0 0 4.68 15a1.65 1.65 0 0 0-1.51-1H3a2 2 0 0 1 0-4h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 0 1 2.83-2.83l.06.06A1.65 1.65 0 0 0 9 4.68a1.65 1.65 0 0 0 1-1.51V3a2 2 0 0 1 4 0v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 0 1 2.83 2.83l-.06.06A1.65 1.65 0 0 0 19.4 9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 0 1 0 4h-.09a1.65 1.65 0 0 0-1.51 1z"/></svg>
                        <span style="color:#4b6cb7;">CONFIGURAÇÕES</span>
                     </li>
                     <li style="color:#c5cde0;font-size:.9rem;">&#8250;</li>
                     <li style="color:#1e2a45;">REQUISIÇÃO >> ATUALIZAR</li>
                  </ol>
               </nav>
               <div class="titlepage">
                     <h2>Atualiza requisicao</h2>
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
$idreq = (int)base64_decode($_GET['url']);
?>


<?php
if ( !isset($idreq) || empty($idreq) || !is_numeric($idreq) 
)

{
?>


<script>
window.setTimeout(function() {
    window.location.href = '<?php echo SVRURL ?>myrequi';
}, 10);
</script>


<?php
}
?>





<?php

if (isset($idreq))
{
$url = explode('/',$idreq);
}
else
{ 
    ?>
    
<script>
window.setTimeout(function() {
    window.location.href = '<?php echo SVRURL ?>myrequi';
}, 10);
</script>

<?php
}
?>


<?php

   
   //else $tmp="";
if ($_SERVER["REQUEST_METHOD"] == "POST" )
{

    //$sql0 = "delete from requisicao 
    //where id=".$url[0]."";
   // $result0 = mysqli_query($db,$sql0);
    
    
  



$a=$_POST["horainicio"];
$hi1 = strtotime($a);
$hi= date('H:i:s', $hi1);



$_data_util = $_POST["datautil"] ?? '';
$_sala_req  = (int)($_POST["sala"] ?? 0);
$_hini      = $_POST["horainicio"] ?? '';
$stmt_c = $db->prepare("SELECT count(*) as c1 FROM requisicao r WHERE r.datautil=STR_TO_DATE(?,'%Y-%m-%d') AND r.horafim > ? AND r.horainicio < ? AND r.id_sala=? AND r.id != ?");
$stmt_c->bind_param("sssii", $_data_util, $_hini, $_hini, $_sala_req, $idreq);
$stmt_c->execute();
$rows_c = $stmt_c->get_result()->fetch_assoc();
$stmt_c->close();
$conta = (int)$rows_c['c1'];


//se sala, datautil e horas disponíveis (conta=0)
if ($conta==0)
{

    $escolhas= $_POST['eqrequi'];

    if(isset($escolhas)) {


     $ceq=0;
    foreach ($escolhas as $key => $value)
    {
    $ideq=$value;

//and r.horafim>'".$hi."'
    $sql3 = "
    SELECT count(*) as c3
    from requisicao r, equip_requisitado er
    where r.id=er.id_req
    and r.datautil=STR_TO_DATE('".$_POST["datautil"]."','%Y-%m-%d')
    and er.id_equip=$ideq
    and r.id<>".$idreq."
    and horainicio<'".$hi."'
    and r.dataentrega is null;";
    
    $result3 = mysqli_query($db,$sql3);
    $rows3 =mysqli_fetch_row($result3);
    $contaideq = $rows3[0];



    if ($contaideq==1)
    {
    $ceq=$ceq+1;
    }


    }
    }


    if ($ceq>0)
    {
    ?>
        <script>
    
        swal({
    title: 'A requisição não foi atualizada!',
    text: 'Algum equipamento que selecionou está requisitado.  Consulte a tabela das requisições para o dia.',
    icon: 'error',
    //buttons: false,
    
    })
    .then(function() {
    window.location = "<?php echo SVRURL ?>atualiza_requisicao.php?ri=<?php echo base64_encode($idreq);?>";
    });
    
    
    </script>


<?php
    }
    elseif($ceq==0)
    {

      
      $stmt_del = $db->prepare("DELETE FROM equip_requisitado WHERE id_req=?");
      $stmt_del->bind_param("i", $idreq);
      $stmt_del->execute();
      $stmt_del->close();
      
      

      $_r_datautil = $_POST["datautil"]    ?? '';
      $_r_hi       = $_POST["horainicio"] ?? '';
      $_r_hf       = $_POST["horafim"]    ?? '';
      $_r_sala     = (int)($_POST["sala"] ?? 0);
      $stmt_upd = $db->prepare("UPDATE requisicao SET datautil=STR_TO_DATE(?,'%Y-%m-%d'), horainicio=?, horafim=?, id_sala=? WHERE id=?");
      $stmt_upd->bind_param("sssii", $_r_datautil, $_r_hi, $_r_hf, $_r_sala, $idreq);
      $stmt_upd->execute();
      $stmt_upd->close();





foreach ($escolhas as $key => $value)
    {
   
    $ideq=$value;

    
$stmt_er2 = $db->prepare("INSERT INTO equip_requisitado (id_req,id_equip) VALUES (?,?)");
$stmt_er2->bind_param("ii", $idreq, $ideq);
$stmt_er2->execute();
$stmt_er2->close();
    }

        



mysqli_close($db);

    ?>

<script>
    
        swal({
    title: 'A requisição foi atualizada!',
    //text: 'Algum equipamento já requisitado para essa data, horas e sala!',
    icon: 'success',
    //buttons: false,
    
    })
    .then(function() {
    window.location = "<?php echo SVRURL ?>myrequi";
    });
    
    
    </script>


<?php
    }



}//fim do ceq==0


elseif($conta >= 1)
{
    
?>

<script>
    
    swal({
title: 'A requisição não foi efetuada!',
text: 'Verificar data da utilização, horas e sala.',
icon: 'error',
//buttons: false,

})
.then(function() {
window.location = "<?php echo SVRURL ?>atualiza_requisicao.php?ri=<?php echo base64_encode($idreq);?>";
});


</script>


<?php


}


?>



<?php
}
?>


<br><br><br><br><br><br><br><br>
                    </div>
               
               </div>
            </div>
         </div>
      </div>
      <!-- end about -->
    


      <?php include ("footer.php");?>


   </body>
</html>