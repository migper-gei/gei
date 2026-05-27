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

               </div>
            </div>
            
            <div class="container">
               <div class="row">
                  <div class="col-md-10 offset-md-1">
              
                        
    <!-- Welcome Section -->
           <div class="welcome-section"> 
<?php
include("msg_bemvindo.php");
?>
   </div>


            <?php
                $sql2a = "select max(id) as me  from escolas ";
                $result2a = mysqli_query($db,$sql2a); 
                $rows2a =mysqli_fetch_row($result2a);
                
                
                $maxesc = $rows2a[0];

                 
               $id = (int)base64_decode($_GET['ide']);
               $sa = (int)base64_decode($_GET['sa']);
               $ies = (int)base64_decode($_GET['ies']);

               if ($ies>$maxesc || $ies<0 
                || !isset($id)   || !is_numeric($id)    ||  empty($id) 
               || !isset($sa)   || !is_numeric($sa)    ||  empty($sa) 
               || !isset($ies)   || !is_numeric($ies)    ||  empty($ies) 
               )
               
               {
               ?>
               
               <script>
               window.setTimeout(function() {
                  window.location.href = '<?php echo SVRURL ?>equip';
               }, 10);
               </script>
               
               
               <?php
               
               }
               








                $sql = "select e.*,e.nomeequi as neq,s.nome as nos,e.tipo as ti 
                from equipamento e, salas s
                where s.id=e.id_sala
                and e.id=".$id."";
                $result = mysqli_query($db,$sql); 
                $row=mysqli_fetch_array($result);
               ?>

               <!-- Cabeçalho com info do equipamento -->
               <div style="display:flex; align-items:center; flex-wrap:wrap; gap:16px; margin:14px 0 10px; padding:12px 16px; background:#f4f6fb; border:1px solid #e3e8f4; border-radius:10px;">
                  <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#4b6cb7" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" style="flex-shrink:0;"><rect x="2" y="7" width="20" height="14" rx="2"/><path d="M16 7V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v2"/></svg>
                  <div style="display:flex; flex-direction:column;">
                     <span style="font-size:.7rem; font-weight:600; text-transform:uppercase; letter-spacing:.4px; color:#7b88a0;">Tipo</span>
                     <span style="font-size:.95rem; font-weight:700; color:#182848;"><?php echo htmlspecialchars($row['ti'], ENT_QUOTES, 'UTF-8'); ?></span>
                  </div>
                  <span style="color:#c5cde0;">|</span>
                  <div style="display:flex; flex-direction:column;">
                     <span style="font-size:.7rem; font-weight:600; text-transform:uppercase; letter-spacing:.4px; color:#7b88a0;">Equipamento</span>
                     <span style="font-size:.95rem; font-weight:700; color:#182848;"><?php echo htmlspecialchars($row['neq'], ENT_QUOTES, 'UTF-8'); ?></span>
                  </div>
                  <span style="color:#c5cde0;">|</span>
                  <div style="display:flex; flex-direction:column;">
                     <span style="font-size:.7rem; font-weight:600; text-transform:uppercase; letter-spacing:.4px; color:#7b88a0;">Sala</span>
                     <span style="font-size:.95rem; font-weight:700; color:#182848;"><?php echo htmlspecialchars($row['nos'], ENT_QUOTES, 'UTF-8'); ?></span>
                  </div>
               </div>
         
                          
    
<form name="equipamento" action = "<?php echo SVRURL ?>atualiza_dadostecredes_OK.php?id=<?php echo base64_encode($id)?>" method = "post">



      
 
<br>



<h3 style="color:black;">DADOS TÉCNICOS:</h3>
           
