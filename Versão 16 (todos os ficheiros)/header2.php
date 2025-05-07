
      <!-- header -->
      <header>
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
            width: 30px;
            height: 30px;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all 0.3s ease;
        }
        
        .home-button:hover {
            background-color: rgba(255,255,255,0.2);
            transform: translateY(-2px);
        }


      
        .blink-text-btn {
    padding: 10px 0;
    font-size: 16px;
    color: #f39c12;
    background: transparent;
    border: none;
    cursor: pointer;
    font-weight: bold;
    transition: all 0.1s ease;
  }
  
  .blink-text-btn:hover {
    animation: blink 0.8s infinite;
  }
  
  @keyframes blink {
    0%, 100% { opacity: 1; }
    50% { opacity: 0.5; }
  }
   </style>
      
 
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
                   
           

                




                           
                 


                      <div style="justify-content: center; margin-rigth: auto">
                        

                        <h2 style="color:white;"> 
             
                     
                        Sistema de Gestão de Equipamentos Informáticos</h2>
                        <br>
                        <h4 style="color:white;"> 
                        Uma solução completa para o inventário e manutenção dos seus equipamentos 
                       </h4>
                     </div>
                 


                    

<div style="margin-left: auto;margin-right: 0;" >




                              <?php
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
 </li>
                   -->

<?php
}
else
{
?>
<div class="col-md-1 col-sm-10 text-center mt-sm-3 mt-md-0">
<a title="Início" href="<?php echo SVRURL; ?>i" class="home-button">
                        <i class="fas fa-home"></i>
                    </a>

              
                   </div>

                
                    

                    
                   <i class="fa-solid fa-arrow-right-to-bracket fa-lg" style="color:#f39c12"></i>

                   <a title="Login" href="<?php echo SVRURL; ?>l"  >
               

                 <!--
                    <button style="color: #FFD43B;" title="Login" type="button" class="btn login-btn" >
                 
                        <i class="fa-solid fa-arrow-right-to-bracket fa-lg" style="color: #FFD43B;"></i>
     
                        Login
                    </button>
-->

    
<button class="blink-text-btn">Login</button>


                </a>

          
<!--
<li class="nav-item d_none">

 <a class="nav-link" title="Início" href="<?php echo SVRURL ?>i"> 
 <img style="width:30px; height:30px;" src="<?php echo SVRURL ?>images/home.png" alt="Início"> </a>
 </li>
-->
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