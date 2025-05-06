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
            <form id="frmEditar" enctype="multipart/form-data">
                <div class="modal-body">
                    <input type="hidden" id="id" name="id">
                    <input type="hidden" id="field_name" name="field_name">

                    <div class="form-group mb-2 editable-field" id="group_cert_number">
                        <label for="nombre">Certificado</label>
                        <input id="cert_number" class="form-control" type="text" name="cert_number" placeholder="Certificado" readonly>
                    </div>
                    <div class="form-group mb-2 editable-field" id="group_inspector">
                        <label for="nombre">Inspector</label>
                        <input id="inspector" class="form-control" type="text" name="inspector" placeholder="Inspector" readonly>
                    </div>
                    <div class="form-group mb-2 editable-field" id="group_vin">
                        <label for="nombre">VIN</label>
                        <input id="vin" class="form-control" type="text" name="vin" placeholder="VIN">
                    </div>
                    <div class="form-group mb-2 editable-field" id="group_owner_name">
                        <label for="apellido">Propietario</label>
                        <input id="owner_name" class="form-control" type="text" name="owner_name" placeholder="Propietario">
                    </div>

                    <div class="form-group mb-2 editable-field" id="group_make">
                        <label for="make">Marca</label>
                        <input id="make" class="form-control" type="text" name="make" placeholder="Marca">
                    </div>
                    <div class="form-group mb-2 editable-field" id="group_model">
                        <label for="correo">Modelo</label>
                        <input id="model" class="form-control" type="text" name="model" placeholder="Modelo">
                    </div>
                    <div class="form-group mb-2 editable-field" id="group_year">
                        <label for="correo">Año</label>
                        <input id="year" class="form-control" type="text" name="year" placeholder="Año">
                    </div>
                    <div class="form-group mb-2 editable-field" id="group_license_plate">
                        <label for="correo">Placa</label>
                        <input id="license_plate" class="form-control" type="text" name="license_plate" placeholder="Placa">
                    </div>
                    <div class="form-group mb-2 editable-field" id="group_street">
                        <label for="ciudad">Calle</label>
                        <input id="street" class="form-control" type="text" name="street" placeholder="Calle">
                    </div>
                    <div class="form-group mb-2 editable-field" id="group_city">
                        <label for="ciudad">Ciudad</label>
                        <input id="city" class="form-control" type="text" name="city" placeholder="Ciudad" readonly>
                    </div>
                    <div class="form-group mb-2 editable-field" id="group_state">
                        <label for="state">Estado</label>
                        <input id="state" class="form-control" type="text" name="state" placeholder="Estado" readonly>
                    </div>
                    <div class="form-group mb-2 editable-field" id="group_zip">
                        <label for="zip">Codigo Postal</label>
                        <input id="zip" class="form-control" type="text" name="zip" placeholder="Código Postal" readonly>
                    </div>
                    <div class="form-group mb-2 editable-field" id="group_mfg_in">
                        <label for="mfg_in">Fabricado En </label>
                        <input id="mfg_in" class="form-control" type="text" name="mfg_in" placeholder="Fabricado En">
                    </div>
                    <div class="form-group mb-2 editable-field" id="group_images">
                        <label for="mfg_in">Actualizar Fotos </label>
                        <input type="file" class="form-control mb-2" name="imagenes[]" id="imagenes" multiple accept="image/*">

                    </div>
                    <div class="form-group mb-2 editable-field" id="group_proposed_value">
                        <label for="proposed_value">Valor Sugerido</label>
                        <input id="proposed_value" class="form-control" type="text" name="proposed_value" placeholder="Valor Sugerido" readonly>
                    </div>
                    <div class="mb-3" id="group_images_preview" style="display: none;">
                        <label class="form-label">Imágenes Sugeridas</label>
                        <div id="imagenes_sugeridas" class="d-flex flex-wrap gap-2"></div>
                        <button type="button" class="btn btn-secondary mt-2" id="btnUsarSugeridas">
                            Usar imágenes sugeridas
                        </button>
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

<!-- navbar tabs -->
<ul class="nav nav-tabs" id="myTab" role="tablist">
    <li class="nav-item" role="presentation">
        <button class="nav-link active" id="home-tab" data-bs-toggle="tab" data-bs-target="#erroresPendientes" type="button" role="tab" aria-controls="erroresPendientes" aria-selected="true">Errores Pendientes</button>
    </li>
    <li class="nav-item" role="presentation">
        <button class="nav-link" id="profile-tab" data-bs-toggle="tab" data-bs-target="#erroresResueltos" type="button" role="tab" aria-controls="erroresResueltos" aria-selected="false">Errores Resueltos</button>
    </li>
</ul>
<!-- contenido navbar tabs -->
<div class="tab-content" id="myTabContent">
    <div class="tab-pane fade show active" id="erroresPendientes" role="tabpanel" aria-labelledby="home-tab">
        <!-- Contenido de la tabla -->
        <div class="card">
            <div class="card-body">
                <!-- Contenido de la tabla errores pendientes -->
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
                                <th>Fecha de la Solicitud</th>
                                <th></th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>


    <div class="tab-pane fade" id="erroresResueltos" role="tabpanel" aria-labelledby="profile-tab">
        <div class="tab-pane fade show active" id="erroresPendientes" role="tabpanel" aria-labelledby="home-tab">
            <!-- Contenido de la tabla -->
            <div class="card">
                <div class="card-body">
                    <!-- Contenido de la tabla errores pendientes -->
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped table-hover" style="width: 100%;" id="tblErroresResueltos">
                            <thead>
                                <tr>
                                    <th>Certificado</th>
                                    <th>Usuario</th>
                                    <th>Campo donde se produjo el error</th>
                                    <th>Valor Corregido</th>
                                    <th>Estatus del Error</th>
                                    <th>Fecha de la Solicitud</th>
                                    <th>Fecha de Resolucion</th>
                                </tr>
                            </thead>
                            <tbody>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>



<?php include_once 'Views/template/footer-admin.php'; ?>

<script src="<?php echo BASE_URL . 'assets/js/modulos/erroresAdmin.js'; ?>"></script>

</body>

</html>