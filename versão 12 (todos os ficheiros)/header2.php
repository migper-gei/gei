
      <!-- header -->
      <header>

      
 
        <!-- header inner --> 
        <div class="header"> 
    
           <div class="container-fluid">
              <div class="row">

            
                 <div class="col-xl-3 col-lg-3 col-md-3 col-sm-3 col logo_section">
                    <div class="full">
                    
                    
                       


                       <div class="center-desk">
                        
                           <div class="logo">
                              
                           <?php
                         /*
                           if (!isset($_SESSION['nobd']))
                           {
                                ?>

                              <img src="images/gei_icon_2.png"  width="20%" height="20%">
                               
                               <?php
                           }
                        */
                           ?>
                              <!--
                            
                             
                              <br>
                              <div  >
                              <h4 style="color:white;font-size:14px"> <b>GEI</b> <br>
                              <b>G</b>estão do <b>E</b>quipamento <b>I</b>nformático</h4>
                              </div>
                               -->
                           </div>
                       </div>
 
                     </div>    
                     
                  
              
                  </div>    
                   
           

                




                           
                           <nav class="navigation navbar navbar-expand-md navbar-dark ">

                           
                   


                      <div style="justify-content: center; margin-rigth: auto">
                        

                        <h2 style="color:white;"> 
                        <b>G</b>estão do <b>E</b>quipamento <b>I</b>nformático</h2>    
                     </div>
                 

                        
               
                   
              
                         
                           <!--
                           <li class="nav-item">
                             <form action="<?php echo SVRURL ?>equip" method="post">
                             <button class="btn btn-light"  style="width:130px;" title="Equipamentos" type="submit" >
                                Equipamentos</button>
                           </form>


                            
                              </li>

                              &nbsp;    &nbsp;
                             <li class="nav-item">
                             <form action="<?php echo SVRURL ?>avaria" method="post">
                             <button class="btn btn-light" style="width:130px;" title="Avarias" type="submit" >
                                Avarias</button>
                           </form>
                        
                             
                              </li>
                             <li class="nav-item">
                             
                             &nbsp;    &nbsp;
                             <li class="nav-item">
                             <form action="<?php echo SVRURL ?>lista" method="post">
                             <button class="btn btn-light" style="width:130px;" title="Listagens" type="submit" >
                              Listagens</button>
                           </form>
                             
                           
                             
                              </li>
                             <li class="nav-item">
                                
                             &nbsp;    &nbsp;
                             <li class="nav-item">
                             <form action="<?php echo SVRURL ?>manut" method="post">
                             <button class="btn btn-light" style="width:130px;" title="Manutenções" type="submit" >
                             Manutenções</button>
                           </form>

                           </li>
                           &nbsp;    &nbsp;
                             <li class="nav-item">
                             <form action="<?php echo SVRURL ?>configura" method="post">
                             <button class="btn btn-light"  style="width:130px;" title="Configurações" type="submit" >
                             Configurações</button>
                           </form>
-->


                    
</nav>
<div style="margin-left: auto;margin-right: 0;" >

                              <?php
                              if(isset($_SESSION['login_user'])) 
                   {   
                  ?>  




               

<li class="nav-item d_none">
 <a class="nav-link" title="Início" href="<?php echo SVRURL ?>acessorap"> 
 <img style="width:25px; height:25px;" src="<?php echo SVRURL ?>images/home.png" alt="Início"> </a>
 </li>


<?php
}
else
{
?>



<li class="nav-item d_none">

 <a class="nav-link" title="Início" href="<?php echo SVRURL ?>i"> 
 <img style="width:30px; height:30px;" src="<?php echo SVRURL ?>images/home.png" alt="Início"> </a>
 </li>
              <?php
                   }
              ?>     

                       
                    
              
              
                 
</div>


              </div>
           </div>
        </div>
       
     </header>
     <!-- end header inner -->
     <!-- end header -->