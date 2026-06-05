<?php
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

include("css_inserir.php");

include("sessao_timeout.php");

//include("verifica_sessao.php");

//print_r(scandir(session_save_path()));


 
  ?>


<?php
               
                 
               // Gerar token CSRF
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}
$csrf_token = $_SESSION['csrf_token'];

$id= (int)base64_decode($_GET['ide']);
               $sa= (int)base64_decode($_GET['sai']);
               $idescola= (int)base64_decode($_GET['ies']);

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






     

                $sql3 ="select e.*,s.*, es.nome_escola
                from equipamento e, salas s, escolas es
                where e.id_sala=s.id and s.id_escola=es.id
                and e.id=$id and s.id=$sa and es.id=$idescola";
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
               <!-- Breadcrumb melhorado -->
               <nav style="margin-bottom:10px;">
                  <ol style="display:flex;align-items:center;flex-wrap:wrap;gap:4px;list-style:none;padding:0;margin:0;font-size:.8rem;font-weight:600;color:#7b88a0;">
                     <li style="display:flex;align-items:center;gap:4px;">
                        <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="#4b6cb7" stroke-width="2.3" stroke-linecap="round" stroke-linejoin="round"><rect x="2" y="7" width="20" height="14" rx="2"/><path d="M16 7V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v2"/></svg>
                        <span style="color:#4b6cb7;">Equipamentos</span>
                     </li>
                     <li style="color:#c5cde0;font-size:.9rem;">&#8250;</li>
                     <li style="color:#1e2a45;">Atualizar</li>
                  </ol>
               </nav>

               </div>
            </div>
            
            <div class="container">
               <div class="row">
                  <div class="col-md-10 offset-md-1">
              
 <!-- Welcome Section -->
 <div class="welcome-section">
               
               <div>
             
                   <?php include("msg_bemvindo.php"); ?>
               </div>
       </div>

               <!-- Cabeçalho com info do equipamento -->
               <div style="display:flex; align-items:center; flex-wrap:wrap; gap:16px; margin:14px 0 10px; padding:12px 16px; background:#f4f6fb; border:1px solid #e3e8f4; border-radius:10px;">
                  <!-- Ícone -->
                  <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#4b6cb7" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" style="flex-shrink:0;"><rect x="2" y="7" width="20" height="14" rx="2"/><path d="M16 7V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v2"/></svg>
                  <!-- Equipamento -->
                  <div style="display:flex; flex-direction:column;">
                     <span style="font-size:.7rem; font-weight:600; text-transform:uppercase; letter-spacing:.4px; color:#7b88a0;">Equipamento</span>
                     <span style="font-size:.95rem; font-weight:700; color:#182848;"><?php echo htmlspecialchars($row3['nomeequi'], ENT_QUOTES, 'UTF-8'); ?></span>
                  </div>
                  <span style="color:#c5cde0;">|</span>
                  <!-- Sala -->
                  <div style="display:flex; flex-direction:column;">
                     <span style="font-size:.7rem; font-weight:600; text-transform:uppercase; letter-spacing:.4px; color:#7b88a0;">Sala</span>
                     <span style="font-size:.95rem; font-weight:700; color:#182848;"><?php echo htmlspecialchars($row3['nome'], ENT_QUOTES, 'UTF-8'); ?></span>
                  </div>
                  <span style="color:#c5cde0;">|</span>
                  <!-- Escola -->
                  <div style="display:flex; flex-direction:column;">
                     <span style="font-size:.7rem; font-weight:600; text-transform:uppercase; letter-spacing:.4px; color:#7b88a0;">Instituição</span>
                     <span style="font-size:.95rem; font-weight:700; color:#182848;"><?php echo htmlspecialchars($row3['nome_escola'], ENT_QUOTES, 'UTF-8'); ?></span>
                  </div>
               </div>

<?php
$isEscolaDigital = ($row3['escola_digital'] == "Sim");
?>

