<?php include_once 'Views/template/header-admin.php'; ?>


<div class="card">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered table-striped table-hover" style="width: 100%;" id="tblImportaciones">
                <thead>
                    <tr>
                        <th>id</th>
                        <th>Nombre del Archivo</th>
                        <th>Registros Exitosos</th>
                        <th>Registros Fallidos</th> 
                        <th>Registrado por </th>
                        <th>Estado</th>
                        <th>Mensaje de Error</th>
                        <th>Fecha de Creacion</th> 
                    </tr>
                </thead>
                <tbody>
                </tbody>
            </table>
        </div>
    </div>
</div>
 
 

<?php include_once 'Views/template/footer-admin.php'; ?>

<script src="<?php echo BASE_URL . 'assets/js/modulos/importaciones.js'; ?>"></script>

</body>

</html>