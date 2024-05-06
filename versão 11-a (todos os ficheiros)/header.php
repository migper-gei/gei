
<?php  
session_start();
include ("config.php");

//echo session_id();

session_regenerate_id();

//echo ('<br><br>');

//echo session_id();


//include ("svrurl.php");




//echo($count[0]);
?>
	


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
                          <!--   <img src="images/logo.png"  width="20%" height="20%">
                             (Logo e nome da escola não definidos.)
                          -->
                           
                          <?php
                         include("tabela_logo.php");
                          ?>


                           </div>
                       </div>
 
                     </div>    
                  </div>    
                   
           

                


                  <div class="col-xl-9 col-lg-9 col-md-9 col-sm-9">
                     <nav class="navigation navbar navbar-expand-md navbar-dark ">
                        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarsExample04" aria-controls="navbarsExample04" aria-expanded="false" aria-label="Toggle navigation">
                        <span class="navbar-toggler-icon"></span>
                        </button>
                        <div class="collapse navbar-collapse" id="navbarsExample04">
                           <ul class="navbar-nav mr-auto">
                           
                             <li class="nav-item">
                             <form action="<?php echo SVRURL ?>equip" method="post">
                             <button class="btn btn-light"  style="width:140px;" title="Equipamentos" type="submit" >
                                Equipamentos</button>
                           </form>


                                <!-- <a class="nav-link" title="Equipamentos" href="<?php echo SVRURL ?>equip">EQUIPAMENTOS  </a>-->
                            
                            
                              </li>

                              &nbsp;    &nbsp;
                             <li class="nav-item">
                             <form action="<?php echo SVRURL ?>avaria" method="post">
                             <button class="btn btn-light" style="width:140px;" title="Avarias" type="submit" >
                                Avarias</button>
                           </form>
                           <!--
                                <a class="nav-link" title="Avarias/Reparações" href="<?php echo SVRURL ?>avaria">AVARIAS/REPARAÇÕES</a>
class="btn btn-primary"
                              -->
                             
                              </li>
                             <li class="nav-item">
                             
                             &nbsp;    &nbsp;
                             <li class="nav-item">
                             <form action="<?php echo SVRURL ?>lista" method="post">
                             <button class="btn btn-light" style="width:140px;" title="Listagens" type="submit" >
                              Listagens</button>
                           </form>
                             
                           <!--style="width:135px; height:30px;"
                             <a class="nav-link" title="Listagens" href="<?php echo SVRURL ?>lista">LISTAGENS</a>
-->
                             
                             
                              </li>
                             <li class="nav-item">
                                
                             &nbsp;    &nbsp;
                             <li class="nav-item">
                             <form action="<?php echo SVRURL ?>manut" method="post">
                             <button class="btn btn-light" style="width:140px;" title="Manutenções" type="submit" >
                             Manutenções</button>
                           </form>
                             <!--
                             <a class="nav-link" title="Manutenções" href="<?php echo SVRURL ?>manut">MANUTENÇÕES</a>
-->  
                           </li>
                           &nbsp;    &nbsp;
                             <li class="nav-item">
                             <form action="<?php echo SVRURL ?>configura" method="post">
                             <button class="btn btn-light"  style="width:140px;" title="Configurações" type="submit" >
                             Configurações</button>
                           </form>
                           <!--
                                <a class="nav-link" title="Configurações" href="<?php echo SVRURL ?>configura">CONFIGURAÇÕES</a>
-->
                              </li>

                          
                    

                              &nbsp;     &nbsp;    &nbsp;



               <?php
           
               //echo $_SESSION['tipo'];
               if(isset($_SESSION['login_user'])) 
                   {   
                  ?>  

<li class="nav-item d_none">
 <a class="nav-link" title="Início" href="<?php echo SVRURL ?>acessorap"> 
 <img style="width:25px; height:25px;" src="<?php echo SVRURL ?>images/home5.png" alt="Início"> </a>
 </li>


<?php
}
else
{
?>
<!--
<li class="nav-item d_none">
 <a class="nav-link" title="Login/Registo" href="<?php echo SVRURL ?>l">
 <i class="fa fa-user-circle padd_right" aria-hidden="true"></i></a>
</li>
-->

<li class="nav-item d_none">
 <a class="nav-link" title="Início" href="<?php echo SVRURL ?>i"> 
 <img style="width:30px; height:30px;" src="<?php echo SVRURL ?>images/home.png" alt="Início"> </a>
 </li>
              <?php
                   }
              ?>       

                          </ul>
                       </div>
                    </nav>
                 </div>
                 


                 
            
              </div>
           </div>
        </div>
       
     </header>
     <!-- end header inner -->
     <!-- end header -->