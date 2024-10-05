<style>
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                body {font-family: Arial, Helvetica, sans-serif;}

/* The Modal (background) */
.modal {
  display: none; /* Hidden by default */
  position: fixed; /* Stay in place */
  z-index: 1; /* Sit on top */
  padding-top: 100px; /* Location of the box */
  left: 0;
  top: 0;
  width: 100%; /* Full width */
  height: 100%; /* Full height */
  overflow: auto; /* Enable scroll if needed */
  background-color: #000; /*rgb(0,0,0);  Fallback color */
  background-color: rgba(0,0,0,0.4); /* Black w/ opacity */
}

/* Modal Content */
.modal-content {
  background-color: #fefefe;
  margin: auto;
  padding: 20px;
  border: 1px solid #888;
  width: 80%;
  height: 90%;
  overflow: scroll;
}

/* The Close Button */
.close {
  color: #aaa;
  float: right;
  font-size: 28px;
  font-weight: bold;
}

.close:hover,
.close:focus {
  color: #000;
  text-decoration: none;
  cursor: pointer;
}
</style>



      <!--  footer -->

      <div class="footer">
                      
                <div class="col-md-12">
                   <p style= "color:white;font-size:10px">Copyright 2021-2024 | <a   href="#" id="myBtn">Versão 12</a>
                   | gei@miguelarpereira.pt
                  </p>
                </div>
    </div>

    
<!-- The Modal -->
<div id="myModal" class="modal">

<!-- Modal content -->
<div class="modal-content">
  

<span class="close">&times;</span>
<p><b>Versão 12 (outubro 2024):</b></p>
<p>  - optimizações e correções gerais</p>

 
  
  <br>
<p><b>Versão 11 (outubro 2023):</b></p>
  <p>- adaptação da aplicação para a utilização de outras instituições que não escolas.</p>
  <p>- optimizações e correções gerais</p>
 
  
  <br>
  <p><b>Versão 10 (junho 2023):</b></p>
  <p>- possibilidade de incluir outro equipamento que não informático.</p>
  <p>- adição de nova tabela: outro_equipamento (ficheiro: outro_equipamento.sql).</p>
 
 
  
  <br>

<span class="close">&times;</span>
  <p><b>Versão 9.7 (maio 2023):</b></p>
  <p>- possibilidade de exportar os dados para formato CSV (utilizadores, tipos de equipamento, salas, equipamentos, tipos de manutenção, tarefas a realizar) >> (separador configurações).</p>
 
 
  
  <br>
