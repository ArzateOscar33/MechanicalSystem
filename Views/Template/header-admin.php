<!doctype html>
<html lang="en">

<head>
    
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!--favicon-->
    <link rel="icon" href="<?php echo BASE_URL; ?>assets/images/favicon-32x32.png" type="image/png" />
    <link href="<?php echo BASE_URL; ?>assets/plugins/simplebar/css/simplebar.css" rel="stylesheet" />
    <link href="<?php echo BASE_URL; ?>assets/plugins/metismenu/css/metisMenu.min.css" rel="stylesheet" />
    <!-- loader-->
    <link href="<?php echo BASE_URL; ?>assets/css/pace.min.css" rel="stylesheet" />
    <script src="<?php echo BASE_URL; ?>assets/js/pace.min.js"></script>
    <!-- Bootstrap CSS -->
    <link href="<?php echo BASE_URL; ?>assets/css/bootstrap.min.css" rel="stylesheet">
    <link href="<?php echo BASE_URL; ?>assets/css/bootstrap-extended.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500&display=swap" rel="stylesheet">
    <link href="<?php echo BASE_URL; ?>assets/css/app.css" rel="stylesheet">
    <link href="<?php echo BASE_URL; ?>assets/css/icons.css" rel="stylesheet">
    <!-- Theme Style CSS -->
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>assets/css/dark-theme.css" />
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>assets/css/semi-dark.css" />
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>assets/css/header-colors.css" />
    <link rel="stylesheet" type="text/css" href="<?php echo BASE_URL . 'assets/DataTables/datatables.min.css'; ?>">
    <link href="<?php echo BASE_URL; ?>assets/css/dropzone.css" rel="stylesheet" type="text/css" />
    <title><?php echo TITLE . ' - ' . $data['title']; ?></title>
</head>

