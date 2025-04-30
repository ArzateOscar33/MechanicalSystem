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

    public function getRolUsuario($userId)
{
    $sql = "SELECT role_id FROM user_roles WHERE user_id = $userId";
    $data = $this->select($sql);
    return $data ? $data['role_id'] : null;
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
        $sql = "SELECT COUNT(DISTINCT  city) AS total
        FROM addresses
              ";

        // Ejecutar la consulta y obtener los resultados
        return $this->select($sql);

    }
    

    public function estadosTotales()
    {
        // Consulta para obtener la cantidad estados
        $sql = "SELECT COUNT(DISTINCT state) AS total
        FROM addresses
              ";

        // Ejecutar la consulta y obtener los resultados
        return $this->select($sql);

    }

    public function certificadosPorMes()
    {
        $sql = "SELECT 
        YEAR(test_date) AS anio, 
        MONTH(test_date) AS mes, 
        COUNT(*) AS total
        FROM certificates
        GROUP BY anio, mes
        ORDER BY anio, mes";
        return $this->selectAll($sql);
    }


    public function certificadosPorInspector()
    {
        // Consulta para obtener la cantidad de certificados por ciudad
        $sql = "SELECT i.name AS inspector_name, COUNT(*) AS total 
        FROM certificates c
        JOIN inspectors i ON c.inspector_id = i.id
        GROUP BY i.name";

        return $this->selectAll($sql);

    }
    public function inspectoresTotales()
    {
        // Consulta para obtener la cantidad de certificados por ciudad
        $sql = "SELECT COUNT(*) AS total FROM inspectors;";
    
        return $this->select($sql);

    }

    public function certificadosPorDia($anio, $mes, $estado = '', $ciudad = '')
        {
            $sql = "SELECT DATE(c.test_date) as dia, COUNT(*) as total
                    FROM certificates c
                    INNER JOIN addresses a ON c.address_id = a.id
                    WHERE YEAR(c.test_date) = $anio AND MONTH(c.test_date) = $mes";

            if (!empty($estado)) {
                $sql .= " AND a.state = '$estado'";
            }

            if (!empty($ciudad)) {
                $sql .= " AND a.city = '$ciudad'";
            }

            $sql .= " GROUP BY dia ORDER BY dia ASC";

            return $this->selectAll($sql);
        }
    public function inspectoresDisponibles()
    {
        $sql = "SELECT id, name AS inspector_name FROM inspectors ORDER BY name";
    
        return $this->selectAll($sql);
    }

    public function certificadosPorMesInspector($anio, $inspector)
    {
        $sql = "SELECT MONTH(c.test_date) AS mes, COUNT(*) AS total
        FROM certificates c
        JOIN inspectors i ON c.inspector_id = i.id
        WHERE YEAR(c.test_date) = $anio AND i.name = '$inspector'
        GROUP BY mes ORDER BY mes";

        return $this->selectAll($sql);
    }

    public function lugarInspector($inspector)
{
    $sql = "SELECT a.state, a.city, COUNT(*) AS total
            FROM certificates c
            INNER JOIN addresses a ON c.address_id = a.id
            INNER JOIN inspectors i ON c.inspector_id = i.id
            WHERE i.name = '$inspector'
            GROUP BY a.state, a.city
            ORDER BY total DESC
            LIMIT 1";
    
    return $this->select($sql);
}

public function certificadosPorCiudadPorInspector($inspector)
{
    $sql = "SELECT a.city, COUNT(c.address_id) AS cantidad_certificados
            FROM certificates c
            JOIN addresses a ON c.address_id = a.id
            JOIN inspectors i ON c.inspector_id = i.id
            WHERE i.name = '$inspector'
            GROUP BY a.city";
    return $this->selectAll($sql);
}

    
    
}
