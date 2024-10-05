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
      <!-- loader  -->
      <div class="loader_bg">
         <div class="loader"><img src="<?php echo SVRURL ?>images/loading.gif" alt="Loading" /></div>
      </div>
      <!-- end loader -->


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
               <a href="#" class="btn btn-secondary disabled">CONFIGURAÇÕES >> PERÍODOS >> ATUALIZAR</a>
               <div class="titlepage">
                  
                  </div>
               </div>
            </div>
            
            <div class="container">
               <div class="row">
                  <div class="col-md-8 offset-md-3">
              
                        

<?php
include("msg_bemvindo.php");
?>
    
<br>



<script>
$(function() {
    $( "#calendario" ).datepicker({
       
      changeMonth: true,
      changeYear: true,
      
      showOtherMonths: true,
        selectOtherMonths: true,
        showAnim: "slide",
        showOn: "button",
        buttonImage: "<?php echo SVRURL ?>images/calendario_datapicker.png",
        buttonImageOnly: true,
        dateFormat: 'yy-mm-dd',
        dayNames: ['Domingo','Segunda','Terça','Quarta','Quinta','Sexta','Sábado','Domingo'],
        dayNamesMin: ['D','S','T','Q','Q','S','S','D'],
        dayNamesShort: ['Dom','Seg','Ter','Qua','Qui','Sex','Sáb','Dom'],
        monthNames: ['Janeiro','Fevereiro','Março','Abril','Maio','Junho','Julho','Agosto','Setembro','Outubro','Novembro','Dezembro'],
        monthNamesShort: ['Janeiro','Fevereiro','Março','Abril','Maio','Junho','Julho','Agosto','Setembro','Outubro','Novembro','Dezembro']
    });


    $( "#calendario2" ).datepicker({
      changeMonth: true,
      changeYear: true,
      showOtherMonths: true,
        selectOtherMonths: true,
      showAnim: "slide",
        showOn: "button",
        buttonImage: "<?php echo SVRURL ?>images/calendario_datapicker.png",
        buttonImageOnly: true,
        dateFormat: 'yy-mm-dd',
        dayNames: ['Domingo','Segunda','Terça','Quarta','Quinta','Sexta','Sábado','Domingo'],
        dayNamesMin: ['D','S','T','Q','Q','S','S','D'],
        dayNamesShort: ['Dom','Seg','Ter','Qua','Qui','Sex','Sáb','Dom'],
        monthNames: ['Janeiro','Fevereiro','Março','Abril','Maio','Junho','Julho','Agosto','Setembro','Outubro','Novembro','Dezembro'],
        monthNamesShort: ['Janeiro','Fevereiro','Março','Abril','Maio','Junho','Julho','Agosto','Setembro','Outubro','Novembro','Dezembro']
        //monthNamesShort: ['Jan','Fev','Mar','Abr','Mai','Jun','Jul','Ago','Set','Out','Nov','Dez']
    });
});
</script>

<?php
if (isset($_GET['url']))
{
$url = explode('/',$_GET['url']);

//echo $url[0];
}
else
{
    ?>
<script>
window.setTimeout(function() {
    window.location.href = 'periodos';
}, 10);
</script>
<?php
}


    ?>


<?php

$sql = "select * from periodos where id=".base64_decode($url[0])."";
$result = mysqli_query($db,$sql);
$row=mysqli_fetch_array($result);


if (mysqli_num_rows($result)==0)
{

   // header("Refresh:0;url=salas");
?>


   <script>
window.setTimeout(function() {
    window.location.href = '<?php echo SVRURL ?>periodos';
}, 10);
</script>

<?php
}
else
{ 
?>




<script language="JavaScript" >
function validardatas(){

//alert("aaaa");

var di= document.getElementsByName("datai")[0].value;
var df= document.getElementsByName("dataf")[0].value;

//alert (di);
//alert (df);


var di2=new Date(di);
//alert (di2);
var df2=new Date(df);
//alert (df2);

//alert(di2- df2); 



if((di2-df2)>0 )
{
      //alert( "A data de fim deve ser igual ou superior à data de inicio." );
      swal({

title: "A data final deve ser superior à data inicial!",
//text: "Sala: "+s1+" (Escola: "+ne1+")",
type: "warning",
//showCancelButton: true,
//confirmButtonColor: "#DD6B55",


confirmButtonText: "OK",
//cancelButtonText: "Não",
closeOnConfirm: false,
closeOnCancel: false

} );
      return false;
}

else {
   //alert( "ccc");
      return true;

  }

}

</script>




<form  onSubmit="return validardatas();" action = "<?php echo SVRURL ?>atualiza_ok_periodo.php?pi=<?php echo base64_encode($row['id']);?>" method = "post" >
                    <label>Ano: </label>  <br>  
                    <input style="width:100%" class="underlineHover" placeholder="Ano"  size=10 type = "text" name="anoletivo"  required value="<?php echo $row['ano_lectivo']; ?>"/><br /><br />
                    
                    <!--pattern="[a-z0-9._%+-]+@[a-z0-9.-]+\.[a-z]{2,63}$"
                    <select name="anoletivo">
                            <option value="<php echo $row['ano_lectivo']; ?><" selected><?php echo $row['ano_lectivo']; ?></option>
                            
                        </select>
                    -->
                   
                     <label>Período: </label>  <br>  

                     <select name="periodo">
                            <?php 
                              if ($row['num_periodo']==1)
                              {
                            ?>
                            <option value="<?php echo $row['num_periodo']; ?>" selected>
                            <?php echo $row['num_periodo']; ?>º</option>
                            <option value=2>2º</option>
                            <option value=3>3º</option>
                            <?php 
                              }
                            elseif ($row['num_periodo']==2)
                            {
                                ?>
                                <option value="<?php echo $row['num_periodo']; ?>" selected>
                                <?php echo $row['num_periodo']; ?>º</option>
                                <option value=2>1º</option>
                                 <option value=3>3º</option>
                            <?php
                            }
                            elseif ($row['num_periodo']==3)
                            {
                            
                            ?>

                            <option value="<?php echo $row['num_periodo']; ?>" selected>
                            <?php echo $row['num_periodo']; ?>º</option>
                            <option value=2>1º</option>
                                 <option value=3>2º</option>
                           

                            <?php
                            }
                            ?>




                        </select>

                   <br />
                    <br>
                    <label>Data Início: </label>  <br>  
                       
                    <input value="<?php echo $row['data_inicio']; ?>"  required size="10" type = "date" name="datai" >          
                
                    <br> <br> 
                    <label>Data Fim: </label>  <br>  
                
                    <input
                   
                    value="<?php echo $row['data_fim']; ?>"  required   size="10" type = "date" name="dataf" >          
                
                    <br />
                    <div  style=" text-align:center;width:100%"> 
                    <input type = "submit" value = "Atualizar"/>     </div>
                 </form>



                 <form action = "<?php echo SVRURL ?>peri" method="post" >
<input type = "hidden" name = "" value = "">
<input title="Voltar" type=image 
src="<?php echo SVRURL ?>images/voltar.svg"  >

</form>

<?php
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