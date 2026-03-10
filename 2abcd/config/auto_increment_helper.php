<?php 
function resetAutoIncrement($conn, $tableName, $idColumn = 'id') {
    try {
        // Tính MAX(id) từ bảng
        $sql = "SELECT MAX($idColumn) as max_id FROM $tableName";
        $result = $conn->query($sql)->fetch();
        $maxId = $result['max_id'] ?? 0;
        
        // Set AUTO_INCREMENT = MAX(id) + 1
        $nextId = $maxId + 1;
        $alterSql = "ALTER TABLE $tableName AUTO_INCREMENT = $nextId";
        $conn->exec($alterSql);
        
        return true;
    } catch (Exception $e) {
        return false;
    }
}
