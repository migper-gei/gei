
<table style="width: 100%;">  

<tbody>  
    <tr>  
        <td >
          

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
    ?>
 

    <?php
//echo "A sessão está ativa" . $_SESSION['login_user'];
 echo " <h5>  ". $_SESSION['login_user'];
 // echo ( $_SESSION['user_id']); 
 

 

 

  ?>


<?php


$tipo=$_SESSION['tipo'];

if ($tipo==1)
echo ('(Administrador)');
elseif ($tipo==2)
echo ('(Utilizador)');
elseif ($tipo==3)
echo ('(Reparador)');
elseif ($tipo==4) 
echo ('(Funcionário)');
?>

<a href="<?php echo SVRURL ?>reset_pass.php" title="Mudar password">
   <img src="<?php echo SVRURL ?>images/key.svg">
  </a>


<?php
//session_start();


$id_user=$_SESSION['user_id'];


$sql = "select dataalteracaopass from utilizadores where id=".$id_user."";
$result = mysqli_query($db,$sql);
$rows2 =mysqli_fetch_row($result);

$dataatual=date('Y-m-d');


?>

|

<a class="underlineHover" title="Terminar sessão" href="<?php echo SVRURL ?>sair"><h6 style="color:blue;">Terminar sessão</h6></a>


<?php
if ( ($rows2[0]==Null) or ($dataatual>=$rows2[0]))
{
  echo str_repeat('&nbsp;', 2);
echo ('<i><u>Deve mudar a password</u></i>');


}
?>
        </td>  
        <td  > 
</td>  
<td ><h5>Ano: 

<?php
$sql2 = "select max(ano_lectivo) from periodos";
$result2 = mysqli_query($db,$sql2); 
$rows2 =mysqli_fetch_row($result2);

$conta = $rows2[0];
echo $conta;

if ($conta == null)
{
echo 'não definido';

}
?>

</h5></td>  
    </tr>  


    <tr>  
        <td >

        <img src="<?php echo SVRURL ?>images/chat.svg" alt="Chat">
<a  target="_new" class="underlineHover" title="Chat" href="<?php echo SVRURL ?>chat/index.php">
<h6 style="color:blue;">CHAT</h6></a>

<?php



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



        </td>  
        <td ></td>  
        <td ></td>  
    </tr>  
   
</tbody>  
</table>  









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

      mysqli_close($db);
    }
  ?>

