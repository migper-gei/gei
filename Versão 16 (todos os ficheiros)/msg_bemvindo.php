    <?php
           
  $_SESSION['user_agent'] = $_SERVER['HTTP_USER_AGENT'];


include("verifica_sessao.php");
  
  ?>

<style>

.notifications {
            display: flex;
            gap: 20px;
            margin-top: 30px;
        }
        
        .notification-card {
       
          flex: 1;
            background-color: white;
            border-radius: 10px;
            padding: 7px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
           
            display: flex;
            align-items: center;
            justify-content: space-between;
        }
        
        .notification-card.warning {
           /* border-left: 5px solid var(--warning);  */
        }
        
        .notification-card.task {
            /* border-left: 5px solid var(--success); */
        }
        
        .notification-content {
            display: flex;
            align-items: center;
            gap: 10px;
        }
        
        .notification-icon {
            display: flex;
            align-items: center;
            justify-content: center;
            width: 40px;
            height: 40px;
            border-radius: 50%;
        }
        
        .warning .notification-icon {
            background-color: rgba(255, 193, 7, 0.2);
            color: #ff9800;
        }
        
        .task .notification-icon {
            background-color: rgba(25, 135, 84, 0.2);
            color: var(--success);
        }
        
        .notification-text {
            font-weight: 500;
        }
        
        .warning .notification-text {
            color: #e67e22;
        }
        
        .task .notification-text {
            color: var(--success);
        }
        
</style>


<?php
//include('config.php');

  $sql1 = "select count(*) from avarias_reparacoes where datareparacao is null";
  $result1 = mysqli_query($db,$sql1); 
  $rows =mysqli_fetch_row($result1);
  
  //echo($_SESSION['tipo']);

  $totallinhas = $rows[0];


  $sql2 = "select count(*) from tarefas where data_conclusao is null";
  $result2 = mysqli_query($db,$sql2); 
  $rows2 =mysqli_fetch_row($result2);
  $totallinhas2 = $rows2[0];

  if ($totallinhas >0 && isset($_SESSION['login_user']) && ($_SESSION['tipo']==1 || $_SESSION['tipo']==3)   )
      {
  ?>
  &nbsp; 

 
  
 
  <table style="width: 100%;" >
  <tr>
   <td>

    <!-- 
  <h4>
  <img src="<?php echo SVRURL ?>images/aviso.svg" alt="Reparações a efetuar">
  ATENÇÃO: Existem reparações a efetuar 
  
  <img src="<?php echo SVRURL ?>images/seta.svg">
  <a title="Ver reparações a efetuar" href="<?php echo SVRURL ?>reparafaz?op=t">
  <img src="<?php echo SVRURL ?>images/reparacao.svg" alt="Ver reparações a efetuar">
  </a></h4>
    </td>

    <td>
    &nbsp;   &nbsp;   &nbsp; -->


 
        
              
    <div class="notification-card warning">
                
                    <div><span>⚠️</span>Existem reparações a efetuar 
                    <a class="underlineHover" title="Ver reparações a efetuar" href="<?php echo SVRURL ?>reparafaz?op=t">
                    <img src="<?php echo SVRURL ?>images/reparacao.svg" alt="Ver reparações a efetuar">
                    </a>
                  </div>
             
      </div>
         
     



      </td>

    <td style="text-align: right;">

    <?php
  if ($totallinhas2 >0 &&  $_SESSION['tipo'] ==1)
  {
    // echo ('<h4>'.'Existem tarefas por realizar');
   

  ?>

    <!-- Notifications -->
 
                       
   
            
                  
    <div class="notification-card warning">
                    
                    <div  >     <span>📋</span>Existem tarefas por realizar  
                    <a class="underlineHover" title="Ver tarefas a realizar" href="<?php echo SVRURL ?>tarefas.php?x=<?php echo base64_encode(0) ?>">
  <img src="<?php echo SVRURL ?>images/tarefas.svg" alt="Ver tarefas a realizar">
  </a>
                  </div>
            
                    <div>
    
      



<!--

  <img src="<?php echo SVRURL ?>images/seta.svg">
  <a title="Ver tarefas a realizar" href="<?php echo SVRURL ?>tarefas.php?x=<?php echo base64_encode(0) ?>">
  <img src="<?php echo SVRURL ?>images/tarefas.svg" alt="Ver tarefas a realizar">
  </a>
  </h4>
  -->


  <?php

  }
?>



    </td>  

  </tr>

</table>


<?php
     // echo('<br><br>');
    }
     
?>