<style>
    .nav-tabs .nav-link {
        border-radius: 8px 8px 0 0;
        padding: 10px 18px;
        font-weight: 600;
        color: #6c757d;
        transition: all 0.3s;
    }
    .nav-tabs .nav-link.active {
        background-color: #3498db !important;
        color: #fff !important;
        border: none !important;
        border-bottom: 3px solid #1a6aad !important;
    }
    .nav-tabs .nav-link.active i { color: #fff !important; }
    .nav-tabs .nav-link:not(.active) i { color: #8a99b0; }
    .nav-tabs .nav-link:hover:not(.active) {
        background-color: #e4eef8;
        color: #3498db;
    }
    .tab-content {
        padding: 25px;
        border: 1px solid #dee2e6;
        border-top: none;
        border-radius: 0 0 8px 8px;
        background-color: white;
    }
    .tab-form-row {
        display: flex;
        flex-wrap: wrap;
        margin: 0 -10px;
    }
    .tab-form-col {
        flex: 0 0 50%;
        max-width: 50%;
        padding: 0 10px;
        margin-bottom: 14px;
    }
    .tab-form-col-full {
        flex: 0 0 100%;
        max-width: 100%;
        padding: 0 10px;
        margin-bottom: 14px;
    }
    @media (max-width: 768px) {
        .tab-form-col { flex: 0 0 100%; max-width: 100%; }
    }
    .tab-form-col label, .tab-form-col-full label {
        font-weight: 600;
        color: #2c3e50;
        margin-bottom: 5px;
        display: block;
        font-size: .88rem;
    }
</style>

<div class="equipment-tabs" style="margin-top:10px;">
    <ul class="nav nav-tabs" id="equipTabs" role="tablist">
        <li class="nav-item">
            <a class="nav-link active" data-toggle="tab" href="#tab-geral" role="tab">
                <i class="fas fa-laptop"></i> Dados Gerais
            </a>
        </li>
        <?php if ($isEscolaDigital): ?>
        <li class="nav-item">
            <a class="nav-link" data-toggle="tab" href="#tab-escdig" role="tab">
                <i class="fas fa-school"></i> Escola Digital
            </a>
        </li>
        <?php endif; ?>
    </ul>

    <div class="tab-content" id="equipTabsContent">

        <!-- TAB: Dados Gerais -->
        <div class="tab-pane fade show active" id="tab-geral" role="tabpanel">
            <form class="needs-validation" novalidate action="<?php echo SVRURL ?>atualiza_equipamento_OK.php?ide=<?php echo base64_encode($id)?>&&sai=<?php echo base64_encode($sa)?>&&ies=<?php echo base64_encode($idescola)?>" method="post">
                <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($csrf_token); ?>">

                <div class="tab-form-row">
                    <div class="tab-form-col">
                        <label><i class="fas fa-tag"></i> Tipo de equipamento</label>
                        <?php
                        $sql = "SELECT DISTINCT(nome) as no FROM tipos_equipamento order by nome";
                        $result = mysqli_query($db,$sql);
                        ?>
                        <select name="tipoeq" id="tipoeq" required class="form-control required-field">
                            <option value=""></option>
                            <?php while($row=mysqli_fetch_array($result)): ?>
                                <option value="<?php echo htmlspecialchars($row['no'], ENT_QUOTES, 'UTF-8'); ?>"
                                    <?php echo ($row['no']==$row3['tipo']) ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($row['no'], ENT_QUOTES, 'UTF-8'); ?>
                                </option>
                            <?php endwhile; ?>
                        </select>
                    </div>
                    <div class="tab-form-col">
                        <label><i class="fas fa-door-open"></i> Sala</label>
                        <?php
                        $sql2 = "SELECT DISTINCT(s.nome) as no, s.id as said
                                 FROM salas s, escolas e
                                 WHERE s.id_escola=e.id AND e.id=$idescola
                                 ORDER BY s.nome";
                        $result2 = mysqli_query($db,$sql2);
                        ?>
                        <select name="sala" class="form-control required-field" required>
                            <?php while($row2=mysqli_fetch_array($result2)): ?>
                                <option value="<?php echo $row2['said']; ?>"
                                    <?php echo ($row2['said']==$sa) ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($row2['no'], ENT_QUOTES, 'UTF-8'); ?>
                                </option>
                            <?php endwhile; ?>
                        </select>
                    </div>
                </div>

                <div class="tab-form-row">
                    <div class="tab-form-col">
                        <label><i class="fas fa-laptop"></i> Nome do equipamento</label>
                        <input required type="text" name="nomeq"
                               value="<?php echo htmlspecialchars($row3['nomeequi'], ENT_QUOTES, 'UTF-8')?>"
                               class="form-control required-field" placeholder="Nome do equipamento">
                    </div>
                    <div class="tab-form-col">
                        <label><i class="fas fa-barcode"></i> Nº de série</label>
                        <input type="text" name="nserie" class="form-control"
                               value="<?php echo htmlspecialchars($row3['numserie'] ?? '', ENT_QUOTES, 'UTF-8')?>"
                               placeholder="Nº de série">
                    </div>
                </div>

                <div class="tab-form-row">
                    <div class="tab-form-col">
                        <label><i class="fas fa-trademark"></i> Marca/Modelo</label>
                        <input type="text" name="marcamod" class="form-control"
                               value="<?php echo htmlspecialchars($row3['marca_modelo'] ?? '', ENT_QUOTES, 'UTF-8')?>"
                               placeholder="Marca/Modelo">
                    </div>
                    <div class="tab-form-col">
                        <label><i class="fas fa-calendar-alt"></i> Data da compra</label>
                        <input type="date" name="datacompra" class="form-control"
                               value="<?php echo htmlspecialchars($row3['data_compra'], ENT_QUOTES, 'UTF-8')?>">
                    </div>
                </div>

                <div class="tab-form-row">
                    <div class="tab-form-col-full">
                        <label><i class="fas fa-comment-alt"></i> Observações</label>
                        <textarea class="form-control" rows="4" name="obs"
                                  placeholder="Observações"><?php echo htmlspecialchars($row3['observacoes'])?></textarea>
                    </div>
                </div>

                <div class="text-center mt-3">
                    <button type="submit" class="btn-submit">
                        <i class="fa-solid fa-pen"></i> &nbsp;Atualizar
                    </button>
                </div>
            </form>
        </div>

        <?php if ($isEscolaDigital): ?>
        <!-- TAB: Escola Digital -->
        <div class="tab-pane fade" id="tab-escdig" role="tabpanel">
            <form class="needs-validation" novalidate action="<?php echo SVRURL ?>atualiza_equipamento_OK.php?ide=<?php echo base64_encode($id)?>&&sai=<?php echo base64_encode($sa)?>&&ies=<?php echo base64_encode($idescola)?>" method="post">
                <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($csrf_token); ?>">
                <input type="hidden" name="tipoeq" value="<?php echo htmlspecialchars($row3['tipo'], ENT_QUOTES, 'UTF-8')?>">
                <input type="hidden" name="sala" value="<?php echo htmlspecialchars($sa, ENT_QUOTES, 'UTF-8')?>">
                <input type="hidden" name="nomeq" value="<?php echo htmlspecialchars($row3['nomeequi'], ENT_QUOTES, 'UTF-8')?>">
                <input type="hidden" name="nserie" value="<?php echo htmlspecialchars($row3['numserie'] ?? '', ENT_QUOTES, 'UTF-8')?>">
                <input type="hidden" name="marcamod" value="<?php echo htmlspecialchars($row3['marca_modelo'] ?? '', ENT_QUOTES, 'UTF-8')?>">
                <input type="hidden" name="datacompra" value="<?php echo htmlspecialchars($row3['data_compra'], ENT_QUOTES, 'UTF-8')?>">
                <input type="hidden" name="obs" value="<?php echo htmlspecialchars($row3['observacoes'])?>">

                <div class="tab-form-row">
                    <div class="tab-form-col">
                        <label><i class="fas fa-hashtag"></i> Nº inventário Dgest</label>
                        <input required type="text" name="numinv" class="form-control"
                               style="background-color:#CEF6CE;"
                               value="<?php echo htmlspecialchars($row3['num_inv_dgest'], ENT_QUOTES, 'UTF-8')?>"
                               placeholder="Nº inventário Dgest">
                    </div>
                    <div class="tab-form-col">
                        <label><i class="fas fa-building"></i> Fornecedor</label>
                        <input required type="text" name="fornecedor" class="form-control"
                               style="background-color:#CEF6CE;"
                               value="<?php echo htmlspecialchars($row3['fornecedor'], ENT_QUOTES, 'UTF-8')?>"
                               placeholder="Fornecedor">
                    </div>
                </div>

                <div class="tab-form-row">
                    <div class="tab-form-col">
                        <label><i class="fas fa-envelope"></i> Email do fornecedor</label>
                        <input required type="text" name="emailfornecedor" class="form-control"
                               style="background-color:#CEF6CE;"
                               pattern="[a-z0-9._%+-]+@[a-z0-9.-]+\.[a-z]{2,63}$"
                               value="<?php echo htmlspecialchars($row3['email_fornecedor'], ENT_QUOTES, 'UTF-8')?>"
                               placeholder="Email do fornecedor">
                    </div>
                    <div class="tab-form-col">
                        <label><i class="fas fa-id-card"></i> NIF da pessoa</label>
                        <input required maxlength="9" type="text" name="nifpessoa" class="form-control"
                               style="background-color:#CEF6CE;"
                               oninput="this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\..*)\./g, '$1');"
                               value="<?php echo htmlspecialchars($row3['nif_pessoa'], ENT_QUOTES, 'UTF-8')?>"
                               placeholder="NIF da pessoa">
                    </div>
                </div>

                <div class="tab-form-row">
                    <div class="tab-form-col">
                        <label><i class="fas fa-undo"></i> Nº RMA</label>
                        <input type="text" name="rma" class="form-control"
                               style="background-color:#CEF6CE;"
                               placeholder="Nº RMA">
                    </div>
                </div>

                <div class="text-center mt-3">
                    <button type="submit" class="btn-submit">
                        <i class="fa-solid fa-pen"></i> &nbsp;Atualizar
                    </button>
                </div>
            </form>
        </div>
        <?php endif; ?>

    </div><!-- /tab-content -->
</div><!-- /equipment-tabs -->

<a href="<?php echo SVRURL ?>ver_equipamentos_sala.php?x=<?php echo base64_encode (2) ?>&&si=<?php echo base64_encode ($sa) ?>&&ies=<?php echo base64_encode ($idescola) ?>"  title="Voltar">
<img src="<?php echo SVRURL ?>images/voltar.svg" alt="Voltar">
</a>


<br><br>

                    
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