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
      

  
      <?php

$stmt2a = $db->prepare("SELECT MAX(id) AS me FROM escolas");
$stmt2a->execute();
$result2a = $stmt2a->get_result();
$rows2a = $result2a->fetch_row();
$stmt2a->close();

$maxesc = $rows2a[0];
$x = (int)base64_decode($_GET["x"]);
$idescola = (int)base64_decode($_GET["ies"]);


if ($x==2 && (empty($_POST['sala']) || !isset($_POST['sala']) )  

)

{
  $said = (int)base64_decode($_GET["si"]);
  $idescola = (int)base64_decode($_GET["ies"]);
}
else
{  


if ($x>2 || $x<0 || !is_numeric($x)
|| $idescola>$maxesc || $idescola<0 
|| !isset($x) || !isset($idescola) || !is_numeric($idescola) 
 || empty($idescola)  
)
{

?>


<script>

window.setTimeout(function() {
              window.location.href = '<?php echo SVRURL ?>equip';
          },10);
          </script>


<?php
}


if ($x==0 && (!isset($_POST["sala"]) || empty($_POST["sala"])))
{

?>


<script>

window.setTimeout(function() {
              window.location.href = '<?php echo SVRURL ?>equip';
          },10);
          </script>


<?php
}


}
     
     if ($x==1)
     {
     $said = (int)base64_decode($_GET["si"]);
     $idescola = (int)base64_decode($_GET["ies"]);
     }
     elseif ($x==0)
     {
     $said=$_POST["sala"];
     $idescola = (int)base64_decode($_GET["ies"]);
     }

     // ── Autorização horizontal ────────────────────────────────────────────────
     // Verificar que a sala pertence à escola indicada E que ambas existem na BD
     // desta sessão ($db já está ligado a $_SESSION['nobd']).
     // Um utilizador de outra instituição não consegue forjar idescola/said porque
     // os registos simplesmente não existem na sua BD.
     $stmt10 = $db->prepare(
         "SELECT s.nome FROM salas s
          INNER JOIN escolas e ON e.id = s.id_escola
          WHERE s.id = ? AND s.id_escola = ?"
     );
     $stmt10->bind_param("ii", $said, $idescola);
     $stmt10->execute();
     $result10 = $stmt10->get_result();
     $rows10   = $result10->fetch_row();
     $num_ns   = $result10->num_rows;
     $stmt10->close();

     $ns = $rows10[0] ?? '';

     $stmt11 = $db->prepare("SELECT nome_escola FROM escolas WHERE id = ?");
     $stmt11->bind_param("i", $idescola);
     $stmt11->execute();
     $result11 = $stmt11->get_result();
     $rows11   = $result11->fetch_row();
     $num_ne   = $result11->num_rows;
     $stmt11->close();

     $ne = $rows11[0] ?? '';

     // Rejeitar se sala/escola não existirem nesta BD ou se a sala não pertencer
     // à escola indicada. header+exit garante que nada mais é renderizado.
     if ($num_ns === 0 || $num_ne === 0 || $x > 2) {
         header('Location: ' . SVRURL . 'equip');
         exit;
     }
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
                        <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="#4b6cb7" stroke-width="2.3" stroke-linecap="round" stroke-linejoin="round"><rect x="2" y="7" width="20" height="14" rx="2"/><path d="M16 7V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v2"/></svg>
                        <span style="color:#4b6cb7;">Equipamentos</span>
                     </li>
                     <li style="color:#c5cde0;font-size:.9rem;">›</li>
                     <li style="color:#1e2a45;">Ver equipamentos da sala</li>
                  </ol>
               </nav>

               </div>
            </div>

            <div class="container">
               <div class="row">
                  <div class="col-md-11 offset-md-1">

           <!-- Welcome Section -->
           <div class="welcome-section"> 