<body>
    <!--wrapper-->
    <div class="wrapper">
        <!--sidebar wrapper -->
        <div class="sidebar-wrapper" data-simplebar="true">
            <div class="sidebar-header">
                <div>
                    <img src="<?php echo BASE_URL; ?>assets/images/logo.png" class="logo-icon" alt="logo icon">
                </div>
                <div>
                    <h4 class="logo-text"><?php echo TITLE; ?></h4>
                </div>
                <div class="toggle-icon ms-auto"><i class='bx bx-arrow-to-left'></i>
                </div>
            </div>
            <!--navigation-->
            <ul class="metismenu" id="menu">
                <li>
                    <a href="<?php echo BASE_URL . 'admin/home'; ?>">
                        <div class="parent-icon"><i class=' bx bx-home-circle  m-2'></i></div>
                        <div class="menu-title">Dashboard</div>
                    </a>
                </li>

                <!-- Certificados -->
                <li>
                    <a href="javascript:;" class="has-arrow">
                        <div class="parent-icon"><i class=' m-2 fas fa-file-alt'></i></div>
                        <div class="menu-title">Certificados</div>
                    </a>
                    <ul>
                        <?php if ($_SESSION['rol_usuario'] == 1): ?>
                            <li><a href="<?php echo BASE_URL . 'CargarCertificados'; ?>"><i class='fas fa-upload m-2'></i>Cargar Certificados</a></li>
                        <?php endif; ?>
                        <?php if ($_SESSION['rol_usuario'] <= 3): ?>
                            <li><a href="<?php echo BASE_URL . 'CrearCertificados'; ?>"><i class='fas fa-plus-circle m-2'></i>Crear Nuevo</a></li>
                            <li><a href="<?php echo BASE_URL . 'BuscarCertificados'; ?>"><i class='fas fa-search m-2'></i>Buscar Certificados</a></li>
                        <?php endif; ?>
                        <?php if ($_SESSION['rol_usuario'] == 1): ?>
                            <li><a href="<?php echo BASE_URL . 'DescargarCertificados'; ?>"><i class='fas fa-download m-2'></i>Control de Certificados</a></li>
                        <?php endif; ?>
                    </ul>
                </li>

                <!-- Errores -->
                <li>
                    <a href="javascript:;" class="has-arrow">
                        <div class="parent-icon"><i class='fas fa-bug  m-2'></i></div>
                        <div class="menu-title">Errores</div>
                    </a>
                    <ul>
                        <?php if ($_SESSION['rol_usuario'] <= 3): ?>
                            <li><a href="<?php echo BASE_URL . 'ErroresUsuario'; ?>"><i class='fas fa-exclamation-triangle m-2'></i>Reportar Errores (Usuario)</a></li>
                        <?php endif; ?>
                        <?php if ($_SESSION['rol_usuario'] == 1): ?>
                            <li><a href="<?php echo BASE_URL . 'ErroresAdmin'; ?>"><i class='fas fa-exclamation-circle  m-2'></i>Revisión de Errores (Admin)</a></li>
                        <?php endif; ?>
                    </ul>
                </li>

                <!-- Administración -->
                <?php if ($_SESSION['rol_usuario'] == 1): ?>
                    <li>
                        <a href="javascript:;" class="has-arrow">
                            <div class="parent-icon"><i class='m-2 fas fa-cogs'></i></div>
                            <div class="menu-title">Administración</div>
                        </a>
                        <ul>
                            <li><a href="<?php echo BASE_URL . 'Usuarios'; ?>"><i class='fas fa-user m-2'></i>Usuarios</a></li>
                            <li><a href="<?php echo BASE_URL . 'ControlInspectores'; ?>"><i class='fas fa-wrench  m-2'></i>Inspectores</a></li>
                            <li><a href="<?php echo BASE_URL . 'Direcciones'; ?>"><i class='fas fa-map-marked-alt  m-2'></i>Direcciones</a></li>
                            <li><a href="<?php echo BASE_URL . 'Importaciones'; ?>"><i class='fas fa-file-import  m-2'></i>Importaciones</a></li>
                        </ul>
                    </li>
                <?php endif; ?>

                <!-- Empleados -->
                <?php if ($_SESSION['rol_usuario'] == 1): ?>
                    <li>
                        <a href="javascript:;" class="has-arrow">
                            <div class="parent-icon"><i class=' m-2 fas fa-user-tie'></i></div>
                            <div class="menu-title">Personal</div>
                        </a>
                        <ul>
                            <li><a href="<?php echo BASE_URL . 'Departamentos'; ?>"><i class='fas fa-book  m-2'></i>Departamentos</a></li>
                            <li><a href="<?php echo BASE_URL . 'Empleados'; ?>"><i class='fas fa-user-tie  m-2'></i>Empleados</a></li>
                        </ul>
                    </li>
                <?php endif; ?>

                                <?php if ($_SESSION['rol_usuario'] == 1): ?>
                    <li>
                        <a href="javascript:;" class="has-arrow">
                            <div class="parent-icon"><i class=' m-2 fa-solid  fas fa-chart-line'></i></div>
                            <div class="menu-title">Estadisticas</div>
                        </a>
                        <ul>
                            <li><a href="<?php echo BASE_URL . 'Estadisticas'; ?>"><i class='fas fa-chart-bar  m-2'></i>Graficos Estadisticos</a></li>
                            
                        </ul>
                    </li>
                <?php endif; ?>
            </ul>



            <!--end navigation-->
        </div>
        <!--end sidebar wrapper -->
        <!--start header -->
        <header>
            <div class="topbar d-flex align-items-center">
                <nav class="navbar navbar-expand">
                    <div class="mobile-toggle-menu"><i class='bx bx-menu'></i>
                    </div>
                    <div class="search-bar flex-grow-1">

                    </div>
                    <div class="user-box dropdown">
                        <a class="d-flex align-items-center nav-link dropdown-toggle dropdown-toggle-nocaret" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            <img src="<?php echo BASE_URL; ?>assets/images/logo.png" class="user-img" alt="user avatar">
                            <div class="user-info ps-3">
                                <p class="user-name mb-0"><?php echo $_SESSION['nombre_usuario'] . ' ' .  $_SESSION['apellido_usuario']; ?></p>
                                <p class="designattion mb-0"><?php echo $_SESSION['email']; ?></p>
                            </div>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end">

                            <li><a class="dropdown-item" href="<?php echo BASE_URL . 'admin/salir'; ?>"><i class='bx bx-log-out-circle'></i><span>Logout</span></a>
                            </li>
                        </ul>
                    </div>
                </nav>
            </div>
        </header>
        <!--end header -->
        <!--start page wrapper -->
        <div class="page-wrapper">
            <div class="page-content">