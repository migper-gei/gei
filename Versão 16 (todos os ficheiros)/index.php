<!DOCTYPE html>
<html lang="pt">


<?php

 include ("head.php");




?>

   <head>
      


    <style>
      /*
        
        .hero-section {
            background: linear-gradient(135deg, #4b6cb7 0%, #182848 100%);
            color: white;
            padding: 4rem 0;
            margin-bottom: 2rem;
            border-radius: 0 0 50px 50px;
        }
        
        .hero-title {
            font-weight: 700;
            font-size: 2.5rem;
            margin-bottom: 1rem;
        }
        
        .features-container {
            max-width: 900px;
            margin: 0 auto;
        }
        
        .feature-item {
            display: flex;
            align-items: center;
            margin-bottom: 1.5rem;
            transition: transform 0.3s;
        }
        
        .feature-item:hover {
            transform: translateX(10px);
        }
        
        .feature-icon {
      
           background-color:rgb(80, 127, 235);
            color: white;
            width: 40px;
            height: 40px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-right: 15px;
            flex-shrink: 0;
        }
        
        .feature-text {
            font-size: 1.1rem;
        }
        
     */
        

     :root {
            --primary-color: #4b6cb7;
            --primary-dark: #182848;
            --accent-color: rgb(80, 127, 235);
            --light-bg: #f8f9fa;
            --text-dark: #333;
            --text-light: #fff;
            --box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            --border-radius: 10px;
        }
        

        
     .features-heading {
            font-weight: 700;
            color: var(--primary-dark);
            position: relative;
            display: inline-block;
            margin-bottom: 0.5rem;
        }
        
        .features-heading::after {
            content: '';
            position: absolute;
            bottom: -10px;
            left: 50%;
            transform: translateX(-50%);
            width: 80px;
            height: 4px;
           /* background-color: var(--accent-color);*/
            border-radius: 10px;
        }
        
        .features-subheading {
            font-size: 1.2rem;
            margin-bottom: 3rem;
            color: #666;
        }
        
        .features-container {
            max-width: 1000px;
            margin: 0 auto;
            padding: 0 15px;
        }
        
        .features-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
            gap: 25px;
        }
        
        .feature-item {
            background-color: white;
            border-radius: var(--border-radius);
            padding: 1.5rem;
            box-shadow: var(--box-shadow);
            transition: all 0.3s ease;
            display: flex;
            align-items: flex-start;
            height: 100%;
            border-left: 4px solid var(--accent-color);
        }
        
        .feature-item:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 15px rgba(0, 0, 0, 0.1);
        }
        
        .feature-icon {
            background-color: var(--accent-color);
            color:white;
            width: 45px;
            height: 45px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-right: 15px;
            flex-shrink: 0;
            font-size: 1.2rem;
            box-shadow: 0 3px 5px rgba(75, 108, 183, 0.3);
        }
        
        .feature-text {
            font-size: 1rem;
            line-height: 1.5;
        }
        
        
       
    </style>



   </head>


   <!-- body -->
   <body class="main-layout">
      <!-- loader  -->
      <div class="loader_bg">
         <div class="loader"><img src="<?php echo SVRURL ?>images/loading.gif" alt="Loading" /></div>
      </div>
      <!-- end loader -->


     <?php include ("header2.php");
     
     //include("sessao_timeout.php");
     ?>
     

     
      
      <!-- about -->
      <div  class="about">


      
         <div class="container">
            <div class="row">
               <div class="col-md-12">
                  <div class="titlepage">
                
                  
                  <!--
                  <div style="  text-align: right;">   
                           

                           <a href="l">
                           
                           
                           <button type="button" class="btn btn-outline-primary">Login</button>
                           </a>
                     </div>
                         
    -->


                  </div>
               </div>
            </div>
            



  <!-- <p >Descubra tudo o que a plataforma pode fazer por si</p>-->


