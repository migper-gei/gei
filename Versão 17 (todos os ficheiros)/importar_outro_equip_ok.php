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
      
      <!-- about -->
      <div  class="about">
         <div class="container">
            <div class="row">
               <div class="col-md-12">
               <div class="titlepage">
                     <h2>Importar outros equipamentos</h2>
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
    $filename0 = $_FILES['file']['name'];
    $tmpPath   = $_FILES['file']['tmp_name'];
    $ext       = strtolower(pathinfo($filename0, PATHINFO_EXTENSION));

    // Verificar is_uploaded_file + extensão + MIME real via finfo
    $uploadOk = false;
    if (!is_uploaded_file($tmpPath)) {
        $uploadErr = 'Upload inválido.';
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

    if (!$uploadOk) {
        ?>
        <script>
        swal({
            title: '<?php echo addslashes($uploadErr); ?>',
            icon: 'error',
        }).then(function() {
            window.location = "<?php echo SVRURL ?>importar_outro_equip.php";
        });
        </script>
        <?php

    } else 
    {
 
        include("validar_delimitadorCSV.php");

        if ($d<>",")
        {

        ?>



        <script>
            
            swal({
        title: 'O ficheiro CSV não tem como delimitador a , (vírgula)!',
        //text: 'Os dados foram guardados!',
        icon: 'error',
        //buttons: false,    
        //position: 'top-rigth',
        
        })
        .then(function() {
        window.location = "<?php echo SVRURL ?>importar_outro_equip.php";
        })
        ;
        
        
        </script>
        
        <?php
     


        }
        else
        {
     

            $idsala=$_POST['sala'];





                $fileName1 = $_FILES["file"]["tmp_name"];
        
                
                if ($_FILES["file"]["size"] > 0) {
                
                    $row=1;
        
                    $file1 = fopen($fileName1, "r");
                 
                    
                    // --- Prepared statements reutilizáveis (preparados uma vez fora do loop) ---
                    $stmt_chk_eq = $db->prepare("SELECT count(*) FROM outro_equipamento WHERE nomeoutro=? AND id_sala=?");
                    $stmt_ins_eq = $db->prepare("INSERT INTO outro_equipamento (id_sala, nomeoutro, qta, observacoes) VALUES (?, ?, ?, ?)");

                    while (($column = fgetcsv($file1, 10000, ",")) !== FALSE) {

                        $column = array_map("utf8_encode", $column);

                        if ($row > 1) {

                            // --- Sanitização: trim a todos os campos lidos do CSV ---
                            $nomeout = trim($column[0] ?? '');
                            $qta     = (int)($column[1] ?? 0);
                            $obs     = trim($column[2] ?? '');

                            // Ignorar linhas sem nome
                            if (empty($nomeout)) { $row++; continue; }

                            // Validar quantidade (deve ser inteiro positivo)
                            if ($qta < 0) { $qta = 0; }

                            // --- Verificar duplicado ---
                            $stmt_chk_eq->bind_param("si", $nomeout, $idsala);
                            $stmt_chk_eq->execute();
                            $stmt_chk_eq->bind_result($contaeq);
                            $stmt_chk_eq->fetch();
                            $stmt_chk_eq->free_result();

                            if ($contaeq == 0) {
                                $stmt_ins_eq->bind_param("isis", $idsala, $nomeout, $qta, $obs);
                                $stmt_ins_eq->execute();
                            }
                        }
                        $row++;
                    }

                    // Fechar prepared statements
                    $stmt_chk_eq->close();
                    $stmt_ins_eq->close();

                    fclose($file1);
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
window.location = "<?php echo SVRURL ?>configura";
})
;



</script>

        <?php
                
            }
        }
        
        
        }






?>
<br>


                    </div>
               
               </div>
            </div>
         </div>
      </div>
      <!-- end about -->
    


      <?php include ("footer.php");?>


   </body>
</html>