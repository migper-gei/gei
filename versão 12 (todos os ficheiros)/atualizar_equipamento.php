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

//print_r(scandir(session_save_path()));


 
  ?>


<?php
               
                 
               $id= base64_decode($_GET['ide']);
               $sa= base64_decode($_GET['sai']);
               $idescola=base64_decode($_GET['ies']);

               $sql2a = "select max(id) as me  from escolas ";
               $result2a = mysqli_query($db,$sql2a); 
               $rows2a =mysqli_fetch_row($result2a);
               
               
               $maxesc = $rows2a[0];



               if ($idescola>$maxesc || $idescola<0 
               ||  !isset($id)   || !is_numeric($id) 
               || !isset($idescola)  || empty($idescola)  || !is_numeric($idescola)  
               || !isset($sa)   ||  empty($sa)
               )
               {
               ?>
               
               <script>
               window.setTimeout(function() {
                  window.location.href = '<?php echo SVRURL ?>ver_equipamentos_sala.php?x=<?php echo base64_encode (2) ?>&&si=<?php echo base64_encode ($sa) ?>&&ies=<?php echo base64_encode ($idescola) ?>';
               }, 10);
               </script>
               
               
               <?php
               
               }






     

                $sql3 ="select e.*,s.*, es.nome_escola
                from equipamento e, salas s, escolas es
                where e.id_sala=s.id and s.id_escola=es.id
                and e.id=$id and s.id=$sa and es.id=$idescola";
                $result3 = mysqli_query($db,$sql3); 
                $row3=mysqli_fetch_array($result3);
               
                $nr = mysqli_num_rows($result3);
               ?>
      

      <?php
      //echo $nr;

     if ($nr==0 )
{
?>

<script>
window.setTimeout(function() {
    window.location.href = '<?php echo SVRURL ?>verequipsala?x=<?php echo base64_encode(1) ?>&&si=<?php echo base64_encode($sa)?>&&ies=<?php echo base64_encode($idescola)?>';
}, 10);
</script>


<?php

}

?>




      <!-- about -->
      <div  class="about">
         <div class="container">
            <div class="row">
               <div class="col-md-12">
               <a href="#" class="btn btn-secondary disabled">EQUIPAMENTOS >> ATUALIZAR</a>
               <div class="titlepage">
                     <h2>
                     <?php echo $row3['nomeequi'] ?>  | <?php echo $row3['nome'] ?> <br> <?php echo $row3['nome_escola'] ?>
                  </h2>

                    <br>
                  </div>
               </div>
            </div>
            
            <div class="container">
               <div class="row">
                  <div class="col-md-10 offset-md-1">
              
                        
<?php
include("msg_bemvindo.php");
?>
<br>


<?php
//echo $row3['escola_digital'];

if  ($row3['escola_digital']=="Sim") 
  {
  ?>        
<label>EQUIPAMENTO ESCOLA DIGITAL</label>  
                          
    
<?php
}
?>       

<form name="equipamento" action = "<?php echo SVRURL ?>atualiza_equipamento_OK.php?ide=<?php echo base64_encode($id)?>&&sai=<?php echo base64_encode($sa)?>&&ies=<?php echo base64_encode($idescola)?>" method = "post">

