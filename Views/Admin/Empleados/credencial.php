<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8" />
  <title>Credencial Empleado</title>
  <style>
    body {
      font-family: 'Segoe UI', sans-serif;
      background: #f0f0f0;
      display: flex;
      justify-content: center;
      padding: 30px;
    }

    .credencial, .reverso {
      width: 300px;
      height: 500px;
      border-radius: 20px;
      overflow: hidden;
      box-shadow: 0 0 15px rgba(0,0,0,0.2);
      display: flex;
      flex-direction: column;
    }

    .credencial {
      background: #ffffff;
      margin-right: 30px;
    }

    .header {
      background: #1c1e74;
      color: white;
      padding: 10px;
      text-align: center;
    }

    .header img.logo {
      width: 80px;
      margin: 10px auto 0;
    }

.body {
  flex: 1;
  background: #f7f7ff;
  padding: 15px;
  display: flex;
  flex-direction: column;
  justify-content: space-between;
}



    .foto {
      width: 100px;
      height: 100px;
      background: #1c1e74;
      border-radius: 12px;
      margin: 10px auto;
      display: flex;
      align-items: center;
      justify-content: center;
    }

    .foto img {
      width: 100%;
      height: 100%;
      border-radius: 10px;
      object-fit: cover;
    }

    .datos {
      font-size: 14px;
      margin-top: 10px;
      color: #1c1e74;
    }

    .datos div {
      margin: 5px 0;
    }

    .footer {
      background: #1c1e74;
      color: white;
      padding: 8px 15px;
      font-size: 12px;
      text-align: center;
    }

    .reverso {
      background: #1c1e74;
      color: white;
      padding: 20px;
      box-sizing: border-box;
    }

    .reverso h4 {
      margin-top: 0;
      text-align: center;
    }

    .qr {
      display: flex;
      justify-content: center;
      margin: 20px 0;
    }

    .qr img {
      width: 120px;
      height: 120px;
      border-radius: 10px;
      background: #fff;
    }

    .direccion, .rfc {
      font-size: 13px;
      line-height: 1.6;
    }
  </style>
</head>
<body>
<div class="credencial">
  <div class="header">
    <img src="<?= $logoUrl ?>" alt="Logo" class="logo"/>
  </div>
  <div class="body">
    <div class="foto">
      <img src="<?= $fotoUrl ?>" alt="Foto del empleado"/>
    </div>
    <div class="datos">
      <div><strong>Nombre:</strong> <?= $empleado['nombre_completo'] ?></div>
      <div><strong>Puesto:</strong> <?= $empleado['puesto'] ?></div>
      <div><strong>No.Empleado:</strong> <?= $empleado['employee_number'] ?></div>
      <div><strong>Departamento:</strong> <?= $empleado['departamento'] ?></div>
    </div>
    <div class="footer">
      Fecha de Emisión: <?= date('d/m/Y', strtotime($empleado['issue_date'])) ?>
    </div>
  </div>
</div>


  <div class="reverso">
    <h4>DIVISIÓN MÉXICO</h4>
    <div class="qr">
      <img src="<?= $qrUrl ?>" alt="Código QR del empleado"/>
    </div>
    <div class="direccion">
      Dirección: Cayetano Perez 240-I, Buena Vista,<br>
      Burócrata Ruiz Cortinez, 22406<br>
      Tijuana, B.C.
    </div>
    <div class="rfc" style="margin-top: 15px;">
      RFC: <?= $empleado['rfc'] ?>
    </div>
  </div>
</body>
</html>