<?php
include("msg_bemvindo.php");
?>
   </div>

               <!-- ========================================================
                    CABEÇALHO: sala + escola na mesma linha, por baixo do utilizador
                    ======================================================== -->
               <div style="display:flex; align-items:center; flex-wrap:wrap; gap:10px; margin:14px 0 10px; padding:10px 16px; background:#f4f6fb; border:1px solid #e3e8f4; border-radius:10px;">

                  <!-- Nome da sala em destaque -->
                  <span style="display:inline-flex; align-items:center; gap:7px; font-size:1.1rem; font-weight:700; color:#182848;">
                     <svg width="16" height="16" viewBox="0 0 24 24" fill="none"
                          stroke="#4b6cb7" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"
                          style="flex-shrink:0;">
                        <rect x="3" y="3" width="18" height="18" rx="2"/>
                        <path d="M3 9h18M9 21V9"/>
                     </svg>
                     <?php echo htmlspecialchars($ns, ENT_QUOTES, 'UTF-8'); ?>
                  </span>

                  <!-- Separador -->
                  <span style="color:#c5cde0; font-size:1.1rem; font-weight:300;">|</span>

                  <!-- Nome da escola -->
                  <span style="display:inline-flex; align-items:center; gap:6px; font-size:.92rem; font-weight:500; color:#5a6a85;">
                     <svg width="14" height="14" viewBox="0 0 24 24" fill="none"
                          stroke="#7b88a0" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"
                          style="flex-shrink:0;">
                        <path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/>
                        <polyline points="9 22 9 12 15 12 15 22"/>
                     </svg>
                     <?php echo htmlspecialchars($ne, ENT_QUOTES, 'UTF-8'); ?>
                  </span>

                  <!-- Separador -->
                  <span style="color:#c5cde0; font-size:1.1rem; font-weight:300;">|</span>

                  <!-- Link Dashboard da Sala -->
                  <a href="<?php echo SVRURL ?>dashboard_sala.php?si=<?php echo base64_encode($said); ?>&&ies=<?php echo base64_encode($idescola); ?>"
                     style="display:inline-flex; align-items:center; gap:6px; font-size:.82rem; font-weight:600; color:#4b6cb7; text-decoration:none; padding:4px 10px; background:#eef2fb; border:1.5px solid #c7d4f0; border-radius:7px; transition:all .15s;"
                     onmouseover="this.style.background='#dce6f8';this.style.borderColor='#4b6cb7';"
                     onmouseout="this.style.background='#eef2fb';this.style.borderColor='#c7d4f0';"
                     title="Dashboard da Sala">
                     <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                        <rect x="3" y="3" width="7" height="7"/><rect x="14" y="3" width="7" height="7"/>
                        <rect x="14" y="14" width="7" height="7"/><rect x="3" y="14" width="7" height="7"/>
                     </svg>
                     Dashboard
                  </a>

               </div>
               <!-- ===== FIM CABEÇALHO ===== -->
            


    <script>
function a1(n,no,ne,noeq,ides,said) {

var n0,n1,ne1,noeq1,ides,said;
n0=n; //id_equi
n1=no;  //sala
ne1=ne;  //escola
noeq1=noeq;  //nome equi
ides1=ides;  //id_escola
said1=said;  //id_sala

//alert(ides1);

 //alert(n0);

  event.preventDefault(); // prevent form submit

   swal({

  title: "Deseja eliminar? (Vai eliminar também as avarias caso seja eq. informático)",
 text: "Equipamento: "+noeq1+ "\n" +" ("+"Sala: "+n1 + " | " + "Instituição: "+ne1+")",
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
    
        //alert(n1);
        window.setTimeout(function() {
    window.location.href = '<?php echo SVRURL ?>eliminaequip/'+n0+'/'+ides1+'/'+said1;
}, 10);


          
  } else {
    swal("Cancelado.");
   // swal("Cancelled", "Your imaginary file is safe :)", "error");
   // window.setTimeout(function() {
    //window.location.href = '<?php echo SVRURL ?>ver_equipamentos_sala.php';
//}, 10);
  

  }

});

}






function a1a(n,no,ne,noeq,ides,said) {

var n0,n1,ne1,noeq1,ides,said;
n0=n; //id_equi
n1=no;  //sala
ne1=ne;  //escola
noeq1=noeq;  //nome equi
ides1=ides;  //id_escola
said1=said;  //id_sala

//alert(ides1);

 //alert(n0);

  event.preventDefault(); // prevent form submit

   swal({

  title: "Deseja eliminar? ",
 text: "Equipamento: "+noeq1+ "\n" +" ("+"Sala: "+n1 + " | " + "Instituição: "+ne1+")",
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
    
        //alert(n1);
        window.setTimeout(function() {
    window.location.href = '<?php echo SVRURL ?>eliminaoutequip/'+n0+'/'+ides1+'/'+said1;
}, 10);


          
  } else {
    swal("Cancelado.");
   // swal("Cancelled", "Your imaginary file is safe :)", "error");
   // window.setTimeout(function() {
    //window.location.href = '<?php echo SVRURL ?>ver_equipamentos_sala.php';
//}, 10);
  

  }

});

}




</script>




<script>
function a2(n,no,ne,noeq,ides,said) {

var n0,n1,ne1,noeq1,ides,said;
n0=n; //id_equi
n1=no;  //sala
ne1=ne;  //escola
noeq1=noeq;  //nome equi
ides1=ides;  //id_escola
said1=said;  //id_sala


//alert(ides1);

 //alert(n0);

  event.preventDefault(); // prevent form submit

   swal({

  title: "Deseja mudar o equipamento de sala?",
 text: "Equipamento: "+noeq1+ "\n" +" ("+"Sala: "+n1 + " | " + "Instituição: "+ne1+")",
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
    
        //alert(n1);
        window.setTimeout(function() {
    window.location.href = '<?php echo SVRURL ?>mudarsalaequi/'+n0+'/'+ides1+'/'+said1;
}, 10);


          
  } else {
    swal("Cancelado.");
   // swal("Cancelled", "Your imaginary file is safe :)", "error");
   // window.setTimeout(function() {
    //window.location.href = '<?php echo SVRURL ?>ver_equipamentos_sala.php';
//}, 10);
  

  }

});

}







