<br>
<?php
$sql = "select count(*) from logotipo";
$result = mysqli_query($db,$sql);

$count = mysqli_fetch_row($result);

?>

<table  width="100%">
  <tbody >
    <tr>
   <!--<td><a href="<?php echo SVRURL ?>validauser.php?z=2" title="Início" >
      -->
      <td width="50%" style="text-align:center">

      

     <?php





      if($count[0] == 1) 
      {
       
       $sql2 = " select * from logotipo";
       $result2 = mysqli_query($db,$sql2);
       $row2=mysqli_fetch_array($result2);
    
      if ($row2['logotipo']<>"")
      {
         ?>
              
         <?php
       echo '<img title="Logotipo" width="50%" height="50%" src="data:image/jpeg;base64,'.base64_encode($row2['logotipo']).' ">';
      ?>

          
      <?php
      }
      else echo "Sem logotipo";
      }
        
      else {?>
   
      <img title="Logotipo" src="<?php echo SVRURL ?>images/gei_icon_2.png" alt="" width="50%" height="50%" />
      
      <?php
      }
      ?>
      </td>

     


   <?php
   
   if($count[0] == 0) 
   {
   //echo "Nome da escola não definido.";
?> 
<td>
<p style="color:white;font-size: 9px;">(Logotipo não inserido)</p>
   </td>
<?php
}

   if($count[0] == 1) 
   {
   
   $sql2 = " select * from logotipo";
   $result2 = mysqli_query($db,$sql2);
   $row2=mysqli_fetch_array($result2);

   //echo str_repeat("&nbsp;", 22);
   //echo($row2['nomeescola']);
   ?> 
    
    <td  style="text-align:left">

   <h7><?php echo($row2['nomeescola']);?></h7>

<?php
   //echo str_repeat("&nbsp;", 5);
   echo('<br><br>');
   ?>
   <a title="<?php echo($row2['site']);?>" class="underlineHover" target="_blank" href="<?php echo($row2['site']);?>">

   <?php
      echo($row2['site']);?>
   </a>
      
      <?php
}
?> 

   
      </td>


    </tr>


  
    
  </tbody>
</table>
