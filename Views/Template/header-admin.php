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
                        <div class="parent-icon"><i class='bx bx-home-circle'></i></div>
                        <div class="menu-title">Dashboard</div>
                    </a>
                </li>

                <?php if ($_SESSION['rol_usuario'] == 1): // Solo admin ,si quieres agregar manager || $_SESSION['rol_usuario'] == 2
                ?>
                    <li>
                        <a href="<?php echo BASE_URL . 'CargarCertificados'; ?>">
                            <div class="menu-title"><i class='fas fa-upload'></i> Cargar Certificados</div>
                        </a>
                    </li>
                <?php endif; ?>

                <?php if ($_SESSION['rol_usuario'] == 1 || $_SESSION['rol_usuario'] == 2|| $_SESSION['rol_usuario'] == 3): // Admin,manager,capturista
                ?>
                    <li>
                        <a href="<?php echo BASE_URL . 'CrearCertificados'; ?>">
                            <div class="menu-title"><i class='fas fa-plus-circle'></i> Crear Nuevo Certificado</div>
                        </a>
                    </li>
                    <li>
                        <a href="<?php echo BASE_URL . 'BuscarCertificados'; ?>">
                            <div class="menu-title"><i class='fas fa-search'></i> Busqueda de Certificados</div>
                        </a>
                    </li>
                <?php endif; ?>

                <?php if ($_SESSION['rol_usuario'] == 1): // Solo admin 
                ?>
                    <li>
                        <a href="<?php echo BASE_URL . 'Usuarios'; ?>">
                            <div class="menu-title"><i class='fas fa-user'></i> Usuarios</div>
                        </a>
                    </li>
                <?php endif; ?>

                <?php if ($_SESSION['rol_usuario'] == 1): // Solo admin 
                ?>
                    <li>
                        <a href="<?php echo BASE_URL . 'DescargarCertificados'; ?>">
                            <div class="menu-title"><i class='fas fa-download'></i> Control de  Certificados</div>
                        </a>
                    </li>
                <?php endif; ?>
                <?php if ($_SESSION['rol_usuario'] == 1): // Solo admin 
                ?>
                    <li>
                        <a href="<?php echo BASE_URL . 'Direcciones'; ?>">
                            <div class="menu-title"><i class='fas fa-map-marked-alt'></i> Control de Direcciones</div>
                        </a>
                    </li>
                <?php endif; ?>
                <?php if ($_SESSION['rol_usuario'] == 1): // Solo admin 
                ?>
                    <li>
                        <a href="<?php echo BASE_URL . 'Importaciones'; ?>">
                            <div class="menu-title"><i class='fas fa-file-import'></i> Registro de Importaciones</div>
                        </a>
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