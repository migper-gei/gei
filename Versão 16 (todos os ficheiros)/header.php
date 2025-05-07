<style>
     .nav-button {
            background-color: white;
            color: var(--primary-color);
            border: none;
            border-radius: 6px;
            padding: 10px 20px;
            font-weight: 500;
            transition: all 0.3s ease;
            
            width: 130px;
            text-align: center;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        }
        .nav-button:hover {
            background-color: #f8f9fa;
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
        }

        .home-button {
            background-color: transparent;
            border: 2px solid white;
            color: white;
            border-radius: 5px;
            width: 40px;
            height: 40px;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all 0.3s ease;
        }
        
        .home-button:hover {
            background-color: rgba(255,255,255,0.2);
            transform: translateY(-2px);
        }

        
   </style>

<?php  
        
//session_start();

include ("config.php");



//session_regenerate_id();






//echo session_id();
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
                            
                            <!-- <button class="btn btn-light"  style="width:130px;" title="Equipamentos" type="submit" >
                                Equipamentos</button>
                              -->
                                <button  class="nav-button" title="Equipamentos" type="submit">
                                 Equipamentos</button>


                           </form>


                            
                            
                              </li>

                              &nbsp;    &nbsp;
                             <li class="nav-item">
                             <form action="<?php echo SVRURL ?>avaria" method="post">
                             <button  class="nav-button" title="Avarias" type="submit" >
                                Avarias</button>
                           </form>
                                                   
                              </li>
                             <li class="nav-item">
                             
                             &nbsp;    &nbsp;
                             <li class="nav-item">
                             <form action="<?php echo SVRURL ?>lista" method="post">
                             <button  class="nav-button"  title="Listagens" type="submit" >
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
                             <button  class="nav-button"  title="Manutenções" type="submit" >
                             Manutenções</button>
                           </form>
                             <!--
                             <a class="nav-link" title="Manutenções" href="<?php echo SVRURL ?>manut">MANUTENÇÕES</a>
-->  
                           </li>
                           &nbsp;    &nbsp;
                             <li class="nav-item">
                             <form action="<?php echo SVRURL ?>configura" method="post">
                             <button  class="nav-button"  title="Configurações" type="submit" >
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

<div class="col-md-1 col-sm-12 text-center mt-sm-3 mt-md-0">
<a title="Início" href="<?php echo SVRURL; ?>acessorap" class="home-button">
                        <i class="fas fa-home"></i>
                    </a>
                   </div>
<!--
<li class="nav-item d_none">
 <a class="nav-link" title="Início" href="<?php echo SVRURL ?>acessorap"> 
 <img style="width:25px; height:25px;" src="<?php echo SVRURL ?>images/home.png" alt="Início"> </a>
                   
</li>--> 


<?php
}
else
{
?>


<div class="col-md-1 col-sm-12 text-center mt-sm-3 mt-md-0">
<a title="Início" href="<?php echo SVRURL; ?>i" class="home-button">
                        <i class="fas fa-home"></i>
                    </a>
                   </div>

<!--
<li class="nav-item d_none">
 <a class="nav-link" title="Início" href="<?php echo SVRURL ?>i"> 
 <img style="width:30px; height:30px;" src="<?php echo SVRURL ?>images/home.png" alt="Início"> </a>
 </li>
-->          <?php
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