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

//print_r(scandir(session_save_path()));


 
  ?>
      
      <!-- about -->
      <div  class="about">
         <div class="container">
            <div class="row">
               <div class="col-md-12">
               <div class="titlepage">
                     <h2>Inserir Períodos / Semestres</h2>
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

<script language="javascript">
	function clickMe()
	{
		var datai = (document.forms[0]["datai"].value);
    var dataf = (document.forms[0]["dataf"].value);
	
 //alert(datai);

    //var myDate = new Date("2013/1/16");
    //var date8 =  new Date(Date.parse("2020-09-08"));
    var di=new Date(Date.parse(datai));
    var df=new Date(Date.parse(dataf));
   //alert(date8);
    
  
//alert(compara1);

		if ( df <= di )
				{
			alert(" A data final deve ser superior à data inicial!");
      return false;
		}
		
    else {
      return true;

  }
	}
</script>



<form action = "<?php echo SVRURL ?>grava_periodo.php" method = "post" >
                <label>Ano letivo: </label>  <br>  
                <input style="background-color:#CEF6CE" placeholder="aaaa/aaaa" pattern="[0-9]+/[0-9]{4,4}" size=10 type = "text" name = "anoletivo"  required /><br /><br />
                    
            
               
                     <label>Período/Semestre: </label>  
                   
                        <select name="periodo" style="background-color:#CEF6CE">
                            <option value=1>1º</option>
                            <option value=2>2º</option>
                            <option value=3>3º</option>
                           
                        </select>
                    <br>     <br>
                    <label>Data Início: </label> 
                    <!--
                    <input readonly style="text-align: center" size=10 type = "text" name = "datai" id="calendario"  required/>
                    -->
                    <input  required size="10" type = "date" name = "datai" style="background-color:#CEF6CE"> 
                  <br>     <br>
                  
                   

                    <label>Data Fim: </label>   
                    <input  required size="10" type = "date" name = "dataf" style="background-color:#CEF6CE"> 
                         
			<br><br>
                    <div  style=" text-align:center;width:90%"> <input  onclick="return clickMe();" type = "submit" value = "Inserir"/>   
    </div>
                         
                 
                                       
                 </form>

                 <form action = "<?php echo SVRURL ?>periodos" method="post" >
<input type = "hidden"  value = "<?php echo $sa?>">
<input title="Voltar" type=image 
src="<?php echo SVRURL ?>images/voltar.svg"  >

</form>
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