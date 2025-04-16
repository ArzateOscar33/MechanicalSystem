<?php include_once 'Views/template/header-admin.php'; ?>


<div class="card">
    <div class="card-body">
    <div class="row mb-3">
    <div class="col-md-3">
        <label for="filterCertNumber">Certificado:</label>
        <input type="text" id="filterCertNumber" class="form-control" placeholder="Buscar por número">
    </div>
    <div class="col-md-3">
        <label for="filterVin">VIN:</label>
        <input type="text" id="filterVin" class="form-control" placeholder="Buscar por VIN">
    </div>
    <div class="col-md-3">
        <label for="filterOwner">Propietario:</label>
        <input type="text" id="filterOwner" class="form-control" placeholder="Buscar por dueño">
    </div>
    <div class="col-md-3">
        <label for="filterInspector">Inspector:</label>
        <input type="text" id="filterInspector" class="form-control" placeholder="Buscar por inspector">
    </div>
</div>
<div class="row mb-3">
    <div class="col-md-3">
        <label for="filterMake">Marca:</label>
        <input type="text" id="filterMake" class="form-control" placeholder="Buscar por marca">
    </div>
    <div class="col-md-3">
        <label for="filterCiudad">Ciudad:</label>
        <select id="filterCiudad" class="form-control">
            <option value="">Todas</option>
        </select>
    </div>
    <div class="col-md-3">
        <label for="filterEstado">Estado:</label>
        <select id="filterEstado" class="form-control">
            <option value="">Todos</option>
        </select>
    </div> 
    <div class="col-md-3">
        <label for="filterMfgIn">Manufacturado en:</label>
        <input type="text" id="filterMfgIn" class="form-control" placeholder="Buscar por origen">
    </div>
</div>
 
        <div class="table-responsive">
            <table class="table table-bordered table-striped table-hover" style="width: 100%;" id="tblCertificados">
                <thead>
                    <tr>
                        <th>Certificado</th>
                        <th>VIN</th>
                        <th>Propietario</th>
                        <th>Inspector</th>
                        <th>Marca</th>
                        <th>Ruta del ZIP</th>
                        <th>Ciudad</th>
                        <th>Estado</th>
                        <th>Codigo Postal</th>
                        <th>Fabricado en </th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                </tbody>
            </table>
        </div>
    </div>
</div>



<?php include_once 'Views/template/footer-admin.php'; ?>

<script src="<?php echo BASE_URL . 'assets/js/modulos/buscarcertificado.js'; ?>"></script>

</body>

</html>