<?php
class PrincipalModel extends Query
{
    public function __construct()
    {
        parent::__construct();
    }

    public function certificadosTotales(string $buscar = ''): array
    {
        $buscar = trim($buscar);

        $sql = "SELECT COUNT(*) AS total
                FROM certificates
                WHERE 1=1";

        $params = [];

        if ($buscar !== '') {
            $sql .= " AND (
                        cert_number LIKE ?
                        OR vin LIKE ?
                        OR DATE(test_date) = ?
                      )";

            $like = '%' . $buscar . '%';
            $params[] = $like;
            $params[] = $like;
            $params[] = $buscar;
        }

        return $this->select($sql, $params);
    }

    public function listarCertificadosPaginado(int $page = 1, int $perPage = 25, string $buscar = ''): array
    {
        $page = max(1, $page);
        $perPage = (int)$perPage;
        $buscar = trim($buscar);

        $allowed = [25, 50, 100, 1000, 5000];
        $isAll = ($perPage >= 10000000);

        if (!$isAll && !in_array($perPage, $allowed, true)) {
            $perPage = 25;
        }

        $sql = "SELECT 
                    cert_number,
                    vin,
                    license_plate,
                    test_date,
                    source_file,
                    zip_file_path,
                    created_at
                FROM certificates
                WHERE 1=1";

        $params = [];

        if ($buscar !== '') {
            $sql .= " AND (
                        cert_number LIKE ?
                        OR vin LIKE ?
                        OR DATE(test_date) = ?
                      )";

            $like = '%' . $buscar . '%';
            $params[] = $like;
            $params[] = $like;
            $params[] = $buscar;
        }

        $sql .= " ORDER BY 
                    CASE WHEN test_date IS NULL THEN 1 ELSE 0 END,
                    test_date DESC,
                    created_at DESC";

        if (!$isAll) {
            $offset = ($page - 1) * $perPage;
            $sql .= " LIMIT $offset, $perPage";
        }

        return $this->selectAll($sql, $params);
    }

    public function obtenerCertificadoPorNumero(string $certNumber)
    {
        $sql = "SELECT 
                    cert_number,
                    vin,
                    license_plate,
                    test_date,
                    source_file,
                    zip_file_path,
                    created_at
                FROM certificates
                WHERE cert_number = ?";
        return $this->select($sql, [$certNumber]);
    }
}
