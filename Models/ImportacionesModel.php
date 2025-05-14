<?php
class ImportacionesModel extends Query
{
    public function __construct()
    {
        parent::__construct();
    }

    public function obtenerImportaciones()
    {
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
            FROM import_logs il
            JOIN users u ON il.imported_by = u.id
            ORDER BY il.created_at DESC
        ";
        return $this->selectAll($sql);
    }
}
