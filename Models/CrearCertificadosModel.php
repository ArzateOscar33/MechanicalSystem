<?php
class CrearCertificadosModel extends Query
{
    public function __construct()
    {
        parent::__construct();
    }

    // Método para crear un certificado en la base de datos
    public function crearCertificado($vin, $year, $marca, $modelo, $fabricado_en, $placa, $propietario, $odometro, 
                                       $monitor_fallo_encendido, $monitor_sistema_combustible, $numero_certificado, $latitud, 
                                       $longitud, $monitor_integral_catalizador, $monitor_catalizador, $monitor_sensor_c2, 
                                       $resultado_prueba, $ebitn, $inspector, $firma_inspector, $fecha, $fecha_expiracion)
    {
        // Preparamos la consulta SQL para insertar los datos en la base de datos
        $sql = "INSERT INTO certificados (vin, year, marca, modelo, fabricado_en, placa, propietario, odometro, 
                monitor_fallo_encendido, monitor_sistema_combustible, numero_certificado, latitud, longitud, 
                monitor_integral_catalizador, monitor_catalizador, monitor_sensor_c2, resultado_prueba, ebitn, 
                inspector, firma_inspector, fecha, fecha_expiracion) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

        // Creamos un array con los parámetros que se van a insertar
        $array = array($vin, $year, $marca, $modelo, $fabricado_en, $placa, $propietario, $odometro, 
                       $monitor_fallo_encendido, $monitor_sistema_combustible, $numero_certificado, $latitud, 
                       $longitud, $monitor_integral_catalizador, $monitor_catalizador, $monitor_sensor_c2, 
                       $resultado_prueba, $ebitn, $inspector, $firma_inspector, $fecha, $fecha_expiracion);

        // Ejecutamos la consulta de inserción
        return $this->insertar($sql, $array);
    }
}
?>
