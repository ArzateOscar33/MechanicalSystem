<head>
    <style>
        :root {
            --ms-primary: #0f4c81;
            --ms-primary-dark: #0b365b;
            --ms-accent: #2ea44f;
            --ms-accent-soft: #eaf7ef;
            --ms-warning: #f4b400;
            --ms-dark: #1f2937;
            --ms-text: #374151;
            --ms-muted: #6b7280;
            --ms-bg: #f7fafc;
            --ms-white: #ffffff;
            --ms-soft-blue: #eef6fb;
            --ms-soft-gray: #f3f6f8;
            --ms-border: rgba(15, 76, 129, 0.10);
            --ms-shadow: 0 18px 45px rgba(15, 76, 129, 0.08);
            --ms-radius: 22px;
        }

        body {
            background: linear-gradient(180deg, #f8fbfd 0%, #f3f7fa 100%);
            color: var(--ms-text);
        }

        .ms-hero {
            position: relative;
            overflow: hidden;
            background:
                linear-gradient(120deg, rgba(255, 255, 255, .84), rgba(255, 255, 255, .72)),
                url('Assets/images/portal_clientes/hero-taller.jpg') center/cover no-repeat;
            min-height: 86vh;
            display: flex;
            align-items: center;
            border-bottom: 1px solid rgba(15, 76, 129, .08);
        }

        .ms-hero::before {
            content: "";
            position: absolute;
            top: -120px;
            right: -120px;
            width: 360px;
            height: 360px;
            border-radius: 50%;
            background: radial-gradient(circle, rgba(46, 164, 79, .12) 0%, rgba(46, 164, 79, 0) 70%);
            pointer-events: none;
        }

        .ms-hero::after {
            content: "";
            position: absolute;
            bottom: -160px;
            left: -120px;
            width: 420px;
            height: 420px;
            border-radius: 50%;
            background: radial-gradient(circle, rgba(15, 76, 129, .10) 0%, rgba(15, 76, 129, 0) 70%);
            pointer-events: none;
        }

        .ms-hero-content {
            position: relative;
            z-index: 2;
        }

        .ms-badge-hero {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            background: rgba(15, 76, 129, .08);
            color: var(--ms-primary);
            border: 1px solid rgba(15, 76, 129, .12);
            padding: .60rem 1rem;
            border-radius: 999px;
            font-size: .92rem;
            font-weight: 700;
            margin-bottom: 1.2rem;
        }

        .ms-hero h1 {
            font-size: clamp(2.3rem, 5vw, 4.4rem);
            font-weight: 800;
            line-height: 1.05;
            color: var(--ms-dark);
            margin-bottom: 1rem;
        }

        .ms-hero p {
            font-size: 1.08rem;
            line-height: 1.8;
            color: var(--ms-text);
            max-width: 670px;
            margin-bottom: 1.7rem;
        }

        .ms-hero-actions {
            display: flex;
            flex-wrap: wrap;
            gap: 12px;
        }

        .btn-ms-primary {
            background: linear-gradient(135deg, var(--ms-primary), var(--ms-primary-dark));
            color: #fff;
            border: none;
            border-radius: 14px;
            padding: .95rem 1.5rem;
            font-weight: 700;
            box-shadow: 0 12px 24px rgba(15, 76, 129, .20);
        }

        .btn-ms-primary:hover {
            color: #fff;
            transform: translateY(-1px);
        }

        .btn-ms-soft {
            background: #fff;
            color: var(--ms-primary);
            border: 1px solid rgba(15, 76, 129, .15);
            border-radius: 14px;
            padding: .95rem 1.5rem;
            font-weight: 700;
            box-shadow: 0 10px 22px rgba(15, 76, 129, .06);
        }

        .btn-ms-soft:hover {
            color: var(--ms-primary-dark);
            background: #f8fcff;
        }

        .ms-hero-panel {
            position: relative;
            z-index: 2;
            background: rgba(255, 255, 255, .88);
            border: 1px solid rgba(15, 76, 129, .10);
            border-radius: 24px;
            padding: 1.6rem;
            box-shadow: 0 22px 50px rgba(15, 76, 129, .10);
            backdrop-filter: blur(6px);
        }

        .ms-hero-panel h5 {
            color: var(--ms-dark);
            font-weight: 800;
            margin-bottom: .9rem;
        }

        .ms-hero-panel ul {
            list-style: none;
            margin: 0;
            padding: 0;
        }

        .ms-hero-panel li {
            display: flex;
            gap: 10px;
            align-items: flex-start;
            padding: .7rem 0;
            border-bottom: 1px solid rgba(15, 76, 129, .08);
            color: var(--ms-text);
        }

        .ms-hero-panel li:last-child {
            border-bottom: none;
        }

        .ms-section {
            padding: 88px 0;
        }

        .ms-section-title {
            font-size: clamp(1.9rem, 3vw, 2.8rem);
            font-weight: 800;
            color: var(--ms-dark);
            margin-bottom: .8rem;
        }

        .ms-section-text {
            max-width: 760px;
            margin: 0 auto;
            /* color: var(--ms-muted);*/
            color: white;
            font-size: 1.03rem;
            line-height: 1.8;
        }

        .ms-card {
            height: 100%;
            background: #fff;
            border: 1px solid var(--ms-border);
            border-radius: var(--ms-radius);
            padding: 1.7rem;
            box-shadow: var(--ms-shadow);
            transition: .25s ease;
        }

        .ms-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 24px 45px rgba(15, 76, 129, 0.10);
        }

        .ms-icon {
            width: 60px;
            height: 60px;
            border-radius: 18px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            background: linear-gradient(135deg, rgba(15, 76, 129, .10), rgba(46, 164, 79, .10));
            color: var(--ms-primary);
            font-size: 1.35rem;
            margin-bottom: 1rem;
        }

        .ms-card h5 {
            font-weight: 800;
            color: var(--ms-dark);
            margin-bottom: .75rem;
        }

        .ms-card p {
            color: var(--ms-muted);
            margin-bottom: 0;
            line-height: 1.75;
        }

        .ms-search-box {
            background: linear-gradient(135deg, #ffffff, #f6fbff);
            border: 1px solid rgba(15, 76, 129, .10);
            border-radius: 28px;
            box-shadow: 0 25px 55px rgba(15, 76, 129, .08);
            overflow: hidden;
        }

        .ms-search-content {
            padding: 2.3rem;
        }

        .ms-search-content h3 {
            font-size: clamp(1.8rem, 3vw, 2.5rem);
            font-weight: 800;
            color: var(--ms-dark);
            margin-bottom: 1rem;
        }

        .ms-search-content p {
            color: var(--ms-muted);
            line-height: 1.8;
        }

        .ms-search-img {
            width: 100%;
            height: 100%;
            min-height: 420px;
            object-fit: cover;
        }

        .ms-info-strip {
            background: linear-gradient(135deg, #0f4c81, #13619f);
            color: #fff;
            border-radius: 24px;
            overflow: hidden;
            box-shadow: 0 20px 45px rgba(15, 76, 129, .16);
        }

        .ms-info-item {
            text-align: center;
            padding: 1.7rem 1rem;
        }

        .ms-info-item h4 {
            font-weight: 800;
            margin-bottom: .4rem;
            font-size: 1.45rem;
            color: white;
        }

        .ms-info-item p {
            margin: 0;
            color: rgba(255, 255, 255, .86);
        }

        .ms-mvv-card {
            background: #fff;
            border-radius: 24px;
            border: 1px solid rgba(15, 76, 129, .10);
            box-shadow: 0 18px 40px rgba(15, 76, 129, .07);
            padding: 2rem;
            height: 100%;
        }

        .ms-mvv-card h4 {
            font-weight: 800;
            color: var(--ms-dark);
            margin-bottom: 1rem;
        }

        .ms-mvv-card p,
        .ms-mvv-card li {
            color: var(--ms-muted);
            line-height: 1.8;
        }

        .ms-values {
            list-style: none;
            padding: 0;
            margin: 0;
        }

        .ms-values li {
            display: flex;
            gap: 10px;
            margin-bottom: .8rem;
            align-items: flex-start;
        }

        .ms-contact-box {
            background: #fff;
            border-radius: 26px;
            border: 1px solid rgba(15, 76, 129, .10);
            box-shadow: 0 22px 50px rgba(15, 76, 129, .08);
            overflow: hidden;
        }

        .ms-contact-info {
            background: linear-gradient(135deg, #f0f8ff, #e6f2fa);
            padding: 2rem;
            height: 100%;
        }

        .ms-contact-info h3 {
            font-weight: 800;
            color: var(--ms-dark);
            margin-bottom: 1rem;
        }

        .ms-contact-info p {
            color: var(--ms-text);
            line-height: 1.8;
        }

        .ms-contact-item {
            display: flex;
            gap: 12px;
            align-items: flex-start;
            margin-bottom: 1rem;
            color: var(--ms-text);
        }

        .ms-contact-form {
            padding: 2rem;
            background: #fff;
        }

        .form-control,
        .form-select {
            min-height: 50px;
            border-radius: 14px;
            border: 1px solid rgba(15, 76, 129, .14);
        }

        .form-control:focus,
        .form-select:focus {
            border-color: rgba(15, 76, 129, .35);
            box-shadow: 0 0 0 .2rem rgba(15, 76, 129, .10);
        }

        .ms-mini-note {
            color: var(--ms-muted);
            font-size: .95rem;
        }

        @media (max-width: 991.98px) {
            .ms-hero {
                min-height: auto;
                padding: 90px 0 70px;
            }

            .ms-search-img {
                min-height: 280px;
            }
        }
    </style>
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
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
</head>

<!-- NAVBAR -->
<nav class="navbar navbar-expand-lg ms-navbar sticky-top">
    <div class="container">
        <a class="navbar-brand ms-brand-wrap" href="#">
            <div class="ms-logo-box">
                <!-- Placeholder de logo -->
                <img src="<?php echo BASE_URL; ?>assets/images/logo.png" class="logo-icon" alt="logo icon">
            </div>
            <div>
                <p class="ms-brand-title">Mechanical Emissions Services LLC</p>
            </div>
        </a>

        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarPortalClientes">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="navbarPortalClientes">
            <ul class="navbar-nav ms-auto align-items-lg-center gap-lg-2">
                <li class="nav-item"><a class="nav-link" href="<?php echo BASE_URL; ?>#inicio">Inicio</a></li>
                <li class="nav-item"><a class="nav-link" href="<?php echo BASE_URL; ?>#servicios">Servicios</a></li>
                <li class="nav-item"><a class="nav-link" href="<?php echo BASE_URL; ?>#nosotros">Nosotros</a></li>
                <li class="nav-item"><a class="nav-link" href="<?php echo BASE_URL; ?>Principal/vistaCertificados">Certificados</a></li>
                <li class="nav-item ms-lg-2">
                    <a href="<?php echo BASE_URL; ?>admin/index" class="btn btn-ms-primary text-white">
                        Acceso al sistema
                    </a>
                </li>
            </ul>
        </div>
    </div>
</nav>