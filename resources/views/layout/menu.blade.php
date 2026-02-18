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

                {{-- INICIO --}}
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

                {{-- PRÉSTAMOS --}}
                @if(Auth::user()->hasPermission('prestamos.leer') || Auth::user()->hasPermission('prestamos.escribir'))
                <div data-kt-menu-trigger="click" class="menu-item menu-accordion">
                    <span class="menu-link">
                        <span class="menu-icon">
                            <i class="ki-outline ki-delivery-2 fs-2"></i>
                        </span>
                        <span class="menu-title">Préstamos</span>
                        <span class="menu-arrow"></span>
                    </span>

                    <div class="menu-sub menu-sub-accordion">
                        <div class="menu-item">
                            <a class="menu-link" href="{{ route('prestamos.create') }}">
                                <span class="menu-bullet"><span class="bullet bullet-dot"></span></span>
                                <span class="menu-title">Puesto de Escaneo</span>
                            </a>
                        </div>
                        <div class="menu-item">
                            <a class="menu-link" href="{{ route('prestamos.index') }}">
                                <span class="menu-bullet"><span class="bullet bullet-dot"></span></span>
                                <span class="menu-title">Prestamos activos</span>
                            </a>
                        </div>
                        <div class="menu-item">
                            <a class="menu-link" href="{{ route('prestamos.historial') }}">
                                <span class="menu-bullet"><span class="bullet bullet-dot"></span></span>
                                <span class="menu-title">Historial</span>
                            </a>
                        </div>
                    </div>
                </div>
                @endif

                {{-- INCIDENCIAS --}}
                @if(Auth::user()->hasPermission('incidencias.leer') || Auth::user()->hasPermission('incidencias.escribir'))
                <div data-kt-menu-trigger="click" class="menu-item menu-accordion {{ request()->routeIs('incidencias.*') || request()->routeIs('niveles.*') ? 'hover show' : '' }}">
                    <span class="menu-link">
                        <span class="menu-icon">
                            <i class="ki-outline ki-flag fs-2"></i> </span>
                        <span class="menu-title">Incidencias</span>
                        <span class="menu-arrow"></span>
                    </span>

                    <div class="menu-sub menu-sub-accordion">
                        <div class="menu-item">
                            <a class="menu-link {{ request()->routeIs('incidencias.index') ? 'active' : '' }}" href="{{ route('incidencias.index') }}">
                                <span class="menu-bullet"><span class="bullet bullet-dot"></span></span>
                                <span class="menu-title">Listado de Incidencias</span>
                            </a>
                        </div>
                        <div class="menu-item">
                            <a class="menu-link {{ request()->routeIs('incidencias.create') ? 'active' : '' }}" href="{{ route('incidencias.create') }}">
                                <span class="menu-bullet"><span class="bullet bullet-dot"></span></span>
                                <span class="menu-title">Reportar Nueva</span>
                            </a>
                        </div>
                    </div>
                </div>
                @endif

                {{-- ALMACENES --}}
                @if(Auth::user()->hasPermission('almacenes.leer') || Auth::user()->hasPermission('almacenes.escribir'))
                <div class="menu-item">
                    <a class="menu-link" href="{{ route('almacenes.index') }}">
                        <span class="menu-icon">
                            <i class="ki-outline ki-home-3 fs-2"></i>
                        </span>
                        <span class="menu-title">Gestionar almacenes</span>
                    </a>
                </div>
                @endif

                {{-- ACTIVOS --}}
                @if(Auth::user()->hasPermission('activos.leer') || Auth::user()->hasPermission('activos.escribir'))
                <div class="menu-item">
                    <a class="menu-link" href="{{ route('activos.index') }}">
                        <span class="menu-icon">
                            <i class="ki-outline ki-barcode fs-2"></i>
                        </span>
                        <span class="menu-title">Gestionar activos</span>
                    </a>
                </div>
                @endif

                {{-- RESERVAS (Ahora tiene sus propios permisos) --}}
                @if(Auth::user()->hasPermission('reservas.leer') || Auth::user()->hasPermission('reservas.escribir'))
                <div class="menu-item">
                    <a class="menu-link" href="{{ route('reservas.index') }}">
                        <span class="menu-icon">
                            <i class="ki-outline ki-calendar fs-2"></i>
                        </span>
                        <span class="menu-title">Gestionar reservas</span>
                    </a>
                </div>
                @endif

                {{-- ADMIN (Usuarios, Roles y Logs) --}}
                {{-- Mostramos el menú Admin si tiene acceso a usuarios O a logs --}}
                @if(Auth::user()->hasPermission('usuarios.leer') || Auth::user()->hasPermission('usuarios.escribir') || Auth::user()->hasPermission('logs.leer'))
                <div data-kt-menu-trigger="click" class="menu-item menu-accordion">
                    <span class="menu-link">
                        <span class="menu-icon"><i class="ki-outline ki-lock fs-2"></i></span>
                        <span class="menu-title">Admin</span>
                        <span class="menu-arrow"></span>
                    </span>
                    <div class="menu-sub menu-sub-accordion">

                        {{-- Solo si puede ver usuarios --}}
                        @if(Auth::user()->hasPermission('usuarios.leer') || Auth::user()->hasPermission('usuarios.escribir'))
                        <div class="menu-item">
                            <a class="menu-link" href="{{ route('configuracion.index') }}">
                                <span class="menu-bullet"><span class="bullet bullet-dot"></span></span>
                                <span class="menu-title">Gestionar usuarios</span>
                            </a>
                        </div>

                        <div class="menu-item">
                            <a class="menu-link" href="{{ route('permisos.index') }}">
                                <span class="menu-bullet"><span class="bullet bullet-dot"></span></span>
                                <span class="menu-title">Gestionar roles/permisos</span>
                            </a>
                        </div>
                        @endif

                        {{-- Solo si puede ver logs --}}
                        @if(Auth::user()->hasPermission('logs.leer'))
                        <div class="menu-item">
                            <a class="menu-link" href="{{ route('logs.index') }}">
                                <span class="menu-bullet"><span class="bullet bullet-dot"></span></span>
                                <span class="menu-title">Ver logs</span>
                            </a>
                        </div>
                        @endif

                    </div>
                </div>
                @endif

            </div>
        </div>
    </div>
</div>