<!-- features section -->
<!--
            <div class="row">
               
            <div class="col-md-12 text-center mb-5">
                    <h2 class="features-heading">Funcionalidades Principais</h2>
                    
                 
                </div>
            </div>-->

            <h3 style="text-align: center;">Funcionalidades</h3>
            <br>

            <div class="features-container">
                <div class="features-grid">
                    <div class="feature-item">
                        <div class="feature-icon">
                            <i class="fas fa-desktop"></i>
                        </div>
                        <div class="feature-text">
                            <strong>Equipamentos Diversos</strong>
                            <p>Gestão flexível de qualquer tipo de equipamento, não apenas informático</p>
                        </div>
                    </div>
                    
                    <div class="feature-item">
                        <div class="feature-icon">
                            <i class="fas fa-cog"></i>
                        </div>
                        <div class="feature-text">
                            <strong>Configuração Personalizada</strong>
                            <p>Defina emails, tempos de sessão e políticas de password adaptadas às suas necessidades</p>
                        </div>
                    </div>

                    <div class="feature-item">
                        <div class="feature-icon">
                            <i class="fas fa-exchange-alt"></i>
                        </div>
                        <div class="feature-text">
                            <strong>Gestão de Movimentos</strong>
                            <p>Requisição de material e transferência de equipamentos entre salas com controlo total</p>
                        </div>
                    </div>

                    <div class="feature-item">
                        <div class="feature-icon">
                            <i class="fas fa-barcode"></i>
                        </div>
                        <div class="feature-text">
                            <strong>Identificação Automática</strong>
                            <p>Criação de etiquetas e códigos de barras para rastreamento eficiente dos equipamentos</p>
                        </div>
                    </div>

                    <div class="feature-item">
                        <div class="feature-icon">
                            <i class="fas fa-building"></i>
                        </div>
                        <div class="feature-text">
                            <strong>Multi-Instituição</strong>
                            <p>Gestão centralizada do equipamento de uma ou múltiplas escolas/instituições</p>
                        </div>
                    </div>

                    <div class="feature-item">
                        <div class="feature-icon">
                            <i class="fas fa-clipboard-list"></i>
                        </div>
                        <div class="feature-text">
                            <strong>Inventário Detalhado</strong>
                            <p>Registo completo e preciso de todos os equipamentos e suas caraterísticas técnicas</p>
                        </div>
                    </div>

                    <div class="feature-item">
                        <div class="feature-icon">
                            <i class="fas fa-tools"></i>
                        </div>
                        <div class="feature-text">
                            <strong>Gestão de Manutenção</strong>
                            <p>Histórico completo de avarias, reparações e tarefas de manutenção preventiva</p>
                        </div>
                    </div>

                    <div class="feature-item">
                        <div class="feature-icon">
                            <i class="fas fa-exclamation-circle"></i>
                        </div>
                        <div class="feature-text">
                            <strong>Reporte de Avarias</strong>
                            <p>Sistema simplificado para utilizadores registarem problemas e avarias</p>
                        </div>
                    </div>

                    <div class="feature-item">
                        <div class="feature-icon">
                            <i class="fas fa-bell"></i>
                        </div>
                        <div class="feature-text">
                            <strong>Notificações Automáticas</strong>
                            <p>Alertas de reparações e atualizações de estado enviados aos utilizadores</p>
                        </div>
                    </div>

                    <div class="feature-item">
                        <div class="feature-icon">
                            <i class="fas fa-chart-bar"></i>
                        </div>
                        <div class="feature-text">
                            <strong>Análise e Estatísticas</strong>
                            <p>Relatórios detalhados e estatísticas para apoio à decisão e planeamento</p>
                        </div>
                    </div>

                    <div class="feature-item">
                        <div class="feature-icon">
                            <i class="fas fa-file-export"></i>
                        </div>
                        <div class="feature-text">
                            <strong>Transferência de Dados</strong>
                            <p>Importação e exportação de dados para diversos formatos para máxima compatibilidade</p>
                        </div>
                    </div>

                    <div class="feature-item">
                        <div class="feature-icon">
                            <i class="fas fa-comments"></i>
                        </div>
                        <div class="feature-text">
                            <strong>Comunicação Integrada</strong>
                            <p>Chat entre utilizadores para colaboração em tempo real e resolução rápida de problemas</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>


<br>

      </div>

 


      <!-- end about -->
   

      <?php include ("footer.php");?>




   </body>


</html>


