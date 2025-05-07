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


include ("css_inserir.php");

include("sessao_timeout.php");

//include("verifica_sessao.php");

//print_r(scandir(session_save_path()));


 
  ?>
      
      <!-- about -->
      <div  class="about">
         <div class="container">
            <div class="row">
               <div class="col-md-12">
               <a href="#" class="btn btn-secondary disabled">Configurações >> Períodos >> Inserir</a>
               <div class="titlepage">
                  
                  </div>
               </div>
            </div>
            
            <div class="container">
               <div class="row">
                  <div class="col-md-10 offset-md-2">
              
                
                  <div class="welcome-section">   
<?php
include("msg_bemvindo.php");
?>
    </div>



<script language="javascript">
	function clickMe()
	{
		var datai = (document.forms.per.elements.datai.value);
    var dataf = (document.forms.per.elements.dataf.value);
	
 //alert(datai);

    //var myDate = new Date("2013/1/16");
    //var date8 =  new Date(Date.parse("2020-09-08"));
    var di=new Date(Date.parse(datai));
    var df=new Date(Date.parse(dataf));
   //alert(date8);
    
  
//alert(compara1);

		if ( df <= di )
				{
			//alert(" A data final deve ser superior à data inicial!");
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
      return true;

  }
	}




</script>







<?php
$token=md5(uniqid(rand(), TRUE));
$_SESSION['token']=$token;
?>




<div class="form-container">

                        
       <div class="step-indicator">




                        

<i class="bi bi-info-circle-fill me-2"></i>
Complete todos os campos obrigatórios (indicados com fundo azul claro)
</div>


<form name="per" action = "<?php echo SVRURL ?>gravaper" method = "post" 
class="needs-validation" novalidate>




                <label>Ano: </label>  
  
                <input style="width:100%" required type="text" name="anoletivo" id="anoletivo" class="form-control required-field" placeholder="Ano">
    

                <input type="hidden" name="token" value="<?php echo $token; ?>" >
                
                <!--
                <input style="background-color:#CEF6CE;width:100%" placeholder="Ano" size=10 type = "text" name = "anoletivo"  required /><br /><br />
--> 

<br><BR>

               
                     <label>Período: </label>  <br>
                  
                        <select name="periodo" style="width: 100%;
            height: 35px; 
                       "
                       class="form-control required-field" required
                        >
                        <option value=""> -- Selecione -- </option>
                            <option value=1>1º</option>
                            <option value=2>2º</option>
                            <option value=3>3º</option>
                           
                        </select>

                   


                    <br>     <br>
                    <label>Data Início: </label> 
                    <!--
                    <input readonly style="text-align: center" size=10 type = "text" name = "datai" id="calendario"  required/>
                    -->
                    <input  required  type = "date" name="datai"  class="form-control required-field"> 
                  <br>     
                  
                   

                    <label>Data Fim: </label>   
                    <input  required size="10" type = "date" name="dataf" class="form-control required-field"> 
                         
			<br>
         <div class="text-center mt-4">
                                    <button type="submit" class="btn-submit">
                                        <i class="bi bi-check-circle-fill me-2"></i>
                                        &nbsp;Inserir período
                                    </button>
                              
                           
                        </div>   
                 
                                       
                 </form>



                 </div>
               
      


<div class="text-center mt-3">
                            <a class="btn btn-secondary" title="Voltar" href="<?php echo SVRURL ?>peri">
                                <i class="bi bi-arrow-left"></i> Voltar
                            </a>
                            <br>  <br>
                        </div>





                    </div>
               
               </div>
            </div>
         </div>
      </div>
      <!-- end about -->
    


      <?php include ("footer.php");?>
    <!-- Script para validação do formulário -->
    <script>
        // Example starter JavaScript for disabling form submissions if there are invalid fields
        (function() {
            'use strict';
            window.addEventListener('load', function() {
                // Fetch all the forms we want to apply custom Bootstrap validation styles to
                var forms = document.getElementsByClassName('needs-validation');
                // Loop over them and prevent submission
                var validation = Array.prototype.filter.call(forms, function(form) {
                    form.addEventListener('submit', function(event) {
                        if (form.checkValidity() === false) {
                            event.preventDefault();
                            event.stopPropagation();
                        }
                        form.classList.add('was-validated');
                    }, false);
                });
            }, false);
        })();
    </script>

   </body>
</html>