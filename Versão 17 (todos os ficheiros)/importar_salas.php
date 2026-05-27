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

include ("css_inserir.php");

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
                        <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="#4b6cb7" stroke-width="2.3" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="3"/><path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 0 1-2.83 2.83l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-4 0v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 0 1-2.83-2.83l.06-.06A1.65 1.65 0 0 0 4.68 15a1.65 1.65 0 0 0-1.51-1H3a2 2 0 0 1 0-4h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 0 1 2.83-2.83l.06.06A1.65 1.65 0 0 0 9 4.68a1.65 1.65 0 0 0 1-1.51V3a2 2 0 0 1 4 0v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 0 1 2.83 2.83l-.06.06A1.65 1.65 0 0 0 19.4 9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 0 1 0 4h-.09a1.65 1.65 0 0 0-1.51 1z"/></svg>
                        <span style="color:#4b6cb7;">Configurações</span>
                     </li>
                     <li style="color:#c5cde0;font-size:.9rem;">&#8250;</li>
                     <li style="color:#1e2a45;">Salas >> Importação</li>
                  </ol>
               </nav>
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


  
    <?php

//include("config.php");



$message = "";
if (isset($_POST['submit'])) {
    $filename0 = $_FILES['file']['name']  ?? '';
    $tmpPath   = $_FILES['file']['tmp_name'] ?? '';
    $ext       = strtolower(pathinfo($filename0, PATHINFO_EXTENSION));

    // Verificar is_uploaded_file + tamanho + extensão + MIME real via finfo
    $uploadOk = false;
    if (!is_uploaded_file($tmpPath)) {
        $uploadErr = 'Upload inválido.';
    } elseif ($_FILES['file']['size'] > 2097152) {
        $uploadErr = 'O ficheiro excede o tamanho máximo permitido (2 MB)!';
    } elseif ($ext !== 'csv') {
        $uploadErr = 'Ficheiro inválido, deve ser .CSV!';
    } else {
        $finfo    = finfo_open(FILEINFO_MIME_TYPE);
        $mimeReal = finfo_file($finfo, $tmpPath);
        finfo_close($finfo);
        $allowedMimes = ['text/plain', 'text/csv', 'application/csv', 'application/vnd.ms-excel'];
        if (!in_array($mimeReal, $allowedMimes, true)) {
            $uploadErr = 'O ficheiro não tem conteúdo CSV válido!';
        } else {
            $uploadOk = true;
        }
    }

    if (!$uploadOk) { ?>
<script>
swal({
    title: '<?php echo addslashes($uploadErr ?? 'Erro no upload.'); ?>',
    icon: 'error',
})
.then(function() {
    window.location = "<?php echo SVRURL ?>importarsalas";
});
</script>
<?php
    } else {
        include("validar_delimitadorCSV.php");

        if (!isset($d) || !in_array($d, [',', ';', "\t"], true))
        {
       ?>
       <script>
       swal({
       title: 'O ficheiro CSV não tem um delimitador reconhecido (vírgula, ponto e vírgula ou tabulação)!',
       icon: 'error',
       })
       .then(function() {
       window.location = "<?php echo SVRURL ?>importarsalas";
       });
       </script>
       <?php
        }
        else
        {
            $idescola = (int)$_POST['escola'];
            $fileName1 = $_FILES["file"]["tmp_name"];

                if ($_FILES["file"]["size"] > 0) {
                
                    $row=1;
                    $file1 = fopen($fileName1, "r");

                    // Prepared statements fora do loop
                    $stmt_chk = $db->prepare("SELECT id FROM salas WHERE nome = ? AND id_escola = ? LIMIT 1");
                    $stmt_ins = $db->prepare("INSERT INTO salas (nome, localizacao, departamento, id_escola, equip_requisitavel) VALUES (?, ?, ?, ?, ?)");

                    while (($column = fgetcsv($file1, 10000, $d)) !== FALSE) {

                        $column = array_map(function($v) { return mb_convert_encoding($v, 'UTF-8', 'UTF-8, ISO-8859-1'); }, $column);

                   

            
                         if ($row>1)
                         {
                       
                                           
                            $nome   = trim($column[0] ?? '');
                            $local  = trim($column[1] ?? '');
                            $depart = trim($column[2] ?? '');
                            $eqreq  = trim($column[3] ?? '');

                            // Saltar linhas de cabeçalho ou inválidas
                            // (inclui ficheiros Excel com 2 linhas de cabeçalho)
                            if (empty($nome)) { $row++; continue; }

                            // Normalizar e validar equip_requisitavel
                            $eqreqNorm = mb_strtolower($eqreq, 'UTF-8');
                            if (str_contains($eqreqNorm, 'sim')) {
                                $eqreq = 'Sim';
                            } elseif (str_contains($eqreqNorm, 'n')) {
                                $eqreq = 'Não';
                            } else {
                                // Valor não reconhecido — linha de cabeçalho ou inválida, saltar
                                $row++; continue;
                            }

                            $stmt_chk->bind_param("si", $nome, $idescola);
                            $stmt_chk->execute();
                            $stmt_chk->store_result();
                            $count = $stmt_chk->num_rows;
                            $stmt_chk->free_result(); // libertar resultado (não fechar — reutilizado no loop)
                            
                           if ($count==0)
                           {
                            $stmt_ins->bind_param("sssss", $nome, $local, $depart, $idescola, $eqreq);
                            $stmt_ins->execute();
                            }
                            
                        }
                            $row=$row+1;
                      
                           
        
                        //
        
        
                    }
                        fclose($file1);
                    $stmt_chk->close();
                    $stmt_ins->close();
                }


        ?>
        


        <script>
    
    swal({
title: 'O ficheiro foi importado com sucesso!',
//text: 'Os dados foram guardados!',
icon: 'success',
//buttons: false,    
//position: 'top-rigth',

})
.then(function() {
window.location = "<?php echo SVRURL ?>sala?x=<?php echo base64_encode(1) ?>&&escola=<?php echo base64_encode($idescola) ?>";
})
;


</script>

        <?php
                
            }
        }
        
        
        }






