<?php
class AdminModel extends Query
{

    public function __construct()
    {
        parent::__construct();
    }
    public function getUsuario($correo)
    {
        $sql = "SELECT * FROM users WHERE correo = '$correo'";
        return $this->select($sql);
    }
    public function ciudadesCertificados()
    {
        // Consulta para obtener la cantidad de certificados por ciudad
        $sql = "
          SELECT a.city, COUNT(c.address_id) AS cantidad_certificados
        FROM certificates c
        JOIN addresses a ON c.address_id = a.id   
        GROUP BY a.city
        ";

        // Ejecutar la consulta y obtener los resultados
        $result = $this->selectAll($sql);

        // Obtener el total de certificados
        $totalCertificados = 0;
        foreach ($result as $row) {
            $totalCertificados += $row['cantidad_certificados'];
        }

        // Calcular el porcentaje de certificados por ciudad
        foreach ($result as &$row) {
            if ($totalCertificados > 0) {
                $row['porcentaje'] = ($row['cantidad_certificados'] / $totalCertificados) * 100;
            } else {
                $row['porcentaje'] = 0;
            }
        }

        return $result;
    }

    public function estadosCertificados()
    {
        // Consulta para obtener la cantidad de certificados por ciudad
        $sql = "
          SELECT a.state, COUNT(c.address_id) AS cantidad_certificados
        FROM certificates c
        JOIN addresses a ON c.address_id = a.id   
        GROUP BY a.state
        ";

        // Ejecutar la consulta y obtener los resultados
        $result = $this->selectAll($sql);

        // Obtener el total de certificados
        $totalCertificados = 0;
        foreach ($result as $row) {
            $totalCertificados += $row['cantidad_certificados'];
        }

   // Solo aseguramos que venga la cantidad, sin porcentaje
foreach ($result as &$row) {
    $row['cantidad'] = (int) $row['cantidad_certificados'];
}

        return $result;
    }

    public function certificadosTotales()
    {
        // Consulta para obtener la cantidad de certificados por ciudad
        $sql = "SELECT COUNT(cert_number) AS total
        FROM certificates
              ";

        // Ejecutar la consulta y obtener los resultados
        return $this->select($sql);

    }
    public function ciudadesTotales()
    {
        // Consulta para obtener la cantidad de certificados por ciudad
        $sql = "SELECT COUNT(city) AS total
        FROM addresses
              ";

        // Ejecutar la consulta y obtener los resultados
        return $this->select($sql);

    }

    public function estadosTotales()
    {
        // Consulta para obtener la cantidad de certificados por ciudad
        $sql = "SELECT COUNT(state) AS total
        FROM addresses
              ";

        // Ejecutar la consulta y obtener los resultados
        return $this->select($sql);

    }
}
