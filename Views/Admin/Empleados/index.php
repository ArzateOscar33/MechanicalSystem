<?php include_once 'Views/template/header-admin.php'; ?>

<!-- navbar tabs -->
<ul class="nav nav-tabs" id="myTab" role="tablist">
    <li class="nav-item" role="presentation">
        <button class="nav-link active" id="home-tab" data-bs-toggle="tab" data-bs-target="#listadoEmpleados"
            type="button" role="tab" aria-controls="listadoEmpleados" aria-selected="true">Listado de Empleados</button>
    </li>
    <li class="nav-item" role="presentation">
        <button class="nav-link" id="profile-tab" data-bs-toggle="tab" data-bs-target="#detalleEmpleados" type="button"
            role="tab" aria-controls="detalleEmpleados" aria-selected="false">Detalle Empleado</button>
    </li>
    <li class="nav-item" role="presentation">
        <button class="nav-link" id="profile-tab" data-bs-toggle="tab" data-bs-target="#nuevoEmpleado" type="button"
            role="tab" aria-controls="nuevoEmpleado" aria-selected="false">Nuevo Empleado</button>
    </li>
</ul>

<!-- contenido navbar tabs -->
<div class="tab-content" id="myTabContent">
    <div class="tab-pane fade show active" id="listadoEmpleados" role="tabpanel" aria-labelledby="home-tab">
        <!-- Contenido de la tabla -->
        <div class="card">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered table-striped table-hover" style="width: 100%;"
                        id="tblEmpleados">
                        <thead>
                            <tr>
                                <th>Numero de Empleado</th>
                                <th>Nombre Completo</th>

                                <th>Telefono</th>

                                <th>Genero</th>
                                <th>Departamento</th>
                                <th>Puesto</th>
                                <th>Fecha de Ingreso</th>
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

    <div class="tab-pane fade" id="detalleEmpleados" role="tabpanel" aria-labelledby="profile-tab">

        <!-- Card 1: Filtros para buscar al empleado -->
        <div class="card mb-3">
            <div class="card-header bg-primary text-white">
                Seleccionar Empleado
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-4 mb-2">
                        <label for="filtro_departamento" class="form-label">Departamento</label>
                        <select id="filtro_departamento" class="form-select">
                            <option value="">Seleccione</option>
                        </select>
                    </div>
                    <div class="col-md-4 mb-2">
                        <label for="filtro_empleado" class="form-label">Empleado</label>
                        <select id="filtro_empleado" class="form-select">
                            <option value="">Seleccione un departamento primero</option>
                        </select>
                    </div>
                    <div class="col-md-4 mb-2">
                        <label for="buscar_numero" class="form-label">Buscar por Número</label>
                        <div class="input-group">
                            <input type="text" id="buscar_numero" class="form-control" placeholder="Ej: 20240513001">
                            <button class="btn btn-outline-secondary" type="button" id="btnBuscarNumero">
                                Buscar
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Card 2: Información del empleado -->
        <div class="card">
            <div class="card-header bg-secondary text-white">
                Detalle del Empleado
            </div>
            <div class="card-body">

                <!-- Primera fila solo con la imagen -->
                <div class="row justify-content-center mb-3">
                    <div class="col-md-3 text-center">
                        <img id="imgEmpleado" src="" alt="Fotografía del empleado" width="150" height="150"
                            class="img-thumbnail rounded">
                    </div>
                </div>

                <!-- Formulario de detalles -->
                <form id="frmDetalleEmpleado">
                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label class="form-label">No. de Empleado</label>
                            <input type="text" id="detalle_employee_number" name="employee_number" class="form-control" readonly>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Nombre(s)</label>
                            <input type="text" id="detalle_first_name" name="first_name" class="form-control" readonly>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Primer Apellido</label>
                            <input type="text" id="detalle_last_name" name="last_name" class="form-control" readonly>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Segundo Apellido</label>
                            <input type="text" id="detalle_second_last_name" name="second_last_name" class="form-control" readonly>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label">CURP</label>
                            <input type="text" id="detalle_curp" name="curp" class="form-control" readonly>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label">RFC</label>
                            <input type="text" id="detalle_rfc" name="rfc" class="form-control" readonly>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Fecha de Nacimiento</label>
                            <input type="date" id="detalle_birth_date" name="birth_date" class="form-control" readonly>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Género</label>
                            <select id="detalle_gender" name="gender" class="form-select" disabled>
                                <option value="">Seleccione</option>
                                <option value="Masculino">Masculino</option>
                                <option value="Femenino">Femenino</option>
                                <option value="Otro">Otro</option>
                            </select>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Teléfono</label>
                            <input type="text" id="detalle_phone" name="phone" class="form-control" readonly>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Correo Electrónico</label>
                            <input type="email" id="detalle_email" name="email" class="form-control" readonly>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Fotografía</label>
                            <input type="file" id="detalle_photo" name="photo" class="form-control" accept="image/*" disabled>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Departamento</label>
                            <select id="detalle_department_id" name="department_id" class="form-select" disabled>
                                <option value="">Seleccione</option>
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Puesto</label>
                            <select id="detalle_position_id" name="position_id" class="form-select" disabled>
                                <option value="">Seleccione un departamento primero</option>
                            </select>
                        </div>
                    </div>

                    <div class="text-end d-flex justify-content-between">
                        <button type="button" class="btn btn-secondary" id="btnEditarEmpleado">Editar</button>
                        <button type="button" class="btn btn-primary d-none" id="btnConfirmarEdicion">Confirmar Edición</button>
                        <button type="button" class="btn btn-success" id="btnGenerarCredencial">Generar Identificación</button>
                    </div>
                </form>

            </div>
        </div>

    </div>

    <div class="tab-pane fade" id="nuevoEmpleado" role="tabpanel" aria-labelledby="profile-tab">
        <div class="tab-pane fade show active" id="NuevoEmpleado" role="tabpanel" aria-labelledby="home-tab">
            <!-- Contenido del formulario -->
            <div class="card">
                <div class="card-body">
                    <form id="frmEmpleados">
                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <label for="employee_number" class="form-label">No. de Empleado</label>
                                <input type="text" id="employee_number" name="employee_number" class="form-control"
                                    readonly>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label for="first_name" class="form-label">Nombre(s)</label>
                                <input type="text" id="first_name" name="first_name" class="form-control" required>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label for="last_name" class="form-label">Primer Apellido</label>
                                <input type="text" id="last_name" name="last_name" class="form-control" required>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label for="second_last_name" class="form-label">Segundo Apellido</label>
                                <input type="text" id="second_last_name" name="second_last_name" class="form-control"
                                    required>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label for="curp" class="form-label">CURP</label>
                                <input type="text" id="curp" name="curp" maxlength="18"
                                    class="form-control text-uppercase" required>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label for="rfc" class="form-label">RFC</label>
                                <input type="text" id="rfc" name="rfc" maxlength="13"
                                    class="form-control text-uppercase" required>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label for="birth_date" class="form-label">Fecha de Nacimiento</label>
                                <input type="date" id="birth_date" name="birth_date" class="form-control">
                            </div>
                            <div class="col-md-4 mb-3">
                                <label for="gender" class="form-label">Género</label>
                                <select id="gender" name="gender" class="form-select">
                                    <option value="">Seleccione</option>
                                    <option value="Masculino">Masculino</option>
                                    <option value="Femenino">Femenino</option>
                                    <option value="Otro">Otro</option>
                                </select>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label for="phone" class="form-label">Teléfono</label>
                                <input type="text" id="phone" name="phone" class="form-control">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="email" class="form-label">Correo Electrónico</label>
                                <input type="email" id="email" name="email" class="form-control">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="photo" class="form-label">Fotografía</label>
                                <input type="file" id="photo" name="photo" class="form-control" accept="image/*">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="department_id" class="form-label">Departamento</label>
                                <select id="department_id" name="department_id" class="form-select" required>
                                    <option value="">Seleccione</option>
                                    <!-- Se llenará dinámicamente -->
                                </select>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="position_id" class="form-label">Puesto</label>
                                <select id="position_id" name="position_id" class="form-select" required>
                                    <option value="">Seleccione un departamento primero</option>
                                    <!-- Se llenará dinámicamente con JS -->
                                </select>
                            </div>
                        </div>
                        <div class="text-end">
                            <button type="submit" class="btn btn-primary">Registrar Empleado</button>
                        </div>
                    </form>

                </div>
            </div>
        </div>
    </div>
</div>

<?php include_once 'Views/template/footer-admin.php'; ?>

<script src="<?php echo BASE_URL . 'assets/js/modulos/controlEmpleados.js'; ?>"></script>

<script src="<?php echo BASE_URL . 'assets/js/modulos/detalleEmpleados.js'; ?>"></script>

</body>

</html>