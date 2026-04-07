<?php
class CrearCertificadosModel extends Query
{
    private $db;

    public function __construct()
    {
        parent::__construct();

        // Conexión local del modelo para manejar transacciones
        // sin modificar la clase Query.
        $conexion = new Conexion();
        $this->db = $conexion->conect();
    }

    public function consultarCertificado($cert_number)
    {
        $sql = "SELECT cert_number FROM certificates WHERE cert_number = ?";
        return $this->select($sql, [$cert_number]);
    }

    public function consultarDireccion($number, $street, $city, $state, $zip)
    {
        $sql = "SELECT id FROM addresses 
                WHERE `number` = ? AND `street` = ? AND `city` = ? AND `state` = ? AND `zip` = ?";
        return $this->select($sql, [$number, $street, $city, $state, $zip]);
    }

    public function insertarDireccion($number, $street, $city, $state, $zip)
    {
        $sql = "INSERT INTO addresses (`number`, `street`, `city`, `state`, `zip`) 
                VALUES (?, ?, ?, ?, ?)";
        return $this->insertar($sql, [$number, $street, $city, $state, $zip]);
    }

    public function insertarCertificado(
        $cert_number,
        $vin,
        $address_id,
        $phone,
        $year,
        $mfg_in,
        $make,
        $owner_name,
        $model,
        $license_plate,
        $odometer,
        $inspector_id,
        $eei_itn,
        $created_by,
        $client_id,
        $test_date,
        $expires,
        $source_file
    ) {
        try {
            $zip_path = 'uploads/certificates/' . $cert_number . '.zip';

            $sql = "INSERT INTO certificates (
            cert_number, vin, address_id, phone, year, mfg_in, make,
            owner_name, model, license_plate, odometer, inspector_id, eei_itn,
            created_by,client_id, test_date, expires, source_file, zip_file_path
        ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

            $stmt = $this->db->prepare($sql);
            $ok = $stmt->execute([
                $cert_number,
                $vin,
                $address_id,
                $phone,
                $year,
                $mfg_in,
                $make,
                $owner_name,
                $model,
                $license_plate,
                $odometer,
                $inspector_id,
                $eei_itn,
                $created_by,
                $client_id,
                $test_date,
                $expires,
                $source_file,
                $zip_path
            ]);

            if (!$ok) {
                $error = $stmt->errorInfo();
                error_log("Error en insertarCertificado: " . json_encode($error, JSON_UNESCAPED_UNICODE));
                return false;
            }

            return true;
        } catch (\Throwable $e) {
            error_log("Excepción en insertarCertificado: " . $e->getMessage());
            return false;
        }
    }

    public function insertarMonitoreo($cert_number, $tipo, $resultado)
    {
        $sql = "INSERT INTO monitoring_results (cert_number, monitor_type, result) 
                VALUES (?, ?, ?)";
        return $this->insertar($sql, [$cert_number, $tipo, $resultado]);
    }

    public function registrarImportacion($file_name, $status, $error_message, $id_usuario)
    {
        $sql = "INSERT INTO import_logs (file_name, status, error_message, imported_by) 
                VALUES (?, ?, ?, ?)";
        return $this->insertar($sql, [$file_name, $status, $error_message, $id_usuario]);
    }

    public function registrarImportacionAlternativa($file_name, $status, $error_message)
    {
        $sql = "INSERT INTO import_logs (file_name, status, error_message) 
                VALUES (?, ?, ?)";
        return $this->insertar($sql, [$file_name, $status, $error_message]);
    }

    public function actualizarImportacion($import_id, $records_imported, $records_failed, $status, $error_message)
    {
        $sql = "UPDATE import_logs 
                SET records_imported = ?, records_failed = ?, status = ?, error_message = ? 
                WHERE id = ?";
        return $this->save($sql, [$records_imported, $records_failed, $status, $error_message, $import_id]);
    }

    public function obtenerImportLogs()
    {
        $sql = "SELECT * FROM import_logs ORDER BY created_at DESC";
        return $this->selectAll($sql);
    }

    public function obtenerIdUsuarioPorNombre($nombre_usuario)
    {
        $sql = "SELECT id FROM usuarios WHERE nombre_usuario = ?";
        $res = $this->select($sql, [$nombre_usuario]);
        return $res ? $res['id'] : 0;
    }

    public function obtenerDirecciones()
    {
        $sql = "SELECT id, CONCAT(`number`, ' ', `street`, ', ', `city`, ', ', `state`, ' ', `zip`) AS nombre
                FROM addresses
                ORDER BY id DESC";
        return $this->selectAll($sql);
    }