<label>Processador</label>      <br>
<input title="Processador"  type = "text" name = "cpu" placeholder="Processador" value="<?php echo htmlspecialchars($row['processador'], ENT_QUOTES, 'UTF-8')?>">  
              <br>
                <label>Memória (GB)</label>    <br>  
              <input  title="Memória (GB)"  value="<?php echo htmlspecialchars($row['memoria'], ENT_QUOTES, 'UTF-8')?>"
              oninput="this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\..*?)\..*/g, '$1');"
              maxlength="2" type = "text" name = "ram"  maxlength="2" placeholder="Memória (GB)">  
            
              <br>
                <label>Disco</label>    <br>  
              <input   title="Disco" value="<?php echo htmlspecialchars($row['disco'], ENT_QUOTES, 'UTF-8')?>"
                type = "text" name = "disco"   placeholder="Disco">  
              <br>
                <label>Placa gráfica</label>    <br>  
              <input   value="<?php echo htmlspecialchars($row['placagrafica'], ENT_QUOTES, 'UTF-8')?>"  title="Placa gráfica"  type = "text" name = "grafica" placeholder="Placa gráfica">  
              <br>
                <label>Placa rede</label>    <br>  
              <input     value="<?php echo htmlspecialchars($row['placarede'], ENT_QUOTES, 'UTF-8')?>" title="Placa rede" type = "text" name = "rede" placeholder="Placa rede">  
            
              <br>
                <label>Placa som</label>    <br>  
              <input  value="<?php echo htmlspecialchars($row['placasom'], ENT_QUOTES, 'UTF-8')?>" title="Placa som" type = "text" name = "som" placeholder="Placa som">  
              <br>
                <label>Monitor</label>    <br>  
              <input value="<?php echo htmlspecialchars($row['monitor'], ENT_QUOTES, 'UTF-8')?>" title="Monitor"  type = "text" name = "monitor" placeholder="Monitor"> 
              <br>
                <label>Teclado</label>    <br>  
              <input  value="<?php echo htmlspecialchars($row['teclado'], ENT_QUOTES, 'UTF-8')?>" title="Teclado" type = "text" name = "teclado" placeholder="Teclado">
              <select  title="Teclado interface" name="tecladointerface">
              
                     <?php
                     if ($row['tecladointerface']=='USB')
                     {
                   ?>
                     <option value=""></option>
                        <option selected value="USB">USB</option>
                        <option value="PS/2">PS/2</option>
                        <option value="Sem fios">Sem fios</option>
                        <?php
                     }
                   ?>
                    <?php
                     if ($row['tecladointerface']=='PS/2')
                     {
                   ?>
                     <option value=""></option>
                       <option  value="USB">USB</option>
                        <option selected value="PS/2">PS/2</option>
                        <option value="Sem fios">Sem fios</option>
                        <?php
                     }
                   ?>
                    <?php
                     if ($row['tecladointerface']=='Sem fios')
                     {
                   ?>
                     <option value=""></option>
                   <option  value="USB">USB</option>
                   <option value="PS/2">PS/2</option>
                        <option selected value="Sem fios">Sem fios</option>
                        <?php
                     }
                   ?>
                   <?php
                     if ($row['tecladointerface']=='')
                     {
                   ?>
                     <option selected value=""></option>
                   <option  value="USB">USB</option>
                   <option value="PS/2">PS/2</option>
                        <option  value="Sem fios">Sem fios</option>
                        <?php
                     }
                   ?>
                  </select>
              <br>
                <label>Rato</label>    <br>  
              <input value="<?php echo htmlspecialchars($row['rato'], ENT_QUOTES, 'UTF-8')?>" title="Rato:"   type = "text" name = "rato" placeholder="Rato">  
              <select  title="Rato interface" name="ratointerface">
              
                     <?php
                     if ($row['ratointerface']=='USB')
                     {
                   ?>
                    <option value=""></option>
                        <option selected value="USB">USB</option>
                        <option value="PS/2">PS/2</option>
                        <option value="Sem fios">Sem fios</option>
                        <?php
                     }
                   ?>
                    <?php
                     if ($row['ratointerface']=='PS/2')
                     {
                   ?>
                        <option value=""></option>
                       <option  value="USB">USB</option>
                        <option selected value="PS/2">PS/2</option>
                        <option value="Sem fios">Sem fios</option>
                        <?php
                     }
                   ?>
                    <?php
                     if ($row['ratointerface']=='Sem fios')
                     {
                   ?>
                   <option value=""></option>
                   <option  value="USB">USB</option>
                   <option value="PS/2">PS/2</option>
                        <option selected value="Sem fios">Sem fios</option>
                        <?php
                     }
                   ?>
                     <?php
                     if ($row['ratointerface']=='')
                     {
                   ?>
                    <option selected value=""></option>
                   <option value="USB">USB</option>
                   <option value="PS/2">PS/2</option>
                        <option  value="Sem fios">Sem fios</option>
                        <?php
                     }
                   ?>
                  </select>
              <br /> 
              <br>
         
              <label>Colunas: </label>  
              <select  title="Colunas" name="colunas">
                   <option value=""></option>
                   <?php
                     if ($row['colunas']=='Sim')
                     {
                   ?>
                   <option selected value="Sim">Sim</option>
                   <?php
                     }else
                     {
                   ?>
                   <option value="Não">Não</option>
                   <?php
                     }
                   ?>
             </select>
             &nbsp;&nbsp;&nbsp;&nbsp;
         
        
             <label>CD/DVD: </label>  
              <select  title="CD/DVD" name="cddvd">
                   <option value=""></option>
                   <?php
                     if ($row['cd_dvd']=='Sim')
                     {
                   ?>
                   <option selected value="Sim">Sim</option>
                   <?php
                     }else
                     {
                   ?>
                   <option value="Não">Não</option>
                   <?php
                     }
                   ?>
             </select>
                   
                     <br> 
                     <br>
                     <br>   
