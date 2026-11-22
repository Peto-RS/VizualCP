<?php

require_once __DIR__ . '/../../common/facade/db-facade.php';
require_once __DIR__ . '/../../common/facade/json-data-manipulation-facade.php';

function findSecurityDoorsTenantRecordBySlug(string $slug): ?array
{
    $db = getDbConnection();
    $statement = $db->prepare(
        'SELECT slug, name FROM tenant WHERE slug = :slug AND is_active = 1 LIMIT 1'
    );
    if ($statement === false) {
        $errorMessage = $db->lastErrorMsg();
        $db->close();
        throw new RuntimeException('Could not prepare tenant lookup: ' . $errorMessage);
    }

    $statement->bindValue(':slug', $slug, SQLITE3_TEXT);
    $result = $statement->execute();
    if ($result === false) {
        $errorMessage = $db->lastErrorMsg();
        $statement->close();
        $db->close();
        throw new RuntimeException('Could not execute tenant lookup: ' . $errorMessage);
    }

    $row = $result->fetchArray(SQLITE3_ASSOC);
    $result->finalize();
    $statement->close();
    $db->close();

    return $row === false ? null : $row;
}
