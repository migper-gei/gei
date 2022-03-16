
<?php  
session_start();
include ("config.php");

//include ("svrurl.php");

$sql = "select count(*) from logotipo";
$result = mysqli_query($db,$sql);

$count = mysqli_fetch_array($result);

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
                                <a class="nav-link" title="Equipamentos" href="<?php echo SVRURL ?>equip">Equipamentos  </a>
                             </li>
                             <li class="nav-item">
                                <a class="nav-link" title="Avarias/Reparações" href="<?php echo SVRURL ?>avaria">Avarias/Reparações</a>
                             </li>
                             <li class="nav-item">
                                <a class="nav-link" title="Listagens" href="<?php echo SVRURL ?>lista">Listagens</a>
                             </li>
                             <li class="nav-item">
                                <a class="nav-link" title="Manutenções" href="<?php echo SVRURL ?>manut">Manutenções</a>
                             </li>
                             <li class="nav-item">
                                <a class="nav-link" title="Configurações" href="<?php echo SVRURL ?>configura">Configurações</a>
                             </li>

                          
                            


<li class="nav-item d_none">
 <a class="nav-link" title="Login/Registo" href="<?php echo SVRURL ?>l">
 <i class="fa fa-user-circle padd_right" aria-hidden="true"></i></a>
</li>

<li class="nav-item d_none">
 <a class="nav-link" title="Início" href="<?php echo SVRURL ?>i"> <img src="<?php echo SVRURL ?>images/home.png" alt="Início"> </a>
 </li>
                     
                
                 
                             <!--
                             <li class="nav-item d_none">
                                <a class="nav-link" href="#"><i class="fa fa-search" aria-hidden="true"></i></a>
                             </li>-->
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