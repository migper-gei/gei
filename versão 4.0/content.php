
	<!-- start content -->
	<div id="content">
	  <div class="post">
            <h2 class="title"><b>GEI - Gestão do Equipamento Informático  </h2> 
           
       
           
 
<?php
           


           // session_start();
if(isset($_SESSION['login_user'])) 
{
//echo "A sessão está ativa" . $_SESSION['login_user'];
 echo "<h3> Bem vindo: ". $_SESSION['login_user'];
 echo str_repeat("&nbsp;", 85);
 ?>
<a href="<?php echo SVRURL ?>sair">Sair</a>
<?php
}
  else{
    echo "<h3>Não tem sessão iniciada.";
    //echo str_repeat("&nbsp;", 85);
   // echo '<a href="index.php">Login</a>'; 
  }
  
  ?>
  <br>  <br>
   <h2>Dê-nos a sua opinião/sugestão de melhoria.  Clique 
   <a target=new href="https://forms.gle/ZCoWD8zacWLx6mv1A">AQUI.</a></h2> 
        
  <!-- echo "&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;";
             <h3> Bem vindo: <?php echo($_SESSION['login_user'])?>
             echo " <a href="logout.php">Sair</a>";
-->
          

</h3>   
            
           
              

           
            <br>


<?php
//include('config.php');

  $sql1 = "select count(*) from avaria_reparacao where datareparacao is null";
  $result1 = mysqli_query($db,$sql1); 
  $rows =mysqli_fetch_row($result1);
  
  //echo($_SESSION['tipo']);

  $totallinhas = $rows[0];

  if ($totallinhas >0 && isset($_SESSION['login_user']) && $_SESSION['tipo']==1)
      {
  ?>
  <h2>
  <img src="<?php echo SVRURL ?>images/aviso.png" alt="Reparações a efetuar">
  ATENÇÃO: Existem reparações a efetuar - <a title="Reparações a efetuar" href="<?php echo SVRURL ?>reparacoes_efetuar.php">
  <img src="<?php echo SVRURL ?>images/reparacao.png" alt="Reparações a efetuar">
  </a>
</h2>
<?php
      echo('<br><br>');
    }
     
?>

     
			<div class="entry">
				
   
        <br>   <br>
        <p>
                
                GEI é uma aplicação que permite criar uma base 
                    de dados de todo o equipamento informático existente (computadores, impressoras, routers, pontos de acesso, quadros interativos, videoprojetores, ...).</p>
                <p><br />
                         <u>  FUNCIONALIDADES PRINCIPAIS:   </u> 
                </p>
                <P>
                -  inventário preciso de todos os equipamentos informáticos. Todas as características de cada equipamento são armazenadas numa base de dados. </P>
                <p>-  gestão e histórico das ações de avarias/reparações e tarefas de manutenção.
              </p>
                 <p>
                 -  registo das avarias dos utilizadores.</p>
                              <p>
                    -  listagens e estatísticas. 
                </p>
                <p>
                    -  importação e exportação de dados. 
                </p>
			</div>
			
        </div>
        	<!--
        <div class="post">
            <img width="15%" height="10%" alt="Computador" class="style1" src="<?php echo SVRURL ?>images/2comp.jpg" title="Computador" />
            <img width="15%" height="10%"
                alt="Software" class="style8" src="<?php echo SVRURL ?>images/sw.jpg" title="Software" />
                <img  width="15%" height="10%" alt="Impressora" 
                class="style2" src="<?php echo SVRURL ?>images/printer_1.gif" title="Impressora" />
                <img width="15%" height="10%" alt="Quadro Interativo" 
                class="style3" src="<?php echo SVRURL ?>images/quadro_i.jpg" title="Quadro Interativo" />
                <img width="15%" height="10%"
                alt="Videoprojetor" class="style4" src="<?php echo SVRURL ?>images/videoprojetor.jpg" title="Videoprojetor" />
                <img  width="15%" height="10%"
				alt="Router" class="style5" src="<?php echo SVRURL ?>images/router.jpg" title="Router" />
				<img class="style6" src="images/pontoacesso.jpg" title="Ponto de acesso" /><br /> 
        </div>-->

        <div class="post">
		
	        <br />
	  </div>
		
	</div>
	<!-- end content -->
	
	<div style="clear: both;">&nbsp;</div>
</div>
<!-- end page -->