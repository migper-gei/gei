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
                   <p style="color:white;font-size:10px">Copyright 2021-2026 | 
                   Versão 17 | gei@miguelarpereira.pt
                   <?php if (isset($_SESSION['user_id'])): ?>
                   &nbsp;|&nbsp;
                   <a href="contacto.php" style="color:#aad4f5;font-size:10px;text-decoration:none;"
                      onmouseover="this.style.textDecoration='underline'"
                      onmouseout="this.style.textDecoration='none'">
                      ✉ Contacto / Reportar erro
                   </a>
                   <?php endif; ?>

                  </p>
                </div>
    </div>

    

 <!-- end footer -->



  <!-- Javascript files
 <script src="<?php echo SVRURL ?>js/jquery.min.js"></script>
-->

 
 <!-- Bootstrap 4.6.2 (inclui Popper) -->
 <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"></script>

<!-- ═══ TEMA ESCURO — toggle (centralizado no footer para todas as páginas) ═══ -->
<?php if (!defined('SVRURL')) include("svrurl.php"); ?>
<script src="<?php echo SVRURL ?>js/dark-theme.js"></script>
<!-- ══════════════════════════════════════════════════════════════════════════ -->



