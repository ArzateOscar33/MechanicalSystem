<?php
class BuscarCertificadosModel extends Query
{
    public function __construct()
    {
        parent::__construct();
    }
    
    // Obtener todos los certificados con la información de direcciones
    public function obtenerCertificados()
    {
        $sql = "SELECT c.cert_number, c.make, c.owner_name, c.inspector_name, 
                       a.city, a.state, a.zip, c.mfg_in 
                FROM certificates c 
                LEFT JOIN addresses a ON c.address_id = a.id 
                ORDER BY c.cert_number ASC";
        return $this->selectAll($sql);
    }
    
    // Método para buscar certificados con filtros específicos (opcional para implementación futura)
    public function buscarCertificados($filtros)
    {
        $condiciones = [];
        $parametros = [];
        
        if (!empty($filtros['cert_number'])) {
            $condiciones[] = "c.cert_number LIKE :cert_number";
            $parametros[':cert_number'] = '%' . $filtros['cert_number'] . '%';
        }
        
        if (!empty($filtros['city'])) {
            $condiciones[] = "a.city LIKE :city";
            $parametros[':city'] = '%' . $filtros['city'] . '%';
        }
        
        if (!empty($filtros['state'])) {
            $condiciones[] = "a.state LIKE :state";
            $parametros[':state'] = '%' . $filtros['state'] . '%';
        }
        
        if (!empty($filtros['make'])) {
            $condiciones[] = "c.make LIKE :make";
            $parametros[':make'] = '%' . $filtros['make'] . '%';
        }
        
        if (!empty($filtros['owner_name'])) {
            $condiciones[] = "c.owner_name LIKE :owner_name";
            $parametros[':owner_name'] = '%' . $filtros['owner_name'] . '%';
        }
        
        if (!empty($filtros['inspector_name'])) {
            // Buscar tanto en el código como en el nombre del inspector
            $condiciones[] = "(c.inspector_name LIKE :inspector_name OR SUBSTRING(c.inspector_name, LOCATE(' ', c.inspector_name)) LIKE :inspector_name_only)";
            $parametros[':inspector_name'] = '%' . $filtros['inspector_name'] . '%';
            $parametros[':inspector_name_only'] = '%' . $filtros['inspector_name'] . '%';
        }
        
        $sql = "SELECT c.cert_number, c.make, c.owner_name, c.inspector_name, 
                       a.city, a.state, a.zip, c.mfg_in 
                FROM certificates c 
                LEFT JOIN addresses a ON c.address_id = a.id";
        
        if (!empty($condiciones)) {
            $sql .= " WHERE " . implode(" AND ", $condiciones);
        }
        
        $sql .= " ORDER BY c.cert_number ASC";
        
        // Este método requeriría una implementación adicional en la clase Query
        // para manejar consultas preparadas con parámetros asociativos
        // return $this->selectAllParams($sql, $parametros);
        
        // Por ahora, usamos el método existente
        return $this->selectAll($sql);
    }
}
?>