 <?php

//echo $_SESSION['user_agent'];
//echo ('<br><br>');
//echo $_SERVER['HTTP_USER_AGENT'];


if ( $_SESSION['user_agent'] != $_SERVER['HTTP_USER_AGENT'])
 {
   exit;
 }




  if(isset($_SESSION['login_user'])) 
  {
    
//echo "A sessão está ativa" . $_SESSION['login_user'];
 echo " <h5>  ". $_SESSION['login_user'];
 // echo ( $_SESSION['user_id']); 
 

 

 

  ?>


<?php





$id_user=$_SESSION['user_id'];
$sql0 = "select tipo from utilizadores where id=$id_user";
$result0 = mysqli_query($db,$sql0); 
$rows0 =mysqli_fetch_row($result0);

//echo($_SESSION['tipo']);

$tipo = $rows0[0];

if ($tipo==1)
echo ('(Administrador)');
elseif ($tipo==2)
echo ('(Utilizador)');
elseif ($tipo==3)
echo ('(Reparador)');
elseif ($tipo==4) 
echo ('(Funcionário)');
?>





|

<a class="underlineHover" title="Terminar sessão" href="<?php echo SVRURL ?>sair"><h6 style="color:blue;">Terminar sessão</h6></a>

&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
&nbsp;&nbsp;&nbsp;&nbsp;
<img src="<?php echo SVRURL ?>images/chat.svg" alt="Chat">
<a  target="_new" class="underlineHover" title="Chat" href="<?php echo SVRURL ?>chat/index.php">
<h6 style="color:blue;">CHAT</h6></a>

<?php

$id_user=$_SESSION['user_id'];

$sql1 = "select count(*) from chat_message where to_user_id=$id_user and status=1";
$result1 = mysqli_query($db,$sql1); 
$rows =mysqli_fetch_row($result1);

//echo($_SESSION['tipo']);

$nummsg = $rows[0];

//echo $nummsg;

if ($nummsg>0)
{
echo ('(Tem mensagens)');

}
else 
{
  echo ('(Não tem mensagens)');
}

?>







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