<span class="close">&times;</span>
  <p><b>Versão 9.6 (março 2023):</b></p>
  <p>- suporte aos equipamentos da escola digital (separador equipamentos e separador avarias).</p>
  <p>- para os equipamento da escola digital envio de email ao fornecedor com a descrição da avaria.</p>
  <p>- adição de novos campos na tabela "equipamento" e tabela "escolas"</p>
 
  
  <br>

  <p><b>Versão 9.5 (fevereiro 2023):</b></p>
  <p>- configuração das definições do email para envio de alertas (separador configurações).</p>
  <p>- configuração do tempo de sessão ativa do utilizador (separador configurações).</p>
  <p>- adição da tabela "settings"</p>
 
  
  <br>
  <p><b>Versão 9.4 (janeiro 2023):</b></p>
  <p>- após a introdução de uma avaria, os administradores/reparadores recebem email de aviso.</p>
  <p>- possibilidade do administrador enviar email (com dados de uma dada avaria) para um reparador à escolha.</p>
  <p>- optimizações e correções gerais</p>

  
  <br>

  <p><b>Versão 9.3 (novembro 2022):</b></p>
  <p>- optimizações e correções gerais</p>

  <br>

  <p><b>Versão 9.2 (outubro 2022):</b></p>
  <p>- adição da tabela "tipos manutenção"</p>
  <p>- separador configurações: tipos de manutenção</p>
  <p>- melhoramento do separador manutenções: inserir manutenção da sala</p>
  <p>- melhoramento do separador manutenções: sala entre datas</p>

  <br>
  <p><b>Versão 9.1 (julho 2022):</b></p>
  <p>- adição do campo "observações" na tabela equipamento</p>
  <p>- optimizações e correções gerais</p>

  <br>
  <p><b>Versão 9.0 (junho 2022):</b></p>
  <p>- possibilidade de requisição de material (separador equipamentos)</p>
  <p>- possibilidade de um equipamento mudar de sala</p>
  <p>- alteração da tabela "salas" (adição de um novo campo)</p>
  <p>- adição de novas tabelas "requisicao" e "equip_requisitado"</p>
  <p>- novo tipo de utilizadores: funcionário</p>
  <p>- melhoramento do aspeto gráfico</p>
  <p>- optimizações e correções gerais</p>


  <br>
  <p><b>Versão 8.0 (maio 2022):</b></p>
  <p>- visualização das últimas 5 avarias registadas (separador avarias/reparações)</p>
  <p>- optimizações e correções gerais</p>


  <br>
  
  <p><b>Versão 7.0 (abril 2022):</b></p>
  <p>- criação de etiquetas / código de barras dos equipamentos (separador equipamento)</p>
  <p>- adição de novas listagens: quantidade por sala do tipo e nº de avarias por tipo equipamento (separador listagens)</p>
  <p>- optimizações e correções gerais</p>
 

  <br>
  <p><b>Versão 6.0 (março 2022):</b></p>
  <p>- separador configurações: adição da opção tarefas a realizar</p>
  <p>- adição da tabela "tarefas" na base de dados</p>
  <p>- melhoramento do separador manutenção</p>
  
  
 
  <br>
  <p><b>Versão 5.0 (março 2022):</b></p>
  <p>- possibilidade de suportar várias escolas do agrupamento</p>
  <p>- optimização e restruturação da base de dados</p>
  <p>- melhoramento em todos os separadores</p>
 
  <br>
  <p><b>Versão 4.0 (fevereiro 2022):</b></p>
  <p>- melhoramento da inserção de avarias</p>
  <p>- melhoramento do separador utilizadores (novo tipo: reparador)</p>
  <p>- indicação se o utilizador tem ou não mensagens no chat</p>
  <br>
  <p><b>Versão 3.0 (dezembro 2021):</b></p>
  <p>- melhoramento da listagem de utilizadores</p>
  <p>- separador configurações: importação de tipos de equipamento</p>
  <p>- separador avarias/reparações (inserir avaria): possibilidade de inserir um vídeo da avaria</p>
  <p> - adição do campo vídeo na tabela "avaria_reparação" </p>

<br>
  <p><b>Versão 2.0 (julho 2021):</b></p>
  <p>- interface renovado</p>
    <p>- site responsivo (ajustável para todos os ecrãs)</p>
    <p>- melhoramento do separador "equipamentos"</p>
    <p>- melhoramento do separador "avarias/reparações"</p>
    <p>- melhoramento do separador "listagens"</p>
    <p>- mais opções de importação no separador "configurações"</p>
    <p>- inclusão de um chat</p>
    <p>- correção de erros</p>
</div>

</div>



<script>
// Get the modal
var modal = document.getElementById("myModal");

// Get the button that opens the modal
var btn = document.getElementById("myBtn");

// Get the <span> element that closes the modal
var span = document.getElementsByClassName("close")[0];

// When the user clicks the button, open the modal 
btn.onclick = function() {
  modal.style.display = "block";
}

// When the user clicks on <span> (x), close the modal
span.onclick = function() {
  modal.style.display = "none";
}

// When the user clicks anywhere outside of the modal, close it
window.onclick = function(event) {
  if (event.target == modal) {
    modal.style.display = "none";
  }
}
</script>

 <!-- end footer -->



 <!-- Javascript files
 <script src="<?php echo SVRURL ?>js/jquery.min.js"></script>
-->

 <script src="<?php echo SVRURL ?>js/popper.min.js"></script>
 <script src="<?php echo SVRURL ?>js/bootstrap.bundle.min.js"></script>
 <script src="<?php echo SVRURL ?>js/jquery-3.0.0.min.js"></script>
 <!-- sidebar -->
 <script src="<?php echo SVRURL ?>js/jquery.mCustomScrollbar.concat.min.js"></script>
 <script src="<?php echo SVRURL ?>js/custom.js"></script>