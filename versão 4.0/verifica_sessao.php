 <?php
  if(isset($_SESSION['login_user'])) 
  {
    
//echo "A sessão está ativa" . $_SESSION['login_user'];
 echo " <h5>  ". $_SESSION['login_user'];

 ?>
    
 |   
  
<a class="underlineHover" title="Terminar sessão" href="<?php echo SVRURL ?>sair"><h6 style="color:blue;">Terminar sessão</h6></a>

&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
<img src="<?php echo SVRURL ?>images/chat.svg" alt="Chat">
<a  target="_new" class="underlineHover" title="Chat" href="<?php echo SVRURL ?>chat/index.php"><h6 style="color:blue;">CHAT</h6></a>


<?php
}
  
    else{
      ?>
     
      <script>
      window.setTimeout(function() {
          window.location.href = '<?php echo SVRURL ?>i';
      }, 10);
      </script>


      <?php
      
    }
  ?>

