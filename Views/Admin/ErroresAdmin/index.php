<?php include_once 'Views/template/header-admin.php'; ?>
<!-- Modal para editar certificados -->
<div id="modalError" class="modal" tabindex="-1" role="dialog" aria-labelledby="my-modal-title" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header bg-primary">
                <h5 class="modal-title" id="titleModal"></h5>
                    <button class="btn-close" data-bs-dismiss="modal" aria-label="Close">
                    </button>
            </div>
            <form id="frmEditar">
                <div class="modal-body">
                    <input type="hidden" id="id" name="id">
                    <div class="form-group mb-2">
                        <label for="nombre">Certificado</label>
                        <input id="cert_number" class="form-control" type="text" name="cert_number" placeholder="Certificado">
                    </div>
                    <div class="form-group mb-2">
                        <label for="inspector">Inspector</label>
                        <select id="inspector" class="form-control" name="inspector"> </select>
                    </div>
                    <div class="form-group mb-2">
                        <label for="nombre">VIN</label>
                        <input id="vin" class="form-control" type="text" name="vin" placeholder="VIN">
                    </div>
                    <div class="form-group mb-2">
                        <label for="apellido">Propietario</label>
                        <input id="owner_name" class="form-control" type="text" name="owner_name" placeholder="Propietario">
                    </div>
    
                    <div class="form-group mb-2">
                        <label for="make">Marca</label>
                        <input id="make" class="form-control" type="text" name="make" placeholder="Marca">
                    </div>
                    <div class="form-group mb-2">
                        <label for="correo">Modelo</label>
                        <input id="model" class="form-control" type="text" name="model" placeholder="Modelo">
                    </div>
                    <div class="form-group mb-2">
                        <label for="ciudad">Ciudad</label>
                        <input id="city" class="form-control" type="text" name="city" placeholder="Ciudad">
                    </div>
                    <div class="form-group mb-2">
                        <label for="state">Estado</label>
                        <input id="state" class="form-control" type="text" name="state" placeholder="Estado">
                    </div>
                    <div class="form-group mb-2">
                        <label for="zip">Codigo Postal</label>
                        <input id="zip" class="form-control" type="text" name="zip" placeholder="Código Postal">
                    </div>
                    <div class="form-group mb-2">
                        <label for="mfg_in">Fabricado En </label>
                        <input id="mfg_in" class="form-control" type="text" name="mfg_in" placeholder="Fabricado En">
                    </div>
                </div>
                <div class="modal-footer">
            <button class="btn btn-primary" type="submit" id="btnAccion">Modificar</button>
            <button class="btn btn-danger" type="button" data-bs-dismiss="modal">Cancelar</button>
        </div>
        </div>

        </form>
    </div>
</div>
 

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
                <select id="filterOwner" class="form-control">
                    <option value="">Todos</option>
                </select>
            </div>
            <div class="col-md-3">
                <label for="filterInspector">Inspector:</label>
                <select id="filterInspector" class="form-control">
                    <option value="">Todos</option>
                </select>
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
                <select id="filterMfgIn" class="form-control">
                    <option value="">Todos</option>
                </select>
            </div>
        </div>




        <div class="table-responsive">
            <table class="table table-bordered table-striped table-hover" style="width: 100%;" id="tblErrores">
                <thead>
                    <tr>
                        <th>Certificado</th>
                        <th>Usuario</th>
                        <th>Campo donde se produjo el error</th>
                        <th>Valor actual del campo</th>
                        <th>Valor Propuesto</th>
                        <th>Razon del Error</th>
                        <th>Estatus del Error</th>
                        <th>Revisado por</th>
                        <th>Revisado el</th>
                        <th>Fecha de la Solicitud</th>
                        <th>Fecha de Modificacion</th>
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

<script src="<?php echo BASE_URL . 'assets/js/modulos/erroresAdmin.js'; ?>"></script>

</body>

</html>