<?php

require_once __DIR__ . '/nusoap.php';

class SecomextClient
{
    private string $wsdlDatos  = 'http://45.6.142.37/ntsDataTransfer/Service1.svc?wsdl';
    private string $wsdlFotos  = 'http://45.6.142.37//sendphotos/Service1.svc?wsdl';

    private string $usuario;
    private string $password;
    private int    $workshopId;
    private string $idEquipment;
    private string $idInspector;
    private int    $idLocation;
    private bool   $modoTesting;

    public function __construct()
    {
        $this->usuario     = SECOMEXT_USER;
        $this->password    = SECOMEXT_PASS;
        $this->workshopId  = (int)SECOMEXT_WORKSHOP_ID;
        $this->idEquipment = SECOMEXT_EQUIPMENT_ID;
        $this->idInspector = SECOMEXT_INSPECTOR_ID;
        $this->idLocation  = (int)SECOMEXT_LOCATION_ID;
        $this->modoTesting = (defined('APP_ENV') && APP_ENV === 'testing');
    }

    /**
     * Paso 1 — Envía los datos del certificado a Secomext (ReceiveData).
     * Devuelve: ['success' => bool, 'respuesta' => string]
     */
    public function enviarDatos(array $datos): array
    {
        // --- MODO TESTING ---
        if ($this->modoTesting) {
            $resultado = defined('TEST_DATOS_RESULT') ? TEST_DATOS_RESULT : 'success';

            if ($resultado === 'success') {
                return [
                    'success'   => true,
                    'respuesta' => '[TEST] ReceiveData simulado: success'
                ];
            }

            return [
                'success'   => false,
                'respuesta' => '[TEST] ReceiveData simulado: Failure: VIN provided is invalid.'
            ];
        }

        // --- MODO PRODUCCIÓN ---
        try {
            $client = new nusoap_client($this->wsdlDatos, 'wsdl');
            $client->soap_defencoding = 'UTF-8';

            $error = $client->getError();
            if ($error) {
                return [
                    'success'   => false,
                    'respuesta' => 'Error al conectar con Secomext (datos): ' . $error
                ];
            }

            $params = [
                'user'                            => $this->usuario,
                'pwd'                             => $this->password,
                'workshopId'                      => $this->workshopId,
                'idequipment'                     => $this->idEquipment,
                'niv'                             => $datos['vin'],
                'CertNumber'                      => $datos['cert_number'],
                'dmvNumber'                       => $datos['dmv_number'] ?? '',
                'misfireMonitoring'               => $datos['misfire_monitoring'],
                'fuelSystemMonitoring'            => $datos['fuel_system_monitoring'],
                'comprehensiveCatalystMonitoring' => $datos['comprehensive_catalyst_monitoring'],
                'catalystMonitoring'              => $datos['catalyst_monitoring'],
                'oxigenSensorMonitoring'          => $datos['oxygen_sensor_monitoring'],
                'overallTestResult'               => $datos['overall_test_result'],
                'dateTest'                        => $datos['test_date'],
                'odometer'                        => (float)$datos['odometer'],
                'licenseplate'                    => $datos['license_plate'],
                'idInspector'                     => $this->idInspector,
                'idLocation'                      => $this->idLocation,
                'lattitude'                       => $datos['latitude'],
                'longitude'                       => $datos['longitude'],
            ];

            $resultado = $client->call('ReceiveData', $params);

            error_log('[Secomext datos] params=' . print_r($params, true));
            error_log('[Secomext datos] raw_result=' . print_r($resultado, true));

            if ($client->fault) {
                error_log('[Secomext datos] fault=' . print_r($resultado, true));
            }

            $error = $client->getError();
            if ($error) {
                error_log('[Secomext datos] soap_error=' . $error);
            }


            if ($client->fault) {
                return [
                    'success'   => false,
                    'respuesta' => 'Fault SOAP: ' . print_r($resultado, true)
                ];
            }

            $error = $client->getError();
            if ($error) {
                return [
                    'success'   => false,
                    'respuesta' => 'Error SOAP: ' . $error
                ];
            }

            $respuestaTexto = is_array($resultado)
                ? (string)($resultado['ReceiveDataResult'] ?? print_r($resultado, true))
                : (string)$resultado;

            $exitoso = stripos($respuestaTexto, 'success') !== false;

            return [
                'success'   => $exitoso,
                'respuesta' => $respuestaTexto
            ];
        } catch (Exception $e) {
            return [
                'success'   => false,
                'respuesta' => 'Excepción: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Paso 2 — Envía las fotos a Secomext (SendString en Base64).
     * Devuelve: ['success' => bool, 'respuesta' => string]
     */
    public function enviarFotos(string $certNumber, string $vin, array $fotos): array
    {
        // --- MODO TESTING ---
        if ($this->modoTesting) {
            $resultado = defined('TEST_FOTOS_RESULT') ? TEST_FOTOS_RESULT : 'success';

            if ($resultado === 'success') {
                return [
                    'success'   => true,
                    'respuesta' => '[TEST] SendString simulado: ok'
                ];
            }

            return [
                'success'   => false,
                'respuesta' => '[TEST] SendString simulado: Error: certificate not found in database.'
            ];
        }

        // --- MODO PRODUCCIÓN ---
        try {
            $client = new nusoap_client($this->wsdlFotos, 'wsdl');
            $client->soap_defencoding = 'UTF-8';

            $error = $client->getError();
            if ($error) {
                return [
                    'success'   => false,
                    'respuesta' => 'Error al conectar con Secomext (fotos): ' . $error
                ];
            }

            $params = [
                'user'    => $this->usuario,
                'pwd'     => $this->password,
                'cert'    => $certNumber,
                'vin'     => $vin,
                'front'   => $this->fotoABase64($fotos['front']   ?? ''),
                'back'    => $this->fotoABase64($fotos['back']    ?? ''),
                'left'    => $this->fotoABase64($fotos['left']    ?? ''),
                'right'   => $this->fotoABase64($fotos['right']   ?? ''),
                'vindash' => $this->fotoABase64($fotos['vindash'] ?? ''),
                'label'   => $this->fotoABase64($fotos['label']   ?? ''),
                'device'  => $this->fotoABase64($fotos['device']  ?? ''),
                'device2' => $this->fotoABase64($fotos['device2'] ?? ''),
            ];

            $resultado = $client->call('SendString', $params);

            error_log('[Secomext fotos] cert=' . $certNumber . ' vin=' . $vin);
            error_log('[Secomext fotos] raw_result=' . print_r($resultado, true));

            if ($client->fault) {
                error_log('[Secomext fotos] fault=' . print_r($resultado, true));
            }

            $error = $client->getError();
            if ($error) {
                error_log('[Secomext fotos] soap_error=' . $error);
            }
            if ($client->fault) {
                return [
                    'success'   => false,
                    'respuesta' => 'Fault SOAP: ' . print_r($resultado, true)
                ];
            }

            $error = $client->getError();
            if ($error) {
                return [
                    'success'   => false,
                    'respuesta' => 'Error SOAP: ' . $error
                ];
            }

            $respuestaTexto = is_array($resultado)
                ? (string)($resultado['SendStringResult'] ?? print_r($resultado, true))
                : (string)$resultado;

            $exitoso = stripos($respuestaTexto, 'ok') !== false
                && stripos($respuestaTexto, 'error') === false;

            return [
                'success'   => $exitoso,
                'respuesta' => $respuestaTexto
            ];
        } catch (Exception $e) {
            return [
                'success'   => false,
                'respuesta' => 'Excepción: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Convierte una imagen a Base64.
     * Si el archivo no existe devuelve string vacío.
     */
    private function fotoABase64(string $rutaAbsoluta): string
    {
        if (empty($rutaAbsoluta) || !file_exists($rutaAbsoluta)) {
            return '';
        }
        return base64_encode(file_get_contents($rutaAbsoluta));
    }
}
