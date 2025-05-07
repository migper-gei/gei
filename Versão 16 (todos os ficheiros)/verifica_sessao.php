   
   <style>
   

   .welcome-section {

            padding: 15px;
            background-color: #f8f9fc;
            border-radius: 6px;
            margin-bottom: 20px;
            border-left: 4px solid #36b9cc;

   }
   
   .action-section {
            background-color: white;
            border-radius: 8px;
            padding: 20px;
            margin-bottom: 20px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
            border-left: 4px solid #4e73df;
        }
   

        .section-title {
            font-size: 1.1rem;
            font-weight: 600;
            color: #4e73df;
            margin-bottom: 15px;
            padding-bottom: 10px;
            border-bottom: 1px solid #e3e6f0;
        }


        .action-button {
            width: 100%;
            padding: 12px 15px;
            margin-bottom: 15px;
            border-radius: 6px;
            font-weight: 500;
            transition: all 0.3s ease;
            border: none;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        
        .btn-primary-action {
            background-color: #4e73df;
            color: white;
            box-shadow: 0 4px 6px rgba(78, 115, 223, 0.25);
        }
        
        .btn-primary-action:hover {
            background-color: #3a5ccc;
            transform: translateY(-2px);
            box-shadow: 0 6px 8px rgba(78, 115, 223, 0.3);
        }
        
        .btn-secondary-action {
            background-color: #fff;
            color: #4e73df;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.08);
            border: 1px solid #e3e6f0;
        }
        
        .btn-secondary-action:hover {
            background-color: #f8f9fc;
            transform: translateY(-2px);
            box-shadow: 0 6px 8px rgba(0, 0, 0, 0.1);
        }
        
        .btn-danger-action {
            background-color: #e74a3b;
            color: white;
            box-shadow: 0 4px 6px rgba(231, 74, 59, 0.25);
        }
        
        .btn-danger-action:hover {
            background-color: #d52a1a;
            transform: translateY(-2px);
            box-shadow: 0 6px 8px rgba(231, 74, 59, 0.3);
        }
        
        .btn-outline-action {
            background-color: transparent;
            color: #4e73df;
            border: 1px solid #4e73df;
            box-shadow: 0 4px 6px rgba(78, 115, 223, 0.1);
        }
        
        .btn-outline-action:hover {
            background-color: #eaecf4;
            transform: translateY(-2px);
            box-shadow: 0 6px 8px rgba(78, 115, 223, 0.2);
        }

        .password-warning {
            font-style: italic;
            color: var(--danger);
            font-weight: 500;
        }
        
   </style>



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


<a class="underlineHover" href="<?php echo SVRURL ?>reset_pass.php" title="Mudar password">

<!--<img src="<?php echo SVRURL ?>images/key.svg">-->
<i class="fa-solid fa-key fa-xl" style="color: #FFD43B;"></i>

</a>

<?php
//session_start();


$id_user=$_SESSION['user_id'];


$sql22 = "select dataalteracaopass from utilizadores where id=".$id_user."";
$result22 = mysqli_query($db,$sql22);
$rows22 =mysqli_fetch_row($result22);

$dataatual=date('Y-m-d');


?>

|

<a class="underlineHover" title="Terminar sessão" href="<?php echo SVRURL ?>sair"><h6 style="color:blue;">Terminar sessão</h6></a>


<?php
if ( ($rows22[0]==Null) or ($dataatual>=$rows22[0]))
{
  echo str_repeat('&nbsp;', 2);
  ?>
          <span class="password-warning">(Deve mudar a password)</span>


<?php
//          echo ('<i><u>Deve mudar a password</u></i>');
}
?>


        </td>  


        <td > 
 
</td>  


<td style="text-align: right;">
Ano: 

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
  </td>  
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
        <td style="text-align: right;">
        <?php
if ($_SESSION['tipo']<>1)
{
  ?>

<a  target="_blank" class="underlineHover" href="<?php echo SVRURL ?>Manual/GEI-manual_utilizador.pdf" title="Manual de utilização" style="color:blue;">Manual de utilização</a>
        
<?php
}
?>


</td>
      
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