<?php
if  ($row3['escola_digital']=="Sim") 
  {
  ?> 
  
<input  value="<?php echo $row3['num_inv_dgest']?>" required type = "text" name = "numinv" placeholder="Nº inventário Dgest" style=" background-color:#CEF6CE;" >
                       
                       <br> 
                    
                       <input  value="<?php echo $row3['fornecedor']?>" required type = "text" name = "fornecedor" placeholder="Fornecedor" style=" background-color:#CEF6CE;" >
                                           
                       <br>  
                    
                       <input  value="<?php echo $row3['email_fornecedor']?>" required  type = "text" 
                       pattern="[a-z0-9._%+-]+@[a-z0-9.-]+\.[a-z]{2,63}$"
                       name = "emailfornecedor" placeholder="Email do fornecedor" style=" background-color:#CEF6CE;" >
                               
                       <br>
                    
                 
                    
                       <input  value="<?php echo $row3['nif_pessoa']?>" required maxlength="9" type = "text" 
                       oninput="this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\..*)\./g, '$1');" 
                       name = "nifpessoa" placeholder="NIF da pessoa" style=" background-color:#CEF6CE;" >
                               
                       <br>  
                    
                    <input type = "text" name = "rma" placeholder="Nº RMA" style=" background-color:#CEF6CE;" >
                                        
                       <br>      <br>  
   <?php
}
?> 
                    <label>Tipo de equipamento: </label> 
                 
               
                    <?php


      $sql = "SELECT DISTINCT(nome) as no FROM tipos_equipamento order by nome";
      
      $result = mysqli_query($db,$sql);
     
 
    //echo('<select name="sala">');
?>





    <select name="tipoeq" id="tipoeq" required  style=" background-color:#CEF6CE;" >
      
<?php

echo('<option value=""> </option>');  

     while($row=mysqli_fetch_array($result))
     {
           
       
        if ($row['no']==$row3['tipo'])
        {
            echo('<option selected value="'.$row['no'].'">'.$row['no'].'</option>');
        }
        else
        
         
        echo('<option value="'.$row['no'].'">'.$row['no'].'</option>');
       
        }
              
          echo('</select>');
     ?>     
             
             
             &nbsp;&nbsp;&nbsp;
              <a style="color:blue;" class="underlineHover" href="<?php echo SVRURL ?>tiposequip" title="Inserir novo tipo de equipamento">  
             Novo tipo
             </a>
      
           <br>
                <label>Sala: </label>  

                    
<select name="sala" style=" background-color:#CEF6CE; width: 290px; "  required   >
  
  <?php
   $sql2 = "select DISTINCT(s.nome) as no, s.id as said
   FROM salas s, escolas e
   where s.id_escola=e.id
   and e.id=$idescola
   order by s.nome";
   $result2 = mysqli_query($db,$sql2);
  
  //echo('<option value=""> </option>');  
  
       while($row2=mysqli_fetch_array($result2))
       {
             
        if ($row2['said']==$row3['id'])
        {
            echo('<option selected value="'.$row2['said'].'">'.$row2['no'].'</option>');
        }
        else
        
           
          echo('<option value="'.$row2['said'].'">'.$row2['no'].'</option>');

       }
                
            echo('</select>');
       ?>  



            

                                              
<br>

<input required type = "text" name = "nomeq" value="<?php echo $row3['nomeequi']?>" style="width:90%; background-color:#CEF6CE;" >
                    
              
<br>


     
                     <input  style="width:90% " type = "text" name = "nserie" placeholder="Nº de série">  
                     <br>  
          
                     <input   style="width:90% " type = "text" name = "marcamod" placeholder="Marca/Modelo"> 

                     <br />   

                     <br />   
                     <div  style=" text-align:center;width:90%">
                     <label>Data da compra:</label>     
                     <input type = "date" size="10"   type = "date" value="<?php echo $row3['data_compra']?>" name = "datacompra" placeholder="Data da compra"> 

                     <br />  
                 
                  <br>
                
                   <textarea style="width:100%;display: block;    margin-left: auto;    margin-right: auto;"
                    type = "text" rows="5"   name="obs" placeholder="Observações" ><?php echo $row3['observacoes']?></textarea>
                   <br>
      </div>





                     <div  style=" text-align:center;width:90%"> <input  type = "submit" value = "Atualizar"/>     </div>
  
                 </form>

<form action = "<?php echo SVRURL ?>ver_equipamentos_sala.php?x=<?php echo base64_encode (2) ?>&&si=<?php echo base64_encode ($sa) ?>&&ies=<?php echo base64_encode ($idescola) ?>" method="post" >
<input type = "hidden" name = "sala" value = "">
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