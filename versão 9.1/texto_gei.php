

   
Links de acesso rápido:     
<?php
if ($_SESSION['tipo']<>1)
{
  ?>

 &nbsp;   &nbsp;   &nbsp;   &nbsp;    &nbsp;   &nbsp;           
 &nbsp;   &nbsp;   &nbsp;   &nbsp;    &nbsp;   &nbsp;       
 &nbsp;   &nbsp;   &nbsp;   &nbsp;    &nbsp;   &nbsp;  


<a  target="_blank" class="underlineHover" href="<?php echo SVRURL ?>Manual/GEI-manual_utilizador.pdf" title="Manual do utilizador" style="color:blue;">Manual do utilizador</a>
        
<?php
}
?>


   <ul>
         <b>  
         <li class="list-group-item">  
       
 
   
<form action="<?php echo SVRURL ?>avaria" method="post">

<h3 >

<button style="width:300px;" title="Inserir avaria" type="submit" class="btn btn-outline-primary" >Inserir avaria</button>
</h3>
</form>


         <li class="list-group-item">  
         <form action="<?php echo SVRURL ?>myavarias" method="post">

<h3 >

<button style="width:300px;" title="Minhas avarias" type="submit" class="btn btn-outline-primary" >Minhas avarias</button>
</h3>
</form>

<br>

<?php
if ($_SESSION['tipo']==1)
{
?>


<form action="<?php echo SVRURL ?>last5avarias" method="post" >

<button style="width:300px;" type="submit" class="btn btn-outline-primary" title="Últimas 5 avarias registadas">Últimas 5 avarias registadas</button>
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
<button style="width:300px;" title="Inserir requisição" type="submit" class="btn btn-outline-primary" >Inserir requisição</button>
</h3>
</form>

         
         </b>
   
         <b>  
         <li class="list-group-item">  
         <form action="<?php echo SVRURL ?>myrequi" method="post">
<h3 >
<button style="width:300px;" title="Minhas requisições" type="submit" class="btn btn-outline-primary" >Minhas requisições</button>
</h3>
</form>

</li></li>
         
         </b>

         <?php
}
?>



       </ul>
   


