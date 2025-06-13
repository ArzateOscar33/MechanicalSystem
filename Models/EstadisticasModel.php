<?php
class EstadisticasModel extends Query
{
    public function __construct()
    {
        parent::__construct();
    }

    public function getUsuario($correo)
    {
        $sql = "SELECT * FROM users WHERE correo = ?";
        return $this->select($sql, [$correo]);
    }

    public function getRolUsuario($userId)
    {
        $sql = "SELECT role_id FROM user_roles WHERE user_id = ?";
        $data = $this->select($sql, [$userId]);
        return $data ? $data['role_id'] : null;
    }

    public function ciudadesCertificados()
    {
        $sql = "
          SELECT a.city, COUNT(c.address_id) AS cantidad_certificados
          FROM certificates c
          JOIN addresses a ON c.address_id = a.id   
          GROUP BY a.city
        ";

        $result = $this->selectAll($sql);

        $totalCertificados = 0;
        foreach ($result as $row) {
            $totalCertificados += $row['cantidad_certificados'];
        }

        foreach ($result as &$row) {
            $row['porcentaje'] = ($totalCertificados > 0)
                ? ($row['cantidad_certificados'] / $totalCertificados) * 100
                : 0;
        }

        return $result;
    }

    public function estadosCertificados()
    {
        $sql = "
          SELECT a.state, COUNT(c.address_id) AS cantidad_certificados
          FROM certificates c
          JOIN addresses a ON c.address_id = a.id   
          GROUP BY a.state
        ";

        $result = $this->selectAll($sql);

        $totalCertificados = 0;
        foreach ($result as $row) {
            $totalCertificados += $row['cantidad_certificados'];
        }

        foreach ($result as &$row) {
            $row['cantidad'] = (int) $row['cantidad_certificados'];
        }

        return $result;
    }

    public function certificadosTotales()
    {
        $sql = "SELECT COUNT(cert_number) AS total FROM certificates";
        return $this->select($sql);
    }

    public function ciudadesTotales()
    {
        $sql = "SELECT COUNT(DISTINCT city) AS total FROM addresses";
        return $this->select($sql);
    }

    public function estadosTotales()
    {
        $sql = "SELECT COUNT(DISTINCT state) AS total FROM addresses";
        return $this->select($sql);
    }

    public function certificadosPorMes()
    {
        $sql = "
            SELECT YEAR(test_date) AS anio, MONTH(test_date) AS mes, COUNT(*) AS total
            FROM certificates
            GROUP BY anio, mes
            ORDER BY anio, mes
        ";
        return $this->selectAll($sql);
    }

    public function certificadosPorInspector()
    {
        $sql = "
            SELECT i.name AS inspector_name, COUNT(*) AS total 
            FROM certificates c
            JOIN inspectors i ON c.inspector_id = i.id
            GROUP BY i.name
        ";

        return $this->selectAll($sql);
    }

    public function inspectoresTotales()
    {
        $sql = "SELECT COUNT(*) AS total FROM inspectors";
        return $this->select($sql);
    }

    public function certificadosPorDia($anio, $mes, $estado = '', $ciudad = '')
    {
        $sql = "SELECT DATE(c.test_date) as dia, COUNT(*) as total
                FROM certificates c
                INNER JOIN addresses a ON c.address_id = a.id
                WHERE YEAR(c.test_date) = ? AND MONTH(c.test_date) = ?";

        $params = [$anio, $mes];

        if (!empty($estado)) {
            $sql .= " AND a.state = ?";
            $params[] = $estado;
        }

        if (!empty($ciudad)) {
            $sql .= " AND a.city = ?";
            $params[] = $ciudad;
        }

        $sql .= " GROUP BY dia ORDER BY dia ASC";

        return $this->selectAll($sql, $params);
    }

    public function inspectoresDisponibles()
    {
        $sql = "SELECT id, name AS inspector_name FROM inspectors ORDER BY name";
        return $this->selectAll($sql);
    }

    public function certificadosPorMesInspector($anio, $inspector)
    {
        $sql = "
            SELECT MONTH(c.test_date) AS mes, COUNT(*) AS total
            FROM certificates c
            JOIN inspectors i ON c.inspector_id = i.id
            WHERE YEAR(c.test_date) = ? AND i.name = ?
            GROUP BY mes ORDER BY mes
        ";

        return $this->selectAll($sql, [$anio, $inspector]);
    }

    public function lugarInspector($inspector)
    {
        $sql = "
            SELECT a.state, a.city, COUNT(*) AS total
            FROM certificates c
            INNER JOIN addresses a ON c.address_id = a.id
            INNER JOIN inspectors i ON c.inspector_id = i.id
            WHERE i.name = ?
            GROUP BY a.state, a.city
            ORDER BY total DESC
            LIMIT 1
            
        ";

        return $this->select($sql, [$inspector]);
    }

    public function certificadosPorCiudadPorInspector($inspector)
    {
        $sql = "
            SELECT a.city, COUNT(c.address_id) AS cantidad_certificados
            FROM certificates c
            JOIN addresses a ON c.address_id = a.id
            JOIN inspectors i ON c.inspector_id = i.id
            WHERE i.name = ?
            GROUP BY a.city
        ";

        return $this->selectAll($sql, [$inspector]);
    }

    public function certificadosPorInspectorPorFecha($fecha)
{
    $sql = "
        SELECT i.name AS inspector_name, COUNT(*) AS total
        FROM certificates c
        INNER JOIN inspectors i ON c.inspector_id = i.id
        WHERE DATE(c.test_date) = ?
        GROUP BY i.name
    ";
    return $this->selectAll($sql, [$fecha]);
}
public function certificadosPorCiudadPorInspectorYRango($inspector, $desde, $hasta)
{
    $sql = "
        SELECT a.city, COUNT(*) AS total
        FROM certificates c
        INNER JOIN addresses a ON c.address_id = a.id
        INNER JOIN inspectors i ON c.inspector_id = i.id
        WHERE i.name = ? 
        AND DATE(c.test_date) BETWEEN ? AND ?
        GROUP BY a.city
        ORDER BY total DESC
    ";

    return $this->selectAll($sql, [$inspector, $desde, $hasta]);
}


public function inspectoresPorRango($desde, $hasta)
{
    $sql = "
        SELECT DISTINCT i.name AS inspector_name
        FROM certificates c
        INNER JOIN inspectors i ON c.inspector_id = i.id
        WHERE DATE(c.test_date) BETWEEN ? AND ?
        ORDER BY i.name
    ";

    return $this->selectAll($sql, [$desde, $hasta]);
}

public function certificadosPorCiudadPorInspectorYFecha($inspector, $fecha)
{
    $sql = "
        SELECT a.city, COUNT(*) AS total
        FROM certificates c
        INNER JOIN addresses a ON c.address_id = a.id
        INNER JOIN inspectors i ON c.inspector_id = i.id
        WHERE i.name = ? AND DATE(c.test_date) = ?
        GROUP BY a.city
        ORDER BY total DESC
    ";
    return $this->selectAll($sql, [$inspector, $fecha]);
}



}
