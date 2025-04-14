<?php include_once 'Views/template/header-admin.php'; ?>

<button class="btn btn-primary mb-2" type="button" id="nuevo_registro">Nuevo</button>

<div class="card">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered table-striped table-hover" style="width: 100%;" id="tblUsuarios">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Username</th>
                        <th>First_name</th>
                        <th>Apellido</th>
                        <th>Correo</th>
                        <th>Telefono</th>
                        <th>Rol</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                </tbody>
            </table>
        </div>
    </div>
</div>

<div id="nuevoModal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="my-modal-title" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header bg-primary">
                <h5 class="modal-title" id="titleModal"></h5>
                <button class="btn-close" data-bs-dismiss="modal" aria-label="Close">
                </button>
            </div>
            <form id="frmRegistro">
                <div class="modal-body">
                    <input type="hidden" id="id" name="id">
                    <div class="form-group mb-2">
                        <label for="nombre">Nombre de usuario</label>
                        <input id="username" class="form-control" type="text" name="username" placeholder="Nombre Usuario">
                    </div>
                    <div class="form-group mb-2">
                        <label for="nombre">Nombres</label>
                        <input id="nombre" class="form-control" type="text" name="nombre" placeholder="Nombres">
                    </div>
                    <div class="form-group mb-2">
                        <label for="apellido">Apellidos</label>
                        <input id="apellido" class="form-control" type="text" name="apellido" placeholder="Apellidos">
                    </div>
                    <div class="form-group mb-2">
                        <label for="apellido">Telefono</label>
                        <input id="phone" class="form-control" type="text" name="phone" placeholder="6631234545">
                    </div>
                    <div class="form-group mb-2">
                        <label for="correo">Correo</label>
                        <input id="correo" class="form-control" type="email" name="correo" placeholder="Correo Electrónico">
                    </div>
                    <div class="form-group mb-2">
                        <label for="clave">Contraseña</label>
                        <input id="clave" class="form-control" type="password" name="clave" placeholder="Contraseña">
                    </div>
                    <div class="form-group mb-2">
                        <label for="rol">Rol</label>
                        <select id="rol" class="form-control" name="rol">
                            <option value="">Seleccione un rol</option>
                            <option value="1">Administrador</option>
                            <option value="2">Manager</option>
                            <option value="3">Capturista</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button class="btn btn-primary" type="submit" id="btnAccion">Registrar</button>
                    <button class="btn btn-danger" type="button" data-bs-dismiss="modal">Cancelar</button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php include_once 'Views/template/footer-admin.php'; ?>

<script src="<?php echo BASE_URL . 'assets/js/modulos/usuarios.js'; ?>"></script>

</body>

</html>