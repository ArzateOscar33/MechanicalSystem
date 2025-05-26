

 

<!doctype html>
<html lang="en">

<head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!--favicon-->
    <link rel="icon" href="<?php echo BASE_URL; ?>assets/images/favicon-32x32.png" type="image/png" />
    <!--plugins-->
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
    <title><?php echo $data['title']; ?></title>
</head>

<body class="bg-login">
    <!--wrapper-->
    <div class="wrapper">
        <div class="section-authentication-signin d-flex align-items-center justify-content-center my-5 my-lg-0">
            <div class="container-fluid">
                <div class="row row-cols-1 row-cols-lg-2 row-cols-xl-3">
                    <div class="col mx-auto">
                        <div class="mb-4 text-center">
                            <img src="<?php echo BASE_URL; ?>assets/images/logo.png" width="180" alt="LOGO" />
                        </div>
                        <div class="card">
                            <div class="card-body">
                                <div class="border p-4 rounded">
                                    <div class="text-center">
                                        <h3 class="">Restablecer Contraseña</h3>
                                    </div>

                                    <div class="login-separater text-center mb-4">
                                        <span>Ingresa una nueva contraseña para tu cuenta</span>
                                        <hr />
                                    </div>

                                    <div class="form-body">
                                        <form class="row g-3" id="formRestablecer" method="POST" action="<?php echo BASE_URL; ?>Recuperar/cambiarPassword">
                                            <input type="hidden" name="token" value="<?php echo $data['token']; ?>">

                                            <div class="col-12">
                                                <label for="password" class="form-label">Nueva Contraseña</label>
                                                <input type="password" class="form-control" id="password" name="password" placeholder="Nueva contraseña" required>
                                            </div>

                                            <div class="col-12">
                                                <label for="confirmar" class="form-label">Confirmar Contraseña</label>
                                                <input type="password" class="form-control" id="confirmar" name="confirmar" placeholder="Confirmar contraseña" required>
                                            </div>

                                            <div class="col-12">
                                                <div class="d-grid">
                                                    <button type="submit" class="btn btn-success"><i class="bx bx-check-shield"></i> Actualizar Contraseña</button>
                                                </div>
                                            </div>
                                        </form>
                                    </div> <!-- /.form-body -->
                                </div>
                            </div>
                        </div>
                    </div>
                </div> <!-- /.row -->
            </div>
        </div>
    </div>
    <!--end wrapper-->

    <!-- Bootstrap JS -->
    <script src="<?php echo BASE_URL; ?>assets/js/bootstrap.bundle.min.js"></script>
    <script src="<?php echo BASE_URL; ?>assets/js/jquery.min.js"></script>
    <script src="<?php echo BASE_URL; ?>assets/plugins/simplebar/js/simplebar.min.js"></script>
    <script src="<?php echo BASE_URL; ?>assets/plugins/metismenu/js/metisMenu.min.js"></script>

    <!-- Scripts -->
    <script>
        const base_url = '<?php echo BASE_URL; ?>';
    </script>
    <script src="<?php echo BASE_URL; ?>assets/js/sweetalert2.all.min.js"></script>
    <script src="<?php echo BASE_URL; ?>assets/js/modulos/restablecer.js"></script>
</body>

</html>