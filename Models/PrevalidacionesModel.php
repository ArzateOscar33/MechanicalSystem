<?php
class PrevalidacionesModel extends Query
{
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Crea un nuevo intento de prevalidación.
     * Se llama cuando el usuario hace click en "Prevalidar Datos".
     * Devuelve el ID del registro creado.
     */
    public function crearIntento(string $vin, string $license_plate, int $created_by): int
    {
        $sql = "INSERT INTO cert_prevalidaciones (vin, license_plate, created_by)
                VALUES (?, ?, ?)";
        return (int)$this->insertar($sql, [$vin, $license_plate, $created_by]);
    }

    /**
     * Registra el resultado del paso 1 (Secomext ReceiveData).
     * status: 'success' o 'failed'
     */
    public function actualizarPasoDatos(
        int    $id,
        string $status,
        string $respuesta,
        string $folio_secomext = ''
    ): int {
        $resultado = $status === 'success' ? 'en_proceso' : 'rechazado_datos';

        $sql = "UPDATE cert_prevalidaciones
                SET datos_enviados_at = NOW(),
                    datos_status      = ?,
                    datos_respuesta   = ?,
                    datos_folio_secomext = ?,
                    resultado         = ?
                WHERE id = ?";

        return $this->save($sql, [
            $status,
            $respuesta,
            $folio_secomext,
            $resultado,
            $id
        ]);
    }

    /**
     * Registra el resultado del paso 2 (Secomext Send/SendString fotos).
     * status: 'success' o 'failed'
     */
    public function actualizarPasoFotos(
        int    $id,
        string $status,
        string $respuesta
    ): int {
        $resultado = $status === 'success' ? 'aprobado' : 'rechazado_fotos';

        $sql = "UPDATE cert_prevalidaciones
                SET fotos_enviadas_at = NOW(),
                    fotos_status      = ?,
                    fotos_respuesta   = ?,
                    resultado         = ?
                WHERE id = ?";

        return $this->save($sql, [
            $status,
            $respuesta,
            $resultado,
            $id
        ]);
    }

    /**
     * Vincula el intento aprobado con el certificado que se generó.
     * Se llama después de insertar exitosamente en certificates.
     */
    public function vincularCertificado(int $id, string $cert_number): int
    {
        $sql = "UPDATE cert_prevalidaciones
                SET cert_number = ?
                WHERE id = ?";

        return $this->save($sql, [$cert_number, $id]);
    }

    /**
     * Obtiene un intento por su ID.
     * Útil para verificar el estado antes de cada paso.
     */
    public function obtenerPorId(int $id): array|false
    {
        $sql = "SELECT * FROM cert_prevalidaciones WHERE id = ?";
        return $this->select($sql, [$id]);
    }

    /**
     * Verifica si un intento específico pasó el paso 1.
     * El controlador la usa antes de permitir enviar fotos.
     */
    public function datosFueronAprobados(int $id): bool
    {
        $sql = "SELECT id FROM cert_prevalidaciones
                WHERE id = ? AND datos_status = 'success'";
        return (bool)$this->select($sql, [$id]);
    }

    /**
     * Verifica si un intento específico pasó ambos pasos.
     * El controlador la usa antes de permitir crear el certificado.
     */
    public function fueronAprobadosAmbos(int $id): bool
    {
        $sql = "SELECT id FROM cert_prevalidaciones
                WHERE id = ?
                  AND datos_status = 'success'
                  AND fotos_status = 'success'
                  AND resultado    = 'aprobado'";
        return (bool)$this->select($sql, [$id]);
    }

    /**
     * Historial de intentos para un VIN.
     * Útil para reportes o para mostrar al operador
     * cuántas veces se intentó prevalidar ese vehículo.
     */
    public function obtenerIntentosPorVin(string $vin): array|false
    {
        $sql = "SELECT * FROM cert_prevalidaciones
                WHERE vin = ?
                ORDER BY created_at DESC";
        return $this->selectAll($sql, [$vin]);
    }
}
