<footer class="artisan-footer">
    <div class="container">
        <div class="row">
                
            <!-- Sección Contacto -->
            <div class="col-md-4 mb-4">
                <h5 class="footer-title border-bottom pb-2">Contacto</h5>
                <ul class="list-unstyled">
                    <li class="mb-2 d-flex align-items-center">
                        <i class="bi bi-geo-alt me-2 text-cva-gold"></i>
                        <span>9 de Julio 1449, Ctes, Argentina</span>
                    </li>
                    <li class="mb-2 d-flex align-items-center">
                        <i class="bi bi-whatsapp me-2 text-cva-gold"></i>
                        <span><?= $env_whatsapp ?></span>
                    </li>
                </ul>
            </div>
    
            <!-- Sección 2: Sobre Nosotros -->
            <div class="col-md-4 mb-4">
                <h5 class="footer-title border-bottom pb-2">Sobre Nosotros</h5>
                <p class="small opacity-75">Somos una empresa familiar especializada en crear muebles únicos, hechos a mano con maderas de calidad y pensados para perdurar.</p>
                <a href="<?= base_url('quienesSomos') ?>" class="btn btn-outline-light btn-sm rounded-pill px-3">Conoce más</a>
            </div>

            <!-- Sección Redes Sociales -->
            <div class="col-md-4 mb-4">
                <h5 class="footer-title border-bottom pb-2 mb-3">Síguenos</h5>
                <div class="social-icons d-flex flex-column gap-2">
                    <a href="https://fb.com/misitio" class="footer-link d-flex align-items-center" target="_blank">
                        <i class="bi bi-facebook me-2 fs-5"></i> Facebook
                    </a>
                    <a href="https://twitter.com/misitio" class="footer-link d-flex align-items-center" target="_blank">
                        <i class="bi bi-twitter-x me-2 fs-5"></i> Twitter / X
                    </a>
                    <a href="https://instagram.com/misitio" class="footer-link d-flex align-items-center" target="_blank">
                        <i class="bi bi-instagram me-2 fs-5"></i> Instagram
                    </a>
                </div>
            </div>
        </div>

        <!-- Copyright -->
        <div class="row mt-4 pt-4 border-top border-secondary">
            <div class="col-12 text-center">
                <p class="small mb-0 opacity-50">
                    &copy; <?= date('Y') ?> CVA Muebles. Artesanía en madera con tradición correntina.
                </p>
            </div>
        </div>
    </div>
</footer>
