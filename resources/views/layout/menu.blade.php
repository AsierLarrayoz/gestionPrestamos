<div id="kt_app_sidebar"
    class="app-sidebar flex-column"
    data-kt-drawer="true"
    data-kt-drawer-name="app-sidebar"
    data-kt-drawer-activate="{default: true, lg: false}"
    data-kt-drawer-overlay="true"
    data-kt-drawer-width="250px"
    data-kt-drawer-direction="start"
    data-kt-drawer-toggle="#kt_app_sidebar_mobile_toggle">

    <div class="app-sidebar-wrapper">
        <div id="kt_app_sidebar_wrapper" class="hover-scroll-y my-5 my-lg-2 mx-4" data-kt-scroll="true" data-kt-scroll-activate="{default: true, lg: true}" data-kt-scroll-height="auto" data-kt-scroll-dependencies="#kt_app_header,#kt_app_sidebar_user,#kt_app_sidebar_fichaje" data-kt-scroll-wrappers="#kt_app_sidebar_wrapper" data-kt-scroll-offset="0px" style="height: 267px;">
            <div id="kt_app_sidebar_menu" data-kt-menu="true" data-kt-menu-expand="false" class="app-sidebar-menu-primary menu menu-column menu-rounded menu-sub-indention menu-state-bullet-primary px-3 mb-5" style="padding-bottom: 100px;">
                <div data-kt-menu-trigger="click" class="menu-item menu-accordion">
                    <span class="menu-link">
                        <span class="menu-icon"><i class="ki-outline ki-home fs-2"></i></span>
                        <span class="menu-title">Fitfox</span>
                        <span class="menu-arrow"></span>
                    </span>
                    <div class="menu-sub menu-sub-accordion">
                        <div class="menu-item">
                            <a class="menu-link" href="/paginaCentro">
                                <span class="menu-bullet"><span class="bullet bullet-dot"></span></span>
                                <span class="menu-title">Centro</span>
                            </a>
                        </div>
                        <div class="menu-item">
                            <a class="menu-link" href="/festivos">
                                <span class="menu-bullet"><span class="bullet bullet-dot"></span></span>
                                <span class="menu-title">Festivos</span>
                            </a>
                        </div>
                    </div>
                </div>
                <div data-kt-menu-trigger="click" class="menu-item menu-accordion">
                    <span class="menu-link">
                        <span class="menu-icon"><i class="ki-outline ki-book-open fs-2"></i></span>
                        <span class="menu-title">Tareas</span>
                        <span class="menu-arrow"></span>
                    </span>
                    <div class="menu-sub menu-sub-accordion">
                        <div class="menu-item">
                            <a class="menu-link" href="/paginaTareas">
                                <span class="menu-bullet"><span class="bullet bullet-dot"></span></span>
                                <span class="menu-title">Tareas</span>
                            </a>
                        </div>
                        <div class="menu-item">
                            <a class="menu-link" href="/paginaListasTareas">
                                <span class="menu-bullet"><span class="bullet bullet-dot"></span></span>
                                <span class="menu-title">Listas de Tareas</span>
                            </a>
                        </div>
                    </div>
                </div>
                <div data-kt-menu-trigger="click" class="menu-item menu-accordion">
                    <span class="menu-link">
                        <span class="menu-icon"><i class="ki-outline ki-archive fs-2"></i></span>
                        <span class="menu-title">Incidencias</span>
                        <span class="menu-arrow"></span>
                    </span>
                    <div class="menu-sub menu-sub-accordion">
                        <div class="menu-item">
                            <a class="menu-link" href="/paginaMaestroIncidencias">
                                <span class="menu-bullet"><span class="bullet bullet-dot"></span></span>
                                <span class="menu-title">Tipos de incidencias</span>
                            </a>
                        </div>
                        <div class="menu-item">
                            <a class="menu-link" href="/paginaIncidencias">
                                <span class="menu-bullet"><span class="bullet bullet-dot"></span></span>
                                <span class="menu-title">Incidencias</span>
                            </a>
                        </div>
                    </div>
                </div>
                <div data-kt-menu-trigger="click" class="menu-item menu-accordion">
                    <span class="menu-link">
                        <span class="menu-icon"><i class="ki-outline ki-calendar fs-2"></i></span>
                        <span class="menu-title">Calendarios</span>
                        <span class="menu-arrow"></span>
                    </span>
                    <div class="menu-sub menu-sub-accordion">
                        <div class="menu-item">
                            <a class="menu-link" href="/paginaTipoAusencias">
                                <span class="menu-bullet"><span class="bullet bullet-dot"></span></span>
                                <span class="menu-title">No definido</span>
                            </a>
                        </div>
                    </div>
                </div>
                <!-- ESTA OPCION DEL MENU QUE SOLO SALGA AL ALDMIN-->
                @if(Auth::check() && Auth::user()->rol->rol === 'Administrador')
                <div data-kt-menu-trigger="click" class="menu-item menu-accordion">
                    <span class="menu-link">
                        <span class="menu-icon"><i class="ki-outline ki-lock fs-2"></i></span>
                        <span class="menu-title">Super Admin</span>
                        <span class="menu-arrow"></span>
                    </span>
                    <div class="menu-sub menu-sub-accordion">
                        <div class="menu-item">
                            <a class="menu-link" href="{{ route('configuracion.index') }}">
                                <span class="menu-bullet"><span class="bullet bullet-dot"></span></span>
                                <span class="menu-title">Gestionar usuarios</span>
                            </a>
                        </div>
                        <div class="menu-item">
                            <a class="menu-link" href="{{ route('almacenes.index') }}">
                                <span class="menu-bullet"><span class="bullet bullet-dot"></span></span>
                                <span class="menu-title">Gestionar almacenes</span>
                            </a>
                        </div>
                        <div class="menu-item">
                            <a class="menu-link" href="{{ route('activos.index') }}">
                                <span class="menu-bullet"><span class="bullet bullet-dot"></span></span>
                                <span class="menu-title">Gestionar activos</span>
                            </a>
                        </div>
                    </div>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>