<h3 style="color:black;">DADOS REDE:</h3>        
           <br> <label>Domínio</label>  <br>
            <input  value="<?php echo htmlspecialchars($row['dominio'], ENT_QUOTES, 'UTF-8')?>" size="30" type = "text" name = "dominio" placeholder="Domínio"> 
            
              <br>
                <label>Endereço IP</label>    <br>  
            <input  value="<?php echo htmlspecialchars($row['ip'], ENT_QUOTES, 'UTF-8')?>"  size="15" maxlength=15 type = "text" name = "ip" placeholder=" Endereço IP">
            <br>
                <label>Máscara de rede</label>    <br> 
            <input  value="<?php echo htmlspecialchars($row['mascara_rede'], ENT_QUOTES, 'UTF-8')?>" size="15" maxlength=15 type = "text" name = "mascara" placeholder="Máscara de rede">  
            <br>
                <label>Gareway</label>    <br> 
            <input   value="<?php echo htmlspecialchars($row['gateway'], ENT_QUOTES, 'UTF-8')?>" size="15" maxlength=15 type = "text" name = "gateway" placeholder="Gateway">   
            <br>
                <label>DNS preferido</label>    <br> 
         
            <input  value="<?php echo htmlspecialchars($row['dns_principal'], ENT_QUOTES, 'UTF-8')?>" size="15" maxlength=15 type = "text" name = "dnsp" placeholder="DNS preferido">
            <br>DND alternativo</label>    <br> 
            <input   value="<?php echo htmlspecialchars($row['dns_alternativo'], ENT_QUOTES, 'UTF-8')?>" size="15" maxlength=15 type = "text" name = "dnsa" placeholder="DNS alternativo"> 
         
         
         

                     <br />   

                     <div  style=" text-align:center;width:90%"> <input  type = "submit" value = "Atualizar"/>     </div>
  
                 </form>


                 <a href="<?php echo SVRURL ?>equip">
<img src="<?php echo SVRURL ?>images/voltar.svg" alt="Voltar">
</a>
<br><br>

                    
                  </div>
               </div>
            </div>
         </div>
      </div>
      <!-- end about -->
    



      <?php include ("footer.php");?>


   </body>
</html>