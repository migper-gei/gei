<?php
  session_start();
  session_regenerate_id();
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
      <!-- loader --> 
      <div class="loader_bg">
         <div class="loader"><img src="<?php echo SVRURL ?>images/loading.gif" alt="Loading" /></div>
      </div>
      <!-- end loader -->


     <?php include ("header.php");?>
     


     <?php
//session_start();



include("sessao_timeout.php");

 
  ?>
      
      <!-- about -->
      <div  class="about">
         <div class="container">
            <div class="row">
               <div class="col-md-12">
               <a href="#" class="btn btn-secondary disabled">AVARIAS >> INSERIR</a>
               <div class="titlepage">
     
                  </div>
               </div>
            </div>
            
            <div class="container">
               <div class="row">
                  <div class="col-md-7 offset-md-3">
              
     
<?php
include("msg_bemvindo.php");
?>
    
<br>
                  

<?php  

$said=base64_decode($_GET["ai"]);
$idesc=base64_decode($_GET["esi"]);



if ( !isset($_POST['equip']) || !isset($_POST['data']) || !isset($_POST['avaria']) 
|| empty($_POST['equip']) || empty($_POST['data']) || empty($_POST['avaria'])
|| !isset($idesc) || !isset($said)  || !is_numeric($said) || !is_numeric($idesc)
|| empty($idesc) || empty($said)
)
{
?>


<script>
window.setTimeout(function() {
    window.location.href = '<?php echo SVRURL ?>insereavaria?aves=<?php echo base64_encode ($idesc) ?>';
}, 10);
</script>


<?php
}
else
{
?>



<?php

 

    $filenamev = $_FILES["v"]["name"];
 //echo($filename);

 
 if ($filenamev=="")
 {
 
 $tmpv="";
 
 }
 
 elseif ($filenamev<>"")
 {



    $tmpnamev=$_FILES["v"]["tmp_name"];
    $filetype= $_FILES["v"]["type"];
    


    $tamanho = $_FILES["v"]["size"];
    
   
    $tmpv=addslashes(file_get_contents($tmpnamev));

 
    //echo($tmpv);

 }
    ?>
   






<?php

//echo($_GET["sa"]);
//echo($_GET["em"]);

//if(!empty($_FILES["imgavaria"]["name"])) { 
  //  $fileName = ($_FILES["imgavaria"]["name"]); 

    //}
    $x=0;


    $filename = $_FILES["imgavaria"]["name"];


    if ($filename=="")
{

$tmp="";

}

elseif ($filename<>"")
    {
    $tmpname=$_FILES["imgavaria"]["tmp_name"];
    $filetype= $_FILES["imgavaria"]["type"];
    
    $tmp=addslashes(file_get_contents($tmpname));
    //echo($tmp);

    $filepath = $_FILES['imgavaria']['tmp_name'];
    $fileSize = filesize($filepath);
    $fileinfo = finfo_open(FILEINFO_MIME_TYPE);
    $filetype = finfo_file($fileinfo, $filepath);
    
    
    
    $allowedTypes = [
     'image/png' => 'png',
    'image/jpeg' => 'jpeg',
    'image/jpg' => 'jpg',
    'image/bpm' => 'bmp',
    'image/gif' => 'gif'
    ];
    
    if (!in_array($filetype, array_keys($allowedTypes))) 
    {
    //echo("File not allowed.");
    $x=1;

    ?>
    <script>
          
    swal({
title: 'ERRO',
text: 'O ficheiro não tem conteúdo de imagem!',
icon: 'error',
//buttons: false,

})
.then(function() {
window.location = "<?php echo SVRURL ?>insereavaria";
})
;

    </script>


<?php
    }
   }
   
   
   //else $tmp="";

//echo($filename);

//echo($_POST["data"]);
if ($_SERVER["REQUEST_METHOD"] == "POST" && $x==0)
{


$dataatual=date('Y-m-d');
//echo($dataatual.'<br>');


//ver o ano letivo
$sql2 = "select max(ano_lectivo) from periodos";
$result2 = mysqli_query($db,$sql2); 
$rows2 =mysqli_fetch_row($result2);

$conta = $rows2[0];
//echo($conta);



$sql3 = "
select max(num_periodo) from periodos
where STR_TO_DATE('$dataatual','%Y-%m-%d')>= STR_TO_DATE(data_inicio,'%Y-%m-%d') and
STR_TO_DATE('$dataatual','%Y-%m-%d')<=STR_TO_DATE(data_fim,'%Y-%m-%d') 
and ano_lectivo='$conta'
";
$result3 = mysqli_query($db,$sql3); 
$rows3 =mysqli_fetch_row($result3);
$per = $rows3[0];
//echo('<br>'.$per);

//<img src="data:image/jpeg;base64,'.base64_encode($row['name'] ).'" height="200" width="200" class="img-thumnail" />


$em=$_SESSION['email'];

$sql = "insert into avarias_reparacoes (id_equi,id_sala,id_escola,autoravaria,dataavaria,avaria,imgavaria,video,ano_letivo,periodo) 
values ('".$_POST["equip"]."','".$said."',".$idesc.",'".$em."',STR_TO_DATE('".$_POST["data"]."','%Y-%m-%d'),
'".$_POST["avaria"]."','$tmp','$tmpv','$conta','$per')";

$result = mysqli_query($db,$sql);




$sql2 = "select max(id) from avarias_reparacoes";
$result2 = mysqli_query($db,$sql2);
$rows =mysqli_fetch_row($result2);
$ida = $rows[0];

//echo $ida;

mysqli_close($db);


unset($_SESSION['idesc']);

?>

<script>
    
    swal({
title: 'Os dados foram guardados!',
text: 'Um email foi enviado ao administrador/reparador com os dados da avaria!',
//text: 'Os dados foram guardados!',
icon: 'success',
//buttons: false,

})
.then(function() {
//window.location = "<?php echo SVRURL ?>avaria";
window.location = "<?php echo SVRURL ?>enviar_email_avaria.php?r=<?php echo base64_encode(0) ?>&&ia=<?php echo base64_encode($ida) ?>";
});


</script>



<?php
}

}
?>


<br><br><br><br><br><br><br><br>
                    </div>
               
               </div>
            </div>
         </div>
      </div>
      <!-- end about -->
    


      <?php include ("footer.php");?>


   </body>
</html>