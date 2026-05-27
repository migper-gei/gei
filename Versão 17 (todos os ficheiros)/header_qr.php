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
// header_qr.php — versão pública do header, sem config.php nem ligação à BD.
// Usado em páginas de acesso via QR Code que não requerem autenticação.
include_once('svrurl.php');
?>

<!-- header -->
<header>
  <div class="header">
    <div class="container-fluid">
      <div class="row">
        <div class="col-xl-3 col-lg-3 col-md-3 col-sm-3 col logo_section">
          <div class="full">
            <div class="center-desk">
              <div class="logo">
                <img title="GEI" src="<?php echo SVRURL ?>images/gei_icon_2.png" alt="GEI" width="50%" height="50%">
              </div>
            </div>
          </div>
        </div>

        <div class="col-xl-9 col-lg-9 col-md-9 col-sm-9">
          <nav class="navigation navbar navbar-expand-md navbar-dark">
            <button class="navbar-toggler" type="button" aria-controls="navbarsQR" aria-expanded="false" aria-label="Toggle navigation" id="gei-nav-toggler-qr">
              <span class="navbar-toggler-icon"></span>
            </button>
            <div class="navbar-collapse" id="navbarsQR" style="display:none;">
              <ul class="navbar-nav mr-auto">
                <div class="col-md-1 col-sm-12 text-center mt-sm-3 mt-md-0">
                  <a title="Início" href="<?php echo SVRURL; ?>i" class="home-button">
                    <i class="fas fa-home"></i>
                  </a>
                </div>
              </ul>
            </div>
          </nav>
        </div>
      </div>
    </div>
  </div>
</header>

<script>
(function() {
    function initNavToggler() {
        var btn = document.getElementById('gei-nav-toggler-qr');
        var nav = document.getElementById('navbarsQR');
        if (!btn || !nav) return;
        btn.addEventListener('click', function () {
            var isOpen = nav.style.display === 'block';
            nav.style.display = isOpen ? 'none' : 'block';
            btn.setAttribute('aria-expanded', String(!isOpen));
        });
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
