<div id="kt_app_footer" class="app-footer">
    <!-- Quiero un menu que solamente se abra en movil -->
    <style>
        /* Menú fijo inferior solo visible en móvil */
        .mobile-footer-menu {
            position: fixed;
            bottom: 1.5rem;
            /* un poco elevado para no tocar el borde */
            left: 50%;
            transform: translateX(-50%);
            width: 60%;
            max-width: 400px;
            background: #fff;
            border-radius: 2rem;
            box-shadow: 0 -3px 12px rgba(0, 0, 0, 0.15);
            padding: 0.5rem 0.75rem;
            z-index: 1050;
        }

        /* Contenedor de los botones */
        .menu-buttons {
            display: flex;
            justify-content: space-around;
            align-items: center;
        }

        /* Botones */
        .menu-btn {
            display: flex;
            flex-direction: column;
            align-items: center;
            background: none;
            border: none;
            font-size: 0.75rem;
            color: #6c757d;
            transition: color 0.25s ease;
        }

        .menu-btn i {
            font-size: 1.5rem;
            margin-bottom: 2px;
        }

        /* Estado activo o hover */
        .menu-btn:hover,
        .menu-btn.active {
            color: #0d6efd;
            /* azul Bootstrap */
        }
    </style>
    <!-- Menú fijo inferior SOLO para móvil -->
    <div class="mobile-footer-menu d-md-none">
        <div class="menu-buttons">
            <!-- Atrás -->
            <button class="menu-btn" onclick="history.back()">
                <i class="bi bi-arrow-left"></i>
                <span>Atrás</span>
            </button>
            <!-- Fichajes -->
            <a href="/paginaFichajes" class="menu-btn">
                <i class="bi bi-people"></i>
                <span>Fichajes</span>
            </a>
            <!-- Calendario -->
            <a href="/miCalendario" class="menu-btn">
                <i class="bi bi-calendar-event"></i>
                <span>Calendario</span>
            </a>
        </div>
    </div>
    <!--begin::Footer container-->
    <div class="app-container container-fluid d-flex flex-column flex-md-row flex-center flex-md-stack py-3">
        <!--begin::Copyright-->
        <div class="text-gray-900 order-2 order-md-1">
            <a href="https://fitfox.es" target="_blank" class="text-gray-800 text-hover-primary">TimeFox</a>
        </div>
        <!--end::Copyright-->
        <!--begin::Menu-->
        <ul class="menu menu-gray-600 menu-hover-primary fw-semibold order-1">
            <li class="menu-item me-3">
                <a href="#" data-bs-toggle="modal" data-bs-target="#privacyModal">Política de privacidad</a>

                <!-- Modal -->
                <div class="modal fade" id="privacyModal" tabindex="-1" aria-labelledby="privacyModalLabel" aria-hidden="true">
                    <div class="modal-dialog modal-lg modal-dialog-centered">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="privacyModalLabel">Política de privacidad</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                            </div>
                            <div class="modal-body"></div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                            </div>
                        </div>
                    </div>
                </div>
            </li>
            <li class="menu-item mx-3">
                <a href="#" data-bs-toggle="modal" data-bs-target="#cookiesModal">Política de cookies</a>

                <!-- Cookies Modal -->
                <div class="modal fade" id="cookiesModal" tabindex="-1" aria-labelledby="cookiesModalLabel" aria-hidden="true">
                    <div class="modal-dialog modal-lg modal-dialog-centered">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="cookiesModalLabel">Política de cookies</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                            </div>
                            <div class="modal-body"></div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                            </div>
                        </div>
                    </div>
                </div>
            </li>
            <li class="menu-item ms-3">
                <a href="#" data-bs-toggle="modal" data-bs-target="#legalModal">Aviso legal</a>

                <!-- Aviso Legal Modal -->
                <div class="modal fade" id="legalModal" tabindex="-1" aria-labelledby="legalModalLabel" aria-hidden="true">
                    <div class="modal-dialog modal-lg modal-dialog-centered">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="legalModalLabel">Aviso legal</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                            </div>
                            <div class="modal-body" style="color: #898989; font-family: Catamaran, sans-serif; font-size: 15px;"></div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                            </div>
                        </div>
                    </div>
                </div>
            </li>
        </ul>
        <!--end::Menu-->
    </div>
    <!--end::Footer container-->
</div>