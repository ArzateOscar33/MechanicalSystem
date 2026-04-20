<?php
class ReportesModel extends Query
{
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Obtiene todos los registros del reporte
     */
    public function obtenerReporteCertificados()
    {
        $sql = "SELECT 
                    cert_number,
                    vin,
                    cliente,
                    realizado_por,
                    secomext,
                    bdd_m,
                    fecha,
                    comentario
                FROM vw_reporte_certificados
                ORDER BY fecha DESC, cert_number DESC";
        return $this->selectAll($sql);
    }

    /**
     * Obtiene registros filtrados
     * Todos los filtros son opcionales
     */
    public function obtenerReporteCertificadosFiltrado(array $filtros = [])
    {
        $sql = "SELECT 
                    cert_number,
                    vin,
                    cliente,
                    realizado_por,
                    secomext,
                    bdd_m,
                    fecha,
                    comentario
                FROM vw_reporte_certificados
                WHERE 1=1";

        $params = [];

        if (!empty($filtros['cert_number'])) {
            $sql .= " AND cert_number LIKE ?";
            $params[] = '%' . trim($filtros['cert_number']) . '%';
        }

        if (!empty($filtros['vin'])) {
            $sql .= " AND vin LIKE ?";
            $params[] = '%' . trim($filtros['vin']) . '%';
        }

        if (!empty($filtros['cliente'])) {
            $sql .= " AND cliente = ?";
            $params[] = trim($filtros['cliente']);
        }

        if (!empty($filtros['realizado_por'])) {
            $sql .= " AND realizado_por = ?";
            $params[] = trim($filtros['realizado_por']);
        }

        if (!empty($filtros['secomext'])) {
            $sql .= " AND secomext = ?";
            $params[] = trim($filtros['secomext']);
        }

        if (!empty($filtros['bdd_m'])) {
            $sql .= " AND bdd_m = ?";
            $params[] = trim($filtros['bdd_m']);
        }

        if (!empty($filtros['fecha_inicio'])) {
            $sql .= " AND DATE(fecha) >= ?";
            $params[] = $filtros['fecha_inicio'];
        }

        if (!empty($filtros['fecha_fin'])) {
            $sql .= " AND DATE(fecha) <= ?";
            $params[] = $filtros['fecha_fin'];
        }

        if (!empty($filtros['comentario'])) {
            $sql .= " AND comentario LIKE ?";
            $params[] = '%' . trim($filtros['comentario']) . '%';
        }

        $sql .= " ORDER BY fecha DESC, cert_number DESC";

        return $this->selectAll($sql, $params);
    }

    /**
     * Obtiene clientes únicos para el filtro
     */
    public function obtenerClientesUnicos()
    {
        $sql = "SELECT DISTINCT cliente
                FROM vw_reporte_certificados
                WHERE cliente IS NOT NULL 
                  AND cliente != ''
                ORDER BY cliente ASC";
        return $this->selectAll($sql);
    }

    /**
     * Obtiene usuarios únicos para el filtro
     */
    public function obtenerRealizadosPorUnicos()
    {
        $sql = "SELECT DISTINCT realizado_por
                FROM vw_reporte_certificados
                WHERE realizado_por IS NOT NULL 
                  AND realizado_por != ''
                ORDER BY realizado_por ASC";
        return $this->selectAll($sql);
    }

    /**
     * Obtiene valores únicos de Secomext
     */
    public function obtenerSecomextUnicos()
    {
        $sql = "SELECT DISTINCT secomext
                FROM vw_reporte_certificados
                WHERE secomext IS NOT NULL 
                  AND secomext != ''
                ORDER BY secomext ASC";
        return $this->selectAll($sql);
    }

    /**
     * Obtiene valores únicos de BDD M
     */
    public function obtenerBddMUnicos()
    {
        $sql = "SELECT DISTINCT bdd_m
                FROM vw_reporte_certificados
                WHERE bdd_m IS NOT NULL 
                  AND bdd_m != ''
                ORDER BY bdd_m ASC";
        return $this->selectAll($sql);
    }
}
