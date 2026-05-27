<style>
        /* ── Compactar altura do header ── */
        .header { min-height: 0 !important; padding-top: 8px !important; padding-bottom: 0 !important; margin-bottom: -8px !important; }
        .header .row { min-height: 0 !important; }
        .navigation.navbar { padding-top: 12px !important; padding-bottom: 0 !important; }
        .navigation.navbar .nav-item:not(:last-child) { margin-top: 6px !important; }
        .gei-theme-toggle-nav { position: relative; top: -12px !important; }
        .navbar-nav { flex-wrap: nowrap; }
        .logo_section { padding-top: 6px !important; padding-bottom: 2px !important; }

    /* ── Home button moderno ── */
        .home-button {
            background-color: rgba(255,255,255,0.2) !important;
            border: none !important;
            border-radius: 14px !important;
            width: 44px !important;
            height: 44px !important;
            display: inline-flex !important;
            align-items: center !important;
            justify-content: center !important;
            transition: all 0.3s ease !important;
            backdrop-filter: blur(6px) !important;
        }
        .home-button:hover {
            background-color: rgba(255,255,255,0.35) !important;
            transform: translateY(-1px) !important;
        }
        .home-button i {
            font-size: 20px !important;
            color: #fff !important;
        }

        /* ── Toggle moderno ── */
        .gei-theme-toggle-nav {
            background: rgba(255,255,255,0.2) !important;
            border: none !important;
            border-radius: 20px !important;
            padding: 8px 12px !important;
            gap: 8px !important;
            backdrop-filter: blur(6px) !important;
        }
        .gei-toggle-track-nav {
            width: 42px !important;
            height: 24px !important;
            border-radius: 12px !important;
            background: rgba(255,255,255,0.35) !important;
        }
        [data-theme="dark"] .gei-toggle-track-nav { background: #4e73df !important; }
        .gei-toggle-knob-nav {
            width: 18px !important;
            height: 18px !important;
            top: 3px !important;
            left: 3px !important;
        }
        [data-theme="dark"] .gei-toggle-knob-nav { transform: translateX(18px) !important; }
        .gei-toggle-icon-nav { font-size: 18px !important; }
        .gei-toggle-label { display: none !important; }

     .nav-button {
            background-color: rgba(255,255,255,0.2);
            color: #fff;
            border: none;
            border-radius: 14px;
            padding: 5px 20px;
            font-weight: 600;
            font-size: 14px;
            transition: all 0.3s ease;
            width: 130px;
            text-align: center;
            backdrop-filter: blur(6px);
            text-shadow: 0 1px 3px rgba(0,0,0,0.15);
        }
        .nav-button:hover {
            background-color: rgba(255,255,255,0.35);
            transform: translateY(-1px);
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

    /* Toggle Tema — navbar */
    .gei-theme-toggle-nav {
        display: inline-flex; align-items: center; gap: 6px;
        cursor: pointer; background: none; border: none;
        padding: 2px 6px; font-size: 11px; font-weight: 600;
        color: rgba(255,255,255,0.8); white-space: nowrap;
        transition: color .2s;
    }
    .gei-theme-toggle-nav:hover { color: #fff; }
    .gei-toggle-track-nav {
        position: relative; width: 34px; height: 18px;
        background: rgba(255,255,255,0.25); border-radius: 9px;
        flex-shrink: 0; transition: background .3s ease;
    }
    [data-theme="dark"] .gei-toggle-track-nav { background: #4e73df; }
    .gei-toggle-knob-nav {
        position: absolute; top: 2px; left: 2px;
        width: 14px; height: 14px; background: #fff; border-radius: 50%;
        box-shadow: 0 1px 4px rgba(0,0,0,.3);
        transition: transform .3s cubic-bezier(.34,1.56,.64,1);
    }
    [data-theme="dark"] .gei-toggle-knob-nav { transform: translateX(16px); }
    .gei-toggle-icon-nav {
        font-size: 14px; line-height: 1;
        transition: transform .4s ease;
    }
    [data-theme="dark"] .gei-toggle-icon-nav { transform: rotate(180deg); }
   </style>

<?php  
        
//session_start();

include ("config.php");



//session_regenerate_id();


//include ("svrurl.php");
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
                        <button class="navbar-toggler" type="button" aria-controls="navbarsExample04" aria-expanded="false" aria-label="Toggle navigation" id="gei-nav-toggler">
                        <span class="navbar-toggler-icon"></span>
                        </button>

                        <div class="navbar-collapse" id="navbarsExample04" style="display:none;">

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

                          <!-- ═══ Toggle Tema Escuro ═══ -->
                          &nbsp;&nbsp;
                          <li class="nav-item" style="display:flex;align-items:center;">
                            <button class="gei-theme-toggle-nav gei-theme-toggle" type="button"
                                    aria-pressed="false" title="Ativar tema escuro">
                              <span class="gei-toggle-icon gei-toggle-icon-nav">🌙</span>
                              <span class="gei-toggle-track-nav">
                                <span class="gei-toggle-knob-nav"></span>
                              </span>
                              <span class="gei-toggle-label">Escuro</span>
                            </button>
                          </li>
                

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
<script>
(function() {
    function initNavToggler() {
        var btn = document.getElementById('gei-nav-toggler');
        var nav = document.getElementById('navbarsExample04');
        if (!btn || !nav) return;
        btn.addEventListener('click', function () {
            var isOpen = nav.style.display === 'block';
            nav.style.display = isOpen ? 'none' : 'block';
            btn.setAttribute('aria-expanded', String(!isOpen));
        });
        // Fechar ao clicar fora
        document.addEventListener('click', function (e) {
            if (!btn.contains(e.target) && !nav.contains(e.target)) {
                nav.style.display = 'none';
                btn.setAttribute('aria-expanded', 'false');
            }
        });
    }
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initNavToggler);
    } else {
        initNavToggler();
    }
})();
</script>
<!-- ═══ TEMA ESCURO — centralizado no header ═══ -->
<script src="<?php echo SVRURL ?>js/dark-theme.js"></script>
<!-- ═════════════════════════════════════════════ -->
