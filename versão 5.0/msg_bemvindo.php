    <?php
           
include("verifica_sessao.php");
  
  ?>

<?php
//include('config.php');




  $sql1 = "select count(*) from avarias_reparacoes where datareparacao is null";
  $result1 = mysqli_query($db,$sql1); 
  $rows =mysqli_fetch_row($result1);
  
  //echo($_SESSION['tipo']);

  $totallinhas = $rows[0];

  if ($totallinhas >0 && isset($_SESSION['login_user']) && ($_SESSION['tipo']==1 || $_SESSION['tipo']==3)   )
      {
  ?>
  &nbsp; 


  <h3>
  <img src="<?php echo SVRURL ?>images/aviso.svg" alt="Reparações a efetuar">
  ATENÇÃO: Existem reparações a efetuar 
  
  <img src="<?php echo SVRURL ?>images/seta.svg">
  <a title="Ver reparações a efetuar" href="<?php echo SVRURL ?>reparafaz?op=t">
  <img src="<?php echo SVRURL ?>images/reparacao.svg" alt="Ver reparações a efetuar">
  </a>
</h3>
<?php
      //echo('<br>');
    }
     
?>
