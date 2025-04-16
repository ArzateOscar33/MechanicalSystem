<?php include_once 'Views/template/header-admin.php'; ?>


<div class="card">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered table-striped table-hover" style="width: 100%;" id="tblCertificados">
                <thead>
                    <tr>
                        <th>cert_number</th>
                        <th>VIN</th>
                        <th>ZIP File Path</th> 
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

<script src="<?php echo BASE_URL . 'assets/js/modulos/descargarcertificados.js'; ?>"></script>

</body>

</html>