function a2a(isa,sa,es) {

var isa1,sa1,ne1;

isa1=isa;
sa1=sa;
ne1=es;



//alert(es1);

event.preventDefault(); // prevent form submit

 swal({

title: "Deseja eliminar todos os equipamentos informáticos (todas as avarias serão eliminadas)?",
text: "Sala: "+sa1 + " | " + "Instituição: "+ne1+" ",
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
  window.location.href = '<?php echo SVRURL ?>eliminaequisala/'+isa1;
}, 10);


        
} else {
  swal("Cancelado.");



}

});

}









function a2b(isa,sa,es) {

var isa1,sa1,ne1;

isa1=isa;
sa1=sa;
ne1=es;



//alert(es1);

event.preventDefault(); // prevent form submit

 swal({

title: "Deseja eliminar todos os outros equipamentos?",
text: "Sala: "+sa1 + " | " + "Instituição: "+ne1+" ",
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
  window.location.href = '<?php echo SVRURL ?>eliminaoutequisala/'+isa1;
}, 10);


        
} else {
  swal("Cancelado.");



}

});

}





</script>



<?php 
  if(isset($_POST['records-limit'])){
      $_SESSION['records-limit'] = $_POST['records-limit'];
  }
  
  $limit = isset($_SESSION['records-limit']) ? $_SESSION['records-limit'] : 10;
  $page = (isset($_GET['page']) && is_numeric($_GET['page']) ) ? $_GET['page'] : 1;
  $paginationStart = ($page - 1) * $limit;
  


  $stmt_eq = $db->prepare("SELECT e.*, s.nome FROM equipamento e, salas s WHERE e.id_sala = s.id AND s.id = ? AND s.id_escola = ? ORDER BY e.tipo DESC LIMIT ?, ?");
  $stmt_eq->bind_param("iiii", $said, $idescola, $paginationStart, $limit);
  $stmt_eq->execute();
  $result = $stmt_eq->get_result();
  $stmt_eq->close();

  // Get total records
  $stmt1 = $db->prepare("SELECT COUNT(*) FROM equipamento e, salas s WHERE e.id_sala = s.id AND s.id = ? AND s.id_escola = ?");
  $stmt1->bind_param("ii", $said, $idescola);
  $stmt1->execute();
  $result1 = $stmt1->get_result();
  $rows = $result1->fetch_row();
  $stmt1->close();
  
  $totallinhas = $rows[0];


  // Calculate total pages
  $totoalPages = ceil($totallinhas / $limit);

  // Prev + Next
  $prev = $page - 1;
  $next = $page + 1;
?> 

   
 
