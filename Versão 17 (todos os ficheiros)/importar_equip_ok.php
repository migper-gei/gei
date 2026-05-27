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
                     <h2>Importar Equipamentos</h2>
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

    // FIX: verificar is_uploaded_file + MIME real via finfo + extensão
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
            window.location = "<?php echo SVRURL ?>importar_equip.php";
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
        window.location = "<?php echo SVRURL ?>importar_equip.php";
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
                    $stmt_chk_tipo = $db->prepare("SELECT count(*) FROM tipos_equipamento WHERE nome=?");
                    $stmt_ins_tipo = $db->prepare("INSERT INTO tipos_equipamento (nome) VALUES (?)");
                    $stmt_chk_eq   = $db->prepare("SELECT count(*) FROM equipamento WHERE nomeequi=? AND id_sala=?");
                    $stmt_ins_eq_d = $db->prepare("INSERT INTO equipamento (nomeequi,id_sala,tipo,marca_modelo,numserie,processador,memoria,disco,placagrafica,placarede,placasom,monitor,teclado,tecladointerface,rato,ratointerface,colunas,cd_dvd,dominio,ip,mascara_rede,gateway,dns_principal,dns_alternativo,data_compra,observacoes,escola_digital,num_inv_dgest,fornecedor,email_fornecedor,nif_pessoa) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,STR_TO_DATE(?,'%Y-%m-%d'),?,?,?,?,?,?)");
                    $stmt_ins_eq_s = $db->prepare("INSERT INTO equipamento (nomeequi,id_sala,tipo,marca_modelo,numserie,processador,memoria,disco,placagrafica,placarede,placasom,monitor,teclado,tecladointerface,rato,ratointerface,colunas,cd_dvd,dominio,ip,mascara_rede,gateway,dns_principal,dns_alternativo,observacoes,escola_digital,num_inv_dgest,fornecedor,email_fornecedor,nif_pessoa) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)");

                    while (($column = fgetcsv($file1, 10000, ",")) !== FALSE) {

                        $column = array_map("utf8_encode", $column);

                        if ($row > 1) {

                            // --- Sanitização: trim a todos os campos lidos do CSV ---
                            $nomeeq      = trim($column[0]  ?? '');
                            $nserie      = trim($column[1]  ?? '');
                            $marca       = trim($column[2]  ?? '');
                            $tipo        = trim($column[3]  ?? '');
                            $proc        = trim($column[4]  ?? '');
                            $mem         = trim($column[5]  ?? '');
                            $disco       = trim($column[6]  ?? '');
                            $plg         = trim($column[7]  ?? '');
                            $pls         = trim($column[8]  ?? '');
                            $plr         = trim($column[9]  ?? '');
                            $mon         = trim($column[10] ?? '');
                            $tec         = trim($column[11] ?? '');
                            $tecinterface= trim($column[12] ?? '');
                            $rato        = trim($column[13] ?? '');
                            $ratointerface=trim($column[14] ?? '');
                            $col         = trim($column[15] ?? '');
                            $cddvd       = trim($column[16] ?? '');
                            $dom         = trim($column[17] ?? '');
                            $ip          = trim($column[18] ?? '');
                            $mas         = trim($column[19] ?? '');
                            $gat         = trim($column[20] ?? '');
                            $dns1        = trim($column[21] ?? '');
                            $dns2        = trim($column[22] ?? '');
                            $dcomp       = trim($column[23] ?? '');
                            $obs         = trim($column[24] ?? '');
                            $esdig       = trim($column[25] ?? '');
                            $numid       = trim($column[26] ?? '');
                            $forn        = trim($column[27] ?? '');
                            $emailforn   = trim($column[28] ?? '');
                            $nifpessoa   = trim($column[29] ?? '');

                            // Ignorar linhas sem nome de equipamento
                            if (empty($nomeeq)) { $row++; continue; }

                            // --- Verificar/inserir tipo de equipamento ---
                            $stmt_chk_tipo->bind_param("s", $tipo);
                            $stmt_chk_tipo->execute();
                            $stmt_chk_tipo->bind_result($contateq);
                            $stmt_chk_tipo->fetch();
                            $stmt_chk_tipo->free_result();

                            if ($contateq == 0 && !empty($tipo)) {
                                $stmt_ins_tipo->bind_param("s", $tipo);
                                $stmt_ins_tipo->execute();
                            }

                            // --- Verificar duplicado de equipamento na sala ---
                            $stmt_chk_eq->bind_param("si", $nomeeq, $idsala);
                            $stmt_chk_eq->execute();
                            $stmt_chk_eq->bind_result($contaeq);
                            $stmt_chk_eq->fetch();
                            $stmt_chk_eq->free_result();

                            if ($contaeq == 0) {
                                // Validar data de compra (YYYY-MM-DD ou vazio)
                                $dcompObj = DateTime::createFromFormat('Y-m-d', $dcomp);
                                $dcompValida = $dcompObj && $dcompObj->format('Y-m-d') === $dcomp;

                                if ($dcompValida) {
                                    // INSERT com data de compra
                                    $stmt_ins_eq_d->bind_param(
                                        "sissssssssssssssssssssssssssss",
                                        $nomeeq, $idsala, $tipo, $marca, $nserie,
                                        $proc, $mem, $disco, $plg, $plr, $pls,
                                        $mon, $tec, $tecinterface, $rato, $ratointerface,
                                        $col, $cddvd, $dom, $ip, $mas, $gat,
                                        $dns1, $dns2, $dcomp, $obs,
                                        $esdig, $numid, $forn, $emailforn, $nifpessoa
                                    );
                                    $stmt_ins_eq_d->execute();
                                } else {
                                    // INSERT sem data de compra
                                    $stmt_ins_eq_s->bind_param(
                                        "sissssssssssssssssssssssssssss",
                                        $nomeeq, $idsala, $tipo, $marca, $nserie,
                                        $proc, $mem, $disco, $plg, $plr, $pls,
                                        $mon, $tec, $tecinterface, $rato, $ratointerface,
                                        $col, $cddvd, $dom, $ip, $mas, $gat,
                                        $dns1, $dns2, $obs,
                                        $esdig, $numid, $forn, $emailforn, $nifpessoa
                                    );
                                    $stmt_ins_eq_s->execute();
                                }
                            }
                        }
                        $row++;
                    }

                    // Fechar prepared statements
                    $stmt_chk_tipo->close();
                    $stmt_ins_tipo->close();
                    $stmt_chk_eq->close();
                    $stmt_ins_eq_d->close();
                    $stmt_ins_eq_s->close();

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