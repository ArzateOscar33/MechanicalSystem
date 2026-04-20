<?php include_once 'Views/template/header-admin.php'; ?>

<div class="card">
    <div class="card-body">

        <div class="row mb-3">
            <div class="col-md-3">
                <label for="filterCertNumber">Certificado:</label>
                <input type="text" id="filterCertNumber" class="form-control" placeholder="Buscar por certificado">
            </div>

            <div class="col-md-3">
                <label for="filterVin">VIN:</label>
                <input type="text" id="filterVin" class="form-control" placeholder="Buscar por VIN">
            </div>

            <div class="col-md-3">
                <label for="filterCliente">Cliente:</label>
                <select id="filterCliente" class="form-control">
                    <option value="">Todos</option>
                </select>
            </div>

            <div class="col-md-3">
                <label for="filterRealizadoPor">Realizado por:</label>
                <select id="filterRealizadoPor" class="form-control">
                    <option value="">Todos</option>
                </select>
            </div>
        </div>

        <div class="row mb-3">
            <div class="col-md-2">
                <label for="filterSecomext">Secomext:</label>
                <select id="filterSecomext" class="form-control">
                    <option value="">Todos</option>
                    <option value="SI">SI</option>
                    <option value="NO">NO</option>
                </select>
            </div>

            <div class="col-md-2">
                <label for="filterBddm">BDD M:</label>
                <select id="filterBddm" class="form-control">
                    <option value="">Todos</option>
                    <option value="SI">SI</option>
                    <option value="NO">NO</option>
                </select>
            </div>

            <div class="col-md-2">
                <label for="filterFechaInicio">Fecha inicio:</label>
                <input type="date" id="filterFechaInicio" class="form-control">
            </div>

            <div class="col-md-2">
                <label for="filterFechaFin">Fecha fin:</label>
                <input type="date" id="filterFechaFin" class="form-control">
            </div>

            <div class="col-md-4">
                <label for="filterComentario">Comentario:</label>
                <input type="text" id="filterComentario" class="form-control" placeholder="Buscar en comentario">
            </div>
        </div>

        <div class="table-responsive">
            <table class="table table-bordered table-striped table-hover" style="width: 100%;" id="tblReporteCertificados">
                <thead>
                    <tr>
                        <th># CERT</th>
                        <th>VIN</th>
                        <th>CLIENTE</th>
                        <th>REALIZADO POR</th>
                        <th>SECOMEXT</th>
                        <th>BDD M</th>
                        <th>FECHA</th>
                        <th>COMENTARIO</th>
                    </tr>
                </thead>
                <tbody>
                </tbody>
            </table>
        </div>

    </div>
</div>

<?php include_once 'Views/template/footer-admin.php'; ?>

<script src="<?php echo BASE_URL . 'assets/js/modulos/reportes.js'; ?>"></script>

</body>

</html>