    public function obtenerDireccionPorId($id)
    {
        $sql = "SELECT `number`, `street`, `city`, `state`, `zip`, `latitude`, `longitude` 
                FROM addresses WHERE id = ?";
        return $this->select($sql, [$id]);
    }

    public function obtenerInspectores()
    {
        $sql = "SELECT id, name FROM inspectors ORDER BY name ASC";
        return $this->selectAll($sql);
    }

    public function obtenerInspectorPorId($id)
    {
        $sql = "SELECT * FROM inspectors WHERE id = ?";
        return $this->select($sql, [$id]);
    }

    public function obtenerInspectorPorNombre($nombre)
    {
        $sql = "SELECT id FROM inspectors WHERE name = ?";
        return $this->select($sql, [$nombre]);
    }

    public function insertarInspector($nombre)
    {
        $inspector = $this->obtenerInspectorPorNombre($nombre);
        if ($inspector) {
            return $inspector['id'];
        }

        $sql = "INSERT INTO inspectors (name) VALUES (?)";
        return $this->insertar($sql, [$nombre]);
    }

    public function insertarDireccionConCoordenadas($number, $street, $city, $state, $zip, $lat, $lon)
    {
        $sql = "INSERT INTO addresses (`number`, `street`, `city`, `state`, `zip`, `latitude`, `longitude`) 
                VALUES (?, ?, ?, ?, ?, ?, ?)";
        return $this->insertar($sql, [$number, $street, $city, $state, $zip, $lat, $lon]);
    }

    public function consultarDireccionConCoordenadas($number, $street, $city, $state, $zip, $lat, $lon)
    {
        $sql = "SELECT id FROM addresses 
                WHERE `number` = ? AND `street` = ? AND `city` = ? 
                  AND `state` = ? AND `zip` = ? AND `latitude` = ? AND `longitude` = ?";
        return $this->select($sql, [$number, $street, $city, $state, $zip, $lat, $lon]);
    }

    public function vinConCertificadoActivo($vin, $fechaReferencia)
    {
        $sql = "SELECT cert_number 
                FROM certificates 
                WHERE vin = ? 
                  AND expires >= ?
                LIMIT 1";

        return $this->select($sql, [$vin, $fechaReferencia]);
    }

    /**
     * SOLO visual.
     * No reserva nada, no bloquea nada, no debe usarse para insertar.
     */
    public function obtenerCertNumberPreliminar()
    {
        $sql = "SELECT numero 
                FROM secuencias_certificado 
                WHERE certificado = ?
                LIMIT 1";
        $res = $this->select($sql, ['CERTIFICADO']);

        $actual = $res && isset($res['numero']) ? intval($res['numero']) : 0;
        $siguiente = $actual + 1;

        $consecutivo = str_pad($siguiente, 8, "0", STR_PAD_LEFT);
        return 'MEX-' . $consecutivo;
    }

    /**
     * Número definitivo.
     * ESTE sí se debe usar al guardar el certificado.
     * Hace reserva atómica del consecutivo para evitar colisiones.
     */
    public function generarCertNumberGlobal()
    {
        try {
            $this->db->beginTransaction();

            $sqlSelect = "SELECT numero
                          FROM secuencias_certificado
                          WHERE certificado = ?
                          FOR UPDATE";
            $stmtSelect = $this->db->prepare($sqlSelect);
            $stmtSelect->execute(['CERTIFICADO']);
            $res = $stmtSelect->fetch(PDO::FETCH_ASSOC);

            if (!$res || !isset($res['numero'])) {
                $this->db->rollBack();
                return false;
            }

            $numeroActual = intval($res['numero']);
            $nuevoNumero  = $numeroActual + 1;

            $sqlUpdate = "UPDATE secuencias_certificado
                          SET numero = ?
                          WHERE certificado = ?";
            $stmtUpdate = $this->db->prepare($sqlUpdate);
            $ok = $stmtUpdate->execute([$nuevoNumero, 'CERTIFICADO']);

            if (!$ok) {
                $this->db->rollBack();
                return false;
            }

            $this->db->commit();

            $consecutivo = str_pad($nuevoNumero, 8, "0", STR_PAD_LEFT);
            return 'MEX-' . $consecutivo;
        } catch (PDOException $e) {
            if ($this->db->inTransaction()) {
                $this->db->rollBack();
            }
            error_log("Error en generarCertNumberGlobal: " . $e->getMessage());
            return false;
        }
    }

    // INSERCIONES PARA REGISTRO DE LA API 
    public function siguienteAttemptApiEmissions(string $cert_number): int
    {
        $sql = "SELECT COALESCE(MAX(attempt), 0) + 1 AS n
                FROM api_emissions_sync_log
                WHERE cert_number = ?";
        $row = $this->select($sql, [$cert_number]);
        return (int)($row['n'] ?? 1);
    }

    public function insertarApiEmissionsSyncLog(array $d): int
    {
        $sql = "INSERT INTO api_emissions_sync_log
            (cert_number, vin, mode, endpoint,
             payload_sha256, pdf_sha256, photos_sha256_json,
             http_status, api_result, api_description, response_raw,
             attempt, sent_at)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())";

        return (int)$this->insertar($sql, [
            $d['cert_number'] ?? '',
            $d['vin'] ?? '',
            $d['mode'] ?? 'production',
            $d['endpoint'] ?? '',

            $d['payload_sha256'] ?? '',
            $d['pdf_sha256'] ?? '',
            $d['photos_sha256_json'] ?? '{}',

            (int)($d['http_status'] ?? 0),
            $d['api_result'] ?? null,
            $d['api_description'] ?? null,
            $d['response_raw'] ?? null,

            (int)($d['attempt'] ?? 1),
        ]);
    }
    //Concurrencia de datos

    public function obtenerReservaActivaPorPrevalId(int $preval_id)
    {
        $sql = "SELECT *
            FROM certificados_reservados
            WHERE preval_id = ?
              AND status IN ('reservado', 'aprobado')
            ORDER BY id DESC
            LIMIT 1";

        return $this->select($sql, [$preval_id]);
    }
    public function obtenerFolioLiberadoDisponible()
    {
        $sql = "SELECT *
            FROM certificados_reservados
            WHERE status = 'liberado'
            ORDER BY liberado_at ASC, id ASC
            LIMIT 1";
        return $this->select($sql);
    }

    public function reservarCertNumberParaPrevalidacion(int $preval_id, ?string $vin, int $id_usuario)
    {
        try {
            $this->db->beginTransaction();

            // 1) Si el intento ya tiene folio activo, reutilizarlo
            $sqlExistente = "SELECT *
                         FROM certificados_reservados
                         WHERE preval_id = ?
                           AND status IN ('reservado', 'aprobado')
                         ORDER BY id DESC
                         LIMIT 1
                         FOR UPDATE";
            $stmtExistente = $this->db->prepare($sqlExistente);
            $stmtExistente->execute([$preval_id]);
            $existente = $stmtExistente->fetch(PDO::FETCH_ASSOC);

            if ($existente && !empty($existente['cert_number'])) {
                $this->db->commit();
                return $existente['cert_number'];
            }

            // 2) Buscar un folio liberado para reutilizar
            $sqlLiberado = "SELECT *
                        FROM certificados_reservados
                        WHERE status = 'liberado'
                        ORDER BY liberado_at ASC, id ASC
                        LIMIT 1
                        FOR UPDATE";
            $stmtLiberado = $this->db->prepare($sqlLiberado);
            $stmtLiberado->execute();
            $liberado = $stmtLiberado->fetch(PDO::FETCH_ASSOC);

            if ($liberado && !empty($liberado['cert_number'])) {

                // ==========================================
                // NUEVO: validar que no exista en certificates
                // ==========================================
                $sqlCheck = "SELECT cert_number FROM certificates WHERE cert_number = ? LIMIT 1";
                $stmtCheck = $this->db->prepare($sqlCheck);
                $stmtCheck->execute([$liberado['cert_number']]);
                $yaExiste = $stmtCheck->fetch(PDO::FETCH_ASSOC);

                if ($yaExiste) {
                    // ❌ ya no se puede reutilizar
                    // lo marcamos como usado para sacarlo del pool
                    $sqlMarcarUsado = "UPDATE certificados_reservados
                           SET status = 'usado',
                               usado_at = NOW()
                           WHERE id = ?";
                    $stmtUsado = $this->db->prepare($sqlMarcarUsado);
                    $stmtUsado->execute([$liberado['id']]);

                    // ⚠️ continuar flujo normal → generar nuevo
                } else {
                    // ✅ reutilizar folio válido
                    $sqlReusar = "UPDATE certificados_reservados
                      SET preval_id = ?,
                          vin = ?,
                          status = 'reservado',
                          reservado_por = ?,
                          reservado_at = NOW(),
                          liberado_at = NULL,
                          aprobado_at = NULL,
                          usado_at = NULL,
                          motivo_liberacion = NULL
                      WHERE id = ?";
                    $stmtReusar = $this->db->prepare($sqlReusar);
                    $ok = $stmtReusar->execute([
                        $preval_id,
                        $vin,
                        $id_usuario,
                        $liberado['id']
                    ]);

                    if (!$ok) {
                        $this->db->rollBack();
                        return false;
                    }

                    $this->db->commit();
                    return $liberado['cert_number'];
                }
            }

            // 3) Si no hay liberados, generar uno nuevo desde secuencia
            $sqlSec = "SELECT numero
                   FROM secuencias_certificado
                   WHERE certificado = ?
                   LIMIT 1
                   FOR UPDATE";
            $stmtSec = $this->db->prepare($sqlSec);
            $stmtSec->execute(['CERTIFICADO']);
            $sec = $stmtSec->fetch(PDO::FETCH_ASSOC);

            if (!$sec || !isset($sec['numero'])) {
                $this->db->rollBack();
                return false;
            }

            $numeroActual = (int)$sec['numero'];
            $nuevoNumero  = $numeroActual + 1;
            $cert_number  = 'MEX-' . str_pad($nuevoNumero, 8, '0', STR_PAD_LEFT);

            $sqlUpdateSec = "UPDATE secuencias_certificado
                         SET numero = ?
                         WHERE certificado = ?";
            $stmtUpdateSec = $this->db->prepare($sqlUpdateSec);
            $okSec = $stmtUpdateSec->execute([$nuevoNumero, 'CERTIFICADO']);

            if (!$okSec) {
                $this->db->rollBack();
                return false;
            }

            $sqlInsertReserva = "INSERT INTO certificados_reservados
            (cert_number, preval_id, vin, status, reservado_por, reservado_at)
            VALUES (?, ?, ?, 'reservado', ?, NOW())";
            $stmtInsertReserva = $this->db->prepare($sqlInsertReserva);
            $okReserva = $stmtInsertReserva->execute([
                $cert_number,
                $preval_id,
                $vin,
                $id_usuario
            ]);

            if (!$okReserva) {
                $this->db->rollBack();
                return false;
            }

            $this->db->commit();
            return $cert_number;
        } catch (PDOException $e) {
            if ($this->db->inTransaction()) {
                $this->db->rollBack();
            }
            error_log("Error en reservarCertNumberParaPrevalidacion: " . $e->getMessage());
            return false;
        }
    }

    public function aprobarReservaCertificado(string $cert_number, int $preval_id): bool
    {
        $sql = "UPDATE certificados_reservados
            SET status = 'aprobado',
                aprobado_at = NOW()
            WHERE cert_number = ?
              AND preval_id = ?
              AND status IN ('reservado', 'aprobado')";
        return (bool)$this->save($sql, [$cert_number, $preval_id]);
    }
    public function liberarReservaCertificado(string $cert_number, int $preval_id, string $motivo = null): bool
    {
        $sql = "UPDATE certificados_reservados
            SET status = 'liberado',
                liberado_at = NOW(),
                motivo_liberacion = ?,
                preval_id = NULL
            WHERE cert_number = ?
              AND preval_id = ?
              AND status IN ('reservado', 'aprobado')";
        return (bool)$this->save($sql, [$motivo, $cert_number, $preval_id]);
    }
    public function marcarReservaComoUsada(string $cert_number, int $preval_id): bool
    {
        $sql = "UPDATE certificados_reservados
            SET status = 'usado',
                usado_at = NOW()
            WHERE cert_number = ?
              AND preval_id = ?
              AND status IN ('reservado', 'aprobado')";
        return (bool)$this->save($sql, [$cert_number, $preval_id]);
    }
    public function obtenerCertNumberPorPrevalId(int $preval_id)
    {
        $sql = "SELECT cert_number, status
            FROM certificados_reservados
            WHERE preval_id = ?
              AND status IN ('reservado', 'aprobado', 'usado')
            ORDER BY id DESC
            LIMIT 1";
        return $this->select($sql, [$preval_id]);
    }

    public function obtenerClientes()
    {
        $sql = "SELECT id_client, nombre_cliente FROM clients ORDER BY nombre_cliente ASC";
        return $this->selectAll($sql);
    }
}
