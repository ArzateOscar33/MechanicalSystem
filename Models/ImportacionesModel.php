<?php
class ImportacionesModel extends Query
{

    public function __construct()
    {
        parent::__construct();
    }
    public function obtenerImportaciones()
    {
        // Consulta para obtener la cantidad de certificados por ciudad
        $sql = "
         SELECT 
            il.id,
            il.file_name,
            il.records_imported,
            il.records_failed,
            CONCAT(u.first_name, ' ', u.last_name) AS imported_by_name,
            il.status,
            il.error_message,
            il.created_at
        FROM 
            import_logs il
        JOIN 
            users u ON il.imported_by = u.id;
        ";

        // Ejecutar la consulta y obtener los resultados
        $result = $this->selectAll($sql);
        return $result;
    }
}