<style>
.gei-btn-resumo { display:inline-flex; align-items:center; gap:6px; padding:5px 13px; border-radius:7px; font-size:.78rem; font-weight:600; color:#4b6cb7 !important; background:#eef2fb; border:1.5px solid #c7d4f0; cursor:pointer; transition:all .15s; white-space:nowrap; }
/* ── Botão histórico / timeline ── */
.gei-btn-historico { display:inline-flex; align-items:center; gap:5px; padding:4px 10px; border-radius:6px; font-size:.75rem; font-weight:600; color:#6f42c1 !important; background:#ede8fc; border:1.5px solid #c9b8f5; cursor:pointer; transition:all .15s; white-space:nowrap; text-decoration:none !important; margin:2px; }
.gei-btn-historico:hover { background:#ddd0f9; border-color:#6f42c1; transform:translateY(-1px); }
/* ── Modal timeline ── */
.tl-modal-overlay { display:none; position:fixed; inset:0; background:rgba(15,17,23,.55); z-index:9999; align-items:center; justify-content:center; padding:16px; }
.tl-modal-overlay.active { display:flex; }
.tl-modal { background:var(--bg-card,#fff); border-radius:14px; width:100%; max-width:700px; max-height:88vh; display:flex; flex-direction:column; box-shadow:0 24px 64px rgba(0,0,0,.22); }
.tl-modal-header { display:flex; align-items:center; gap:12px; padding:16px 20px; border-bottom:1px solid var(--border-color,#e4e9f0); flex-shrink:0; }
.tl-modal-icon { width:34px; height:34px; border-radius:9px; background:linear-gradient(135deg,#6f42c1,#9b6cf7); display:flex; align-items:center; justify-content:center; color:#fff; font-size:.85rem; flex-shrink:0; }
.tl-modal-title { flex:1; font-size:.98rem; font-weight:700; color:var(--text-heading,#111); margin:0; }
.tl-modal-equip { font-size:.78rem; color:var(--text-secondary,#666); margin:0; }
.tl-modal-close { background:none; border:none; cursor:pointer; color:var(--text-secondary,#666); font-size:1.1rem; padding:4px 8px; border-radius:6px; transition:background .15s; line-height:1; }
.tl-modal-close:hover { background:var(--bg-table-alt,#f8f9fc); }
.tl-modal-body { overflow-y:auto; padding:20px; flex:1; }
/* dark mode modal */
[data-theme="dark"] .tl-modal { background:var(--bg-card); border-color:var(--border-color); }
[data-theme="dark"] .tl-modal-header { border-color:var(--border-color); }
[data-theme="dark"] .tl-modal-title { color:var(--text-heading); }
[data-theme="dark"] .gei-btn-historico { background:rgba(111,66,193,.2); border-color:rgba(111,66,193,.4); color:#b48ef7 !important; }
[data-theme="dark"] .gei-btn-historico:hover { background:rgba(111,66,193,.32); }
.gei-btn-resumo:hover { background:#dce6f8; border-color:#4b6cb7; }
.gei-table-wrap { background:#fff; border-radius:10px; box-shadow:0 2px 12px rgba(75,108,183,.10); border:1px solid #e3e8f4; overflow:hidden; margin-bottom:16px; }
.gei-table-toolbar { display:flex; align-items:center; justify-content:space-between; flex-wrap:wrap; gap:10px; padding:12px 16px; background:#f4f6fb; border-bottom:1px solid #e3e8f4; }
.gei-table-toolbar-left { display:flex; align-items:center; gap:10px; flex-wrap:wrap; }
.gei-table-toolbar-right { display:flex; align-items:center; gap:8px; }
.gei-table { width:100%; border-collapse:collapse; font-size:.84rem; }
.gei-table thead th { padding:10px 14px; background:#182848; color:#fff; font-size:.75rem; font-weight:700; text-transform:uppercase; letter-spacing:.5px; border:none; white-space:nowrap; }
.gei-table tbody tr { border-bottom:1px solid #eef1f8; transition:background .15s; }
.gei-table tbody tr:last-child { border-bottom:none; }
.gei-table tbody tr:hover { background:#f0f4fb; }
.gei-table tbody tr:nth-child(even) { background:#f7f9fe; }
.gei-table td { padding:10px 14px; vertical-align:top; color:#1e2a45; font-size:.83rem; }
.gei-action-btn { display:inline-flex; align-items:center; gap:5px; padding:4px 10px; border-radius:6px; font-size:.75rem; font-weight:600; text-decoration:none !important; border:none; cursor:pointer; transition:opacity .15s,transform .12s; white-space:nowrap; margin:2px; }
.gei-action-btn:hover { opacity:.85; transform:translateY(-1px); }
.gei-btn-edit   { background:#eef2fb; color:#00509e !important; border:1.5px solid #c7d4f0; }
.gei-btn-delete { background:#fde8e6; color:#c0392b !important; border:1.5px solid #f5c0bb; }
.gei-btn-move   { background:#fff8e8; color:#b07d00 !important; border:1.5px solid #f0d98a; }
.gei-btn-danger { background:#c0392b; color:#fff !important; border:1.5px solid #a93226; border-radius:6px; padding:5px 12px; font-size:.78rem; font-weight:600; cursor:pointer; display:inline-flex; align-items:center; gap:5px; }
.gei-badge-op { background:#e6f9f2; color:#1a7a52; border:1.5px solid #a8e6cf; border-radius:5px; padding:2px 8px; font-size:.75rem; font-weight:700; display:inline-block; }
.gei-badge-av { background:#fde8e6; color:#c0392b; border:1.5px solid #f5c0bb; border-radius:5px; padding:2px 8px; font-size:.75rem; font-weight:700; display:inline-block; }
.gei-section-label { font-size:.78rem; font-weight:700; text-transform:uppercase; color:#7b88a0; letter-spacing:.4px; margin:18px 0 8px; }
.gei-pagination { display:flex; align-items:center; justify-content:center; gap:4px; flex-wrap:wrap; padding:12px 0; }
.gei-page-btn { display:inline-flex; align-items:center; justify-content:center; min-width:32px; height:32px; padding:0 10px; border-radius:6px; font-size:.8rem; font-weight:600; text-decoration:none !important; border:1.5px solid #e3e8f4; color:#4b6cb7 !important; background:#fff; transition:all .15s; }
.gei-page-btn:hover { background:#eef2fb; border-color:#4b6cb7; }
.gei-page-btn.active { background:#182848; color:#fff !important; border-color:#182848; }
.gei-page-btn.disabled { opacity:.4; pointer-events:none; }
.gei-page-total { font-size:.78rem; color:#7b88a0; font-weight:600; padding:0 10px; }
@media (max-width: 768px) {
    .gei-table thead { display:none; }
    .gei-table tbody tr { display:block; border:1px solid #e3e8f4; border-radius:8px; margin-bottom:12px; padding:10px 12px; background:#fff; box-shadow:0 1px 6px rgba(75,108,183,.08); }
    .gei-table tbody tr:nth-child(even) { background:#fff; }
    .gei-table td { display:flex; align-items:flex-start; gap:8px; padding:5px 2px; border:none; }
    .gei-table td::before { content: attr(data-label); min-width:110px; font-size:.72rem; font-weight:700; text-transform:uppercase; letter-spacing:.4px; color:#7b88a0; padding-top:2px; flex-shrink:0; }
    .gei-table td[data-label="Ações"] { flex-wrap:wrap; gap:6px; padding-top:8px; border-top:1px dashed #e3e8f4; margin-top:4px; }
    .gei-table td[data-label="Ações"]::before { display:none; }
}
</style>
<link rel="stylesheet" href="<?php echo SVRURL ?>css/timeline.css">




<!--
    <?php
     if ($_SESSION['tipo']==1 )
     {
   ?>

<img src="<?php echo SVRURL ?>images/informacao.svg" alt="Informação">
        Ao eliminar o equipamento serão eliminadas todas as avarias. 
        <?php
     }
     ?> 
-->




<div class="gei-table-wrap">
    <div class="gei-table-toolbar">
        <div class="gei-table-toolbar-left">
            <form action="<?php echo SVRURL ?>ver_equipamentos_sala.php?x=<?php echo base64_encode(2) ?>&&si=<?php echo base64_encode($said);?>&&ies=<?php echo base64_encode($idescola) ?>" method="post">
                <label style="font-size:.8rem;font-weight:600;color:#7b88a0;display:flex;align-items:center;gap:6px;">
                Linhas por página:
                <select name="records-limit" onchange="this.form.submit()"
                    style="padding:5px 10px;border-radius:7px;border:1.5px solid #c7d4f0;font-size:.82rem;font-weight:600;color:#1e2a45;background:#fff;cursor:pointer;outline:none;">
                    <?php foreach([5,10,20,30,50,100] as $opt): ?>
                    <option value="<?= $opt ?>" <?= ($limit==$opt) ? 'selected' : '' ?>><?= $opt ?></option>
                    <?php endforeach; ?>
                </select>
                </label>
            </form>
            <!-- Botão Resumo na mesma linha que num linhas -->
            <form action="<?php echo SVRURL ?>qta_equipamentos_sala.php?z=<?php echo base64_encode('eq') ?>&&x=<?php echo base64_encode(1) ?>&&ies=<?php echo base64_encode($idescola) ?>&&si=<?php echo base64_encode($said) ?>" method="post" style="display:inline;">
                <button type="submit" title="Resumo do equipamento" class="gei-btn-resumo">
                    <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><line x1="8" y1="6" x2="21" y2="6"/><line x1="8" y1="12" x2="21" y2="12"/><line x1="8" y1="18" x2="21" y2="18"/><line x1="3" y1="6" x2="3.01" y2="6"/><line x1="3" y1="12" x2="3.01" y2="12"/><line x1="3" y1="18" x2="3.01" y2="18"/></svg>
                    Resumo do equipamento
                </button>
            </form>
            <?php if (($totallinhas>0) && ($_SESSION['tipo']==1)): ?>
            <a href="<?php echo SVRURL ?>mover_sala_massa.php?si=<?php echo base64_encode($said); ?>&&ies=<?php echo base64_encode($idescola); ?>"
               class="gei-btn-resumo" title="Mover equipamentos em massa para outra sala"
               style="color:#b07d00 !important;background:#fff8e8;border-color:#f0d98a;text-decoration:none;">
                <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><polyline points="5 9 2 12 5 15"/><polyline points="9 5 12 2 15 5"/><line x1="2" y1="12" x2="22" y2="12"/><line x1="12" y1="2" x2="12" y2="22"/></svg>
                Mover em massa
            </a>
            <?php endif; ?>
        </div>
        <?php if (($totallinhas>0) && ($_SESSION['tipo']==1)): ?>
        <div class="gei-table-toolbar-right">
            <a onclick="a2a(<?php echo $said;?>,'<?php echo $ns;?>','<?php echo $ne;?>')"
               href="<?php echo SVRURL ?>elimina_equi_sala.php?id=<?php echo base64_encode($said);?>" target="_blank"
               class="gei-btn-danger" title="Eliminar todos os equipamentos informáticos da sala">
                <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="#fff" stroke-width="2.2" stroke-linecap="round"><polyline points="3 6 5 6 21 6"/><path d="M19 6l-1 14a2 2 0 0 1-2 2H8a2 2 0 0 1-2-2L5 6"/></svg>
                Eliminar todos
            </a>
        </div>
        <?php endif; ?>
    </div>
    <table class="gei-table">
        <thead>
            <tr>
                <th>Tipo / Nome</th>
                <th>Dados técnicos</th>
                <th>Dados rede</th>
                <?php if ($_SESSION['tipo']==1): ?>
                <th style="text-align:center;">Ações</th>
                <?php endif; ?>
            </tr>
        </thead>
        <tbody>


                <?php 
                //$c=0;
                while($row=mysqli_fetch_array($result)) { 
                    $n=$row['id'];
                    $noeq=$row['nomeequi'];

                    //$c=$c+1;
                    //$totallinhas = $c;
                    $stmt_av = $db->prepare("SELECT COUNT(*) FROM avarias_reparacoes ar WHERE ar.id_equi = ? AND ar.datareparacao IS NULL");
                    $stmt_av->bind_param("i", $n);
                    $stmt_av->execute();
                    $result1 = $stmt_av->get_result();
                    $rows = $result1->fetch_row();
                    $stmt_av->close();
             

                    ?>
                <tr>
                    <th width="30%"  scope="row"><?php echo htmlspecialchars($row['tipo'], ENT_QUOTES, 'UTF-8'); echo('<br>/<br>'); echo htmlspecialchars($row['nomeequi'], ENT_QUOTES, 'UTF-8');  ?>
                    <br>  <br><br>
                    Escola Digital: 
                    <?php
                    echo ($row['escola_digital']);
                  
                    ?>
                    <br>
                     
                    <?php
                    if ($row['escola_digital']=="Sim")
                {
                    ?>

                    Nº Dgest: 
                    <?php
                    echo ($row['num_inv_dgest']);
                    ?>
                    <br>
                    Fornecedor / Email: <br>
                    <?php
                    echo ($row['fornecedor']); echo('<br>'); echo ($row['email_fornecedor']);
                    ?>
                    
                <?php
                }
                ?>



                    <br> <br> <br>
                    Estado:
                    <?php
                    if ($rows[0]==0)
                    {
                      echo '<em style="color:green;font-size:14px;">
      Operacional </em>';
                    ?>
                   <!--
                   <h5 style="color:green;">Operacional</h5>
                    -->
                    <?php
                    }
                    else
                    {
                      echo '<em style="color:red;font-size:14px;">
                     Avariado </em>';
                    ?>
                 
                     <!--
                      <h5 style="color:red;">Avariado</h5>
                      -->
                   <?php
                     
                      
                    if ($_SESSION['tipo']==1 || $_SESSION['tipo']==3) 
                    {
                      ?>
                     <a   title="Ver avaria" href="<?php echo SVRURL ?>reparacoes_efetuar_equip.php?ieq=<?php echo base64_encode($row['id']);?>&&sai=<?php echo base64_encode($said);?>&&ies=<?php echo base64_encode($idescola) ?>">
                     <img  src="<?php echo SVRURL ?>images/reparacao.svg">
                    </a> 
                    
                    </h5>
                    <?php
                    }


                     }         //else echo ('Avariado');       //echo ('Operacional');
                    ?>
                 
                    


                    <br> 
                    <br>

                      <?php



                      if ( ($row['data_compra']<>null) && ( strcmp($row['data_compra'], "0000-00-00") !== 0) )
                      {
                      echo ('Data da compra: ');
                      echo ($row['data_compra']);
                      }
                      else
                      {
                        echo ('Data da compra: ---');
             
                      }
                      ?>
                     </th>


                    <td data-label="Dados técnicos">
                        <div style="font-size:.78rem;line-height:1.8;color:#1e2a45;">
                        Nº série: <?php echo htmlspecialchars($row['numserie'], ENT_QUOTES, 'UTF-8'); ?><br>
                        Marca / Modelo: <?php echo htmlspecialchars($row['marca_modelo'], ENT_QUOTES, 'UTF-8'); ?><br><br>
                        CPU: <?php echo htmlspecialchars($row['processador'], ENT_QUOTES, 'UTF-8'); ?><br>
                        RAM (GB): <?php echo htmlspecialchars($row['memoria'], ENT_QUOTES, 'UTF-8'); ?><br>
                        Disco (GB): <?php echo htmlspecialchars($row['disco'], ENT_QUOTES, 'UTF-8'); ?><br><br>
                        Gráfica: <?php echo htmlspecialchars($row['placagrafica'], ENT_QUOTES, 'UTF-8'); ?><br>
                        Som: <?php echo htmlspecialchars($row['placasom'], ENT_QUOTES, 'UTF-8'); ?><br>
                        Rede: <?php echo htmlspecialchars($row['placarede'], ENT_QUOTES, 'UTF-8'); ?><br><br>
                        Monitor: <?php echo htmlspecialchars($row['monitor'], ENT_QUOTES, 'UTF-8'); ?><br>
                        Teclado: <?php echo htmlspecialchars($row['teclado'], ENT_QUOTES, 'UTF-8'); ?> — <?php echo htmlspecialchars($row['tecladointerface'], ENT_QUOTES, 'UTF-8'); ?><br>
                        Rato: <?php echo htmlspecialchars($row['rato'], ENT_QUOTES, 'UTF-8'); ?> — <?php echo htmlspecialchars($row['ratointerface'], ENT_QUOTES, 'UTF-8'); ?>
                        </div>
                    </td>


                    <td data-label="Dados rede">
                        <div style="font-size:.78rem;line-height:1.8;color:#1e2a45;">
                        Domínio: <?php echo htmlspecialchars($row['dominio'], ENT_QUOTES, 'UTF-8'); ?><br>
                        IP: <?php echo htmlspecialchars($row['ip'], ENT_QUOTES, 'UTF-8'); ?><br>
                        Máscara: <?php echo htmlspecialchars($row['mascara_rede'], ENT_QUOTES, 'UTF-8'); ?><br>
                        Gateway: <?php echo htmlspecialchars($row['gateway'], ENT_QUOTES, 'UTF-8'); ?><br>
                        DNS principal: <?php echo htmlspecialchars($row['dns_principal'], ENT_QUOTES, 'UTF-8'); ?><br>
                        DNS alternativo: <?php echo htmlspecialchars($row['dns_alternativo'], ENT_QUOTES, 'UTF-8'); ?><br><br>
                        <em>Observações:</em><br>
                        <?php echo htmlspecialchars($row['observacoes'], ENT_QUOTES, 'UTF-8'); ?>
                        </div>
                    </td>



                    <?php if ($_SESSION['tipo']==1): ?>
                    <td data-label="Ações" style="text-align:center;white-space:nowrap;">
                        <a class="gei-action-btn gei-btn-edit" title="Atualizar"
                           href="<?php echo SVRURL ?>atualiequip?ide=<?php echo base64_encode($n); ?>&&sai=<?php echo base64_encode($said); ?>&&ies=<?php echo base64_encode($idescola); ?>">
                            <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
                            Editar
                        </a>
                        <a class="gei-action-btn gei-btn-delete" title="Eliminar"
                           onclick="a1('<?php echo $n;?>','<?php echo $ns;?>','<?php echo $ne;?>','<?php echo $noeq;?>','<?php echo $idescola;?>','<?php echo $said;?>');"
                           href="<?php echo SVRURL ?>eliminaequip">
                            <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round"><polyline points="3 6 5 6 21 6"/><path d="M19 6l-1 14a2 2 0 0 1-2 2H8a2 2 0 0 1-2-2L5 6"/></svg>
                            Eliminar
                        </a>
                        <a class="gei-action-btn gei-btn-move" title="Mudar de sala"
                           onclick="a2('<?php echo $n;?>','<?php echo $ns;?>','<?php echo $ne;?>','<?php echo $noeq;?>','<?php echo $idescola;?>','<?php echo $said;?>');"
                           href="<?php echo SVRURL ?>mudasalaequi">
                            <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round"><polyline points="5 9 2 12 5 15"/><polyline points="9 5 12 2 15 5"/><line x1="2" y1="12" x2="22" y2="12"/><line x1="12" y1="2" x2="12" y2="22"/></svg>
                            Mudar sala
                        </a>
                        <a class="gei-btn-historico" title="Histórico de avarias"
                           onclick="abrirTimeline(<?php echo $n; ?>,'<?php echo htmlspecialchars($noeq,ENT_QUOTES); ?>')">
                            <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                            Histórico
                        </a>
                    </td>
                    <?php endif; ?>
            
                    
                
                </tr>
                <?php } 
                 //$totoalPages = ceil($totallinhas / $limit);
                ?>
      </tbody>
        </table>     
                

        




        
<div class="gei-pagination">
    <a class="gei-page-btn <?php if($page<=1) echo 'disabled'; ?>"
       href="<?php echo $page<=1?'#':'?x='.base64_encode(1).'&&si='.base64_encode($said).'&&ies='.base64_encode($idescola).'&&page='.$prev; ?>">
        <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round"><polyline points="15 18 9 12 15 6"/></svg>
    </a>
    <?php for($i=1; $i<=$totoalPages; $i++): ?>
    <a class="gei-page-btn <?php if($page==$i) echo 'active'; ?>"
       href="<?php echo SVRURL ?>ver_equipamentos_sala.php?x=<?php echo base64_encode(1);?>&&si=<?php echo base64_encode($said);?>&&ies=<?php echo base64_encode($idescola);?>&&page=<?= $i ?>"><?= $i ?></a>
    <?php endfor; ?>
    <a class="gei-page-btn <?php if($page>=$totoalPages) echo 'disabled'; ?>"
       href="<?php echo $page>=$totoalPages?'#':'?x='.base64_encode(1).'&&si='.base64_encode($said).'&&ies='.base64_encode($idescola).'&&page='.$next; ?>">
        <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round"><polyline points="9 18 15 12 9 6"/></svg>
    </a>
    <span class="gei-page-total">Total: <?php echo $totallinhas; ?></span>
</div>
       

        <br>     




        <?php
        $stmt2 = $db->prepare("SELECT oe.* FROM outro_equipamento oe, salas s WHERE oe.id_sala = s.id AND s.id = ? AND s.id_escola = ? ORDER BY oe.nomeoutro");
        $stmt2->bind_param("ii", $said, $idescola);
        $stmt2->execute();
  $result2 = $stmt2->get_result();
  $stmt2->close();

  $count = $result2->num_rows;
?>




<br>


<?php

if ($count>0)
{


  ?>



<div class="gei-section-label">Outro equipamento</div>
<div class="gei-table-wrap">
    <div class="gei-table-toolbar">
        <div class="gei-table-toolbar-left"></div>
        <?php if (($count>0) && ($_SESSION['tipo']==1)): ?>
        <div class="gei-table-toolbar-right">
            <a onclick="a2b(<?php echo $said;?>,'<?php echo $ns;?>','<?php echo $ne;?>')"
               href="<?php echo SVRURL ?>elimina_out_equi_sala.php?id=<?php echo base64_encode($said);?>" target="_blank"
               class="gei-btn-danger" title="Eliminar todos os outros equipamentos da sala">
                <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="#fff" stroke-width="2.2" stroke-linecap="round"><polyline points="3 6 5 6 21 6"/><path d="M19 6l-1 14a2 2 0 0 1-2 2H8a2 2 0 0 1-2-2L5 6"/></svg>
                Eliminar todos
            </a>
        </div>
        <?php endif; ?>
    </div>
    <table class="gei-table" id="js-sort-table">
        <thead>
            <tr>
                <th>Nome</th>
                <th>Quantidade</th>
                <th>Observações</th>
                <?php if ($_SESSION['tipo']==1): ?><th style="text-align:center;">Ações</th><?php endif; ?>
            </tr>
        </thead>
        <tbody>


                <?php 
                //$c=0;

                  if ($count>0)
                  {
              



                while($row=mysqli_fetch_array($result2)) { 

               

                    $n=$row['id'];
                    $noeq=$row['nomeoutro'];
               
             

                    ?>
                <tr>
                    <td data-label="Nome"><?php echo htmlspecialchars($row['nomeoutro'], ENT_QUOTES, 'UTF-8'); ?></td>
                    <td data-label="Quantidade"><span class="gei-badge"><?php echo htmlspecialchars($row['qta'], ENT_QUOTES, 'UTF-8'); ?></span></td>
                    <td data-label="Observações"><?php echo htmlspecialchars($row['observacoes'], ENT_QUOTES, 'UTF-8'); ?></td>
                    <?php if ($_SESSION['tipo']==1): ?>
                    <td data-label="Ações" style="text-align:center;white-space:nowrap;">
                        <a class="gei-action-btn gei-btn-edit" title="Atualizar"
                           href="<?php echo SVRURL ?>atualioutequip?ide=<?php echo base64_encode($n); ?>&&sai=<?php echo base64_encode($said); ?>&&ies=<?php echo base64_encode($idescola); ?>">
                            <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
                            Editar
                        </a>
                        <a class="gei-action-btn gei-btn-delete" title="Eliminar"
                           onclick="a1a('<?php echo $n;?>','<?php echo $ns;?>','<?php echo $ne;?>','<?php echo $noeq;?>','<?php echo $idescola;?>','<?php echo $said;?>');"
                           href="<?php echo SVRURL ?>eliminaoutequip">
                            <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round"><polyline points="3 6 5 6 21 6"/><path d="M19 6l-1 14a2 2 0 0 1-2 2H8a2 2 0 0 1-2-2L5 6"/></svg>
                            Eliminar
                        </a>
                    </td>
                    <?php endif; ?>
            
                    
                
                </tr>


                
                <?php } 
                }
              
                
                  ?>
                
           


                  <?php
               
              }
             
              
                ?>
      </tbody>
        </table>     

    

</div><!-- fim gei-table-wrap outro equipamento -->

 </div>


 
        <a href="<?php echo SVRURL ?>equip">
<img src="<?php echo SVRURL ?>images/voltar.svg" alt="Voltar">
</a>


<br>


        <?php include ("jquery_bootstrap.php");?>


        <?php
      // Clear the session
		unset($_SESSION['escola']);
?>

<br>


                   
               
               </div>
            </div>
         </div>
      </div>
      <!-- end about -->
    


      <?php include ("footer.php");?>

<!-- ═══ MODAL TIMELINE DE AVARIAS ═══════════════════════════════════ -->
<div class="tl-modal-overlay" id="tl-modal-overlay" onclick="fecharTimeline(event)">
    <div class="tl-modal" role="dialog" aria-modal="true" aria-labelledby="tl-modal-titulo">
        <div class="tl-modal-header">
            <div class="tl-modal-icon"><i class="fas fa-history"></i></div>
            <div style="flex:1;min-width:0;">
                <p class="tl-modal-title" id="tl-modal-titulo">Histórico de Avarias</p>
                <p class="tl-modal-equip" id="tl-modal-equip-nome"></p>
            </div>
            <a class="tl-btn-pdf" id="tl-pdf-btn" href="#" target="_blank" title="Exportar PDF">
                <i class="fas fa-file-pdf"></i> PDF
            </a>
            <button class="tl-modal-close" onclick="fecharTimeline()" title="Fechar">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <div class="tl-modal-body" id="timeline-wrap">
            <div class="tl-loading"><i class="fas fa-spinner fa-spin"></i> A carregar...</div>
        </div>
    </div>
</div>
<!-- ═════════════════════════════════════════════════════════════════ -->

<script src="<?php echo SVRURL ?>js/equipamento-timeline.js"></script>
<script>
function abrirTimeline(idEquip, nomeEquip) {
    document.getElementById('tl-modal-equip-nome').textContent = nomeEquip;
    document.getElementById('tl-modal-overlay').classList.add('active');
    document.body.style.overflow = 'hidden';
    document.getElementById('tl-pdf-btn').href = 'equipamento_timeline_pdf.php?id_equip=' + idEquip;
    GEITimeline.carregar(idEquip);
}
function fecharTimeline(e) {
    if (e && e.target !== document.getElementById('tl-modal-overlay')) return;
    document.getElementById('tl-modal-overlay').classList.remove('active');
    document.body.style.overflow = '';
    document.getElementById('timeline-wrap').innerHTML =
        '<div class="tl-loading"><i class="fas fa-spinner fa-spin"></i> A carregar...</div>';
}
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') fecharTimeline();
});
</script>





<style>
#tl-pdf-btn {
    display: inline-flex !important;
    align-items: center !important;
    gap: 5px !important;
    padding: 5px 12px !important;
    background: #c0392b !important;
    color: #fff !important;
    border-radius: 6px !important;
    font-size: 12px !important;
    font-weight: 600 !important;
    text-decoration: none !important;
    margin-right: 8px !important;
    width: auto !important;
    height: auto !important;
    visibility: visible !important;
    opacity: 1 !important;
    position: relative !important;
    overflow: visible !important;
}
</style>


   </body>
</html>
