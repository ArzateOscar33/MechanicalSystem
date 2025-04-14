<?php include_once 'Views/template/header-admin.php'; ?>
<div class="row row-cols-1 row-cols-md-2 row-cols-xl-4">
    <div class="col">
        <div class="card radius-10 border-start border-0 border-3 border-warning">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div>
                        <p class="mb-0 text-secondary">Certificados</p>
                        <h4 class="my-1 text-warning">
                            <?php echo $data['certificadosTotales']['total']; ?>
                    </div>
                    <div class="widgets-icons-2 rounded-circle bg-gradient-blooker text-white ms-auto"><i class='fas fa-file-contract'></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col">
        <div class="card radius-10 border-start border-0 border-3 border-info">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div>
                        <p class="mb-0 text-secondary">Zip Guardados</p>
                        <h4 class="my-1 text-info">
                            ######
                    </div>
                    <div class="widgets-icons-2 rounded-circle bg-gradient-bloody text-white ms-auto"><i class='fas fa-file-archive'></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col">
        <div class="card radius-10 border-start border-0 border-3 border-success">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div>
                        <p class="mb-0 text-secondary">Ciudades Registradas</p>
                        <h4 class="my-1 text-success">
                            <?php echo $data['ciudadesTotales']['total']; ?>
                    </div>
                    <div class="widgets-icons-2 rounded-circle bg-gradient-ohhappiness text-white ms-auto"><i class='fas fa-city'></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col">
        <div class="card radius-10 border-start border-0 border-3 border-warning">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div>
                        <p class="mb-0 text-secondary">Inspectores Registrados</p>
                        
                        <h4 class="my-1 text-warning"><?php echo $data['inspector_name']['total']; ?>
                    </div>
                    <div class="widgets-icons-2 rounded-circle bg-gradient-blooker text-white ms-auto"><i class="fas fa-id-card-alt"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>
</div>

<div>

    <!--end row-->
    <div class="row">
        <!--grafico ciudades-->
        <div class="col-12 col-lg-4">
            <div class="card radius-10">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div>
                            <h6 class="mb-0">Ciudades</h6>
                        </div>
                    </div>
                    <div class="chart-container-2 mt-4">
                        <canvas id="ciudadesGrafico"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <!--Grafico estados-->
        <div class="col-12 col-lg-4">
            <div class="card radius-10">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div>
                            <h6 class="mb-0">Estados</h6>
                        </div>
                    </div>
                    <div class="chart-container-2 mt-4">
                        <canvas id="estadosGrafico"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!--Grafico fechas-->
        <div class="col-12 col-lg-4">
            <div class="card radius-10">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div>
                            <h6 class="mb-0">Cantidad de Certificados por Fecha</h6>
                        </div>
                    </div>
                    <div class="chart-container-2 mt-4">
                        <canvas id="fechaChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
    

    <!--Grafico inspectores-->
    <div class="col-12 col-lg-4">
        <div class="card radius-10">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div>
                        <h6 class="mb-0">Cantidad de Certificados por Inspector</h6>
                    </div>
                </div>
                <div class="chart-container-2 mt-4">
                    <canvas id="inspectoresChart"></canvas>
                </div>
            </div>
        </div>
    </div>
    </div>
</div>
</div>
<?php include_once 'Views/template/footer-admin.php'; ?>
<script src="<?php echo BASE_URL; ?>assets/js/index.js"></script> 



</body>

</html>