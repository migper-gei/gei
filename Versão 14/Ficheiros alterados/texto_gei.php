

   
<!--Links de acesso rápido:-->     



   <ul>
         <b>  
         <li class="list-group-item">  
       
 
   
<form action="<?php echo SVRURL ?>avaria" method="post">

<h3 >

<button style="width:100%;" title="Inserir avaria" type="submit" class="btn btn-outline-primary" >Inserir avaria</button>
</h3>
</form>


         <li class="list-group-item">  
         <form action="<?php echo SVRURL ?>myavarias" method="post">

<h3 >

<button style="width:100%;" title="Minhas avarias" type="submit" class="btn btn-outline-primary" >Minhas avarias</button>
</h3>
</form>

<br>

<?php
if ($_SESSION['tipo']==1)
{
?>


<form action="<?php echo SVRURL ?>last5avariastot" method="post" >

<button style="width:100%;" type="submit" class="btn btn-outline-primary" title="Últimas 5 avarias registadas">Últimas 5 avarias registadas</button>
       </form >


<?php
}
?>







 </li>

         
         </b>
         <br>

         <?php
if ($_SESSION['tipo']<>4)
{
?>
         <b>  
         <li class="list-group-item"> 
         <form action="<?php echo SVRURL ?>equip" method="post">
<h3 >
<button style="width:100%;" title="Inserir requisição" type="submit" class="btn btn-outline-primary" >Inserir requisição</button>
</h3>
</form>

         
         </b>
   
         <b>  
         <li class="list-group-item">  
         <form action="<?php echo SVRURL ?>myrequi" method="post">
<h3 >
<button style="width:100%;" title="Minhas requisições" type="submit" class="btn btn-outline-primary" >Minhas requisições</button>
</h3>
</form>

</li></li>
         
         </b>

         <?php
}
?>



       </ul>
   


