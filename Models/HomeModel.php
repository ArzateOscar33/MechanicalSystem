<?php
class HomeModel extends Query
{
    public function __construct()
    {
        parent::__construct();
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
        $sql = "SELECT 
                    YEAR(test_date) AS anio, 
                    MONTH(test_date) AS mes, 
                    COUNT(*) AS total
                FROM certificates
                GROUP BY anio, mes
                ORDER BY anio, mes";
        return $this->selectAll($sql);
    }
}