?>

<form class="needs-validation" novalidate enctype="multipart/form-data" method="post" action="<?php echo SVRURL ?>importarsalas">
                
<div class="action-section">
    
    <h2 class="section-title"><i class="fas fa-school btn-icon"></i> Selecione a Instituição</h2>
    
    
<select  name="escola" required class="form-control required-field"  style="width:100%;">


<?php
 $sql = "SELECT * FROM escolas ORDER by nome_escola";
 $result = mysqli_query($db,$sql);
 $rowcount = mysqli_num_rows($result);

while($row=mysqli_fetch_array($result))
{


  echo('<option value="'.$row['id'].'">'.$row['nome_escola'].'</option>');


}


echo('</select>');

?>
</diV>
<br>

                <div class="form-group">

                <div class="action-section">
                    <label for="file">Escolha o ficheiro .CSV para importar (Caso já exista a sala, não é importada)</label>
                    <input name="file" type="file" class="form-control required-field" required accept=".csv">
                    <small style="display:block;margin-top:6px;color:#7b88a0;font-size:.75rem;">
                        <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="vertical-align:middle;margin-right:3px;"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
                        Formato: <strong>.CSV</strong> com delimitador <strong>vírgula ( , )</strong> ou <strong>ponto e vírgula ( ; )</strong> &nbsp;·&nbsp; Tamanho máximo: <strong>2 MB</strong>
                    </small>
</div>
                </div>
                <div class="form-group">
                    <?php echo $message; ?>
                </div>
                <br>
                <div class="form-group">
                <div  style=" text-align:center;width:100%"> 
                    
                <button type="submit" name="submit" class="btn-submit">
                <i class="fa-solid fa-file-import"></i>
                                        &nbsp;Importar salas
                                    </button>
                 
               </div>
            </form>

<bR>
         

   
<a href="<?php echo SVRURL ?>configura">
    <img src="<?php echo SVRURL ?>images/voltar.svg" alt="Voltar">
</a>



<br><br>



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