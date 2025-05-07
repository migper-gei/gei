

   
<!--Links de acesso rápido:-->     



<h2 class="section-title">
<i class="fa-solid fa-cash-register"></i>
Avarias</h2>

<div class="row">
                <div class="col-md-6 mb-3">
                    <form action="<?php echo SVRURL ?>avaria" method="post">
                        <button  title="Inserir avaria" type="submit" class="action-button btn-primary-action">
                            <i class="fa-solid fa-bug"> </i> 
                            &nbsp; Inserir avaria
                        </button>
                    </form>
                 </div>
      

                <div class="col-md-6 mb-3">
                  

                    <form action="<?php echo SVRURL ?>myavarias" method="post">

                    <button title="Minhas avarias" type="submit" class="action-button btn-secondary-action">
                            <i class="fas fa-eye btn-icon"></i> &nbsp;Minhas avarias
                        </button>

             

</form>
             
        </div>
   
        </div>








<?php
if ($_SESSION['tipo']==1)
{
?>
  

<form action="<?php echo SVRURL ?>last5avariastot" method="post" >

<button title="Últimas 5 avarias registadas" type="submit" class="action-button btn-secondary-action">
                            <i class="fas fa-eye btn-icon"></i> &nbsp;Últimas 5 avarias registadas
                        </button>

       </form >


<?php
}
?>


</div>




         <?php
if ($_SESSION['tipo']<>4)
{
?>



<div class="action-section">

<h2 class="section-title"><i class="fas fa-clipboard-list btn-icon"></i> Requisições</h2>


<div class="row">
                      
            <div class="col-md-6 mb-3">
         <form action="<?php echo SVRURL ?>equip" method="post">

         <button title="Nova requisição"  
          type="submit" class="action-button btn-primary-action">
                            <i class="fas fa-plus-circle btn-icon"></i> &nbsp;Nova Requisição
                        </button>

</form>
</div>

      
   
     


<div class="col-md-6 mb-3">
         <form action="<?php echo SVRURL ?>myrequi" method="post">

<button title="Minhas requisições" type="submit" class="action-button btn-secondary-action">
                            <i class="fas fa-eye btn-icon"></i> &nbsp;Minhas requisições
                        </button>


</form>

</div>

</div>
</div>


<?php
}
?>

