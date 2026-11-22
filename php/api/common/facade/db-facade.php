<?php
function getDbConnection(): SQLite3
{
    $db = new SQLite3(AppConfigJsonDataManipulation::getAll()['dbFilename']);
    $db->enableExceptions(true);

    if ($db->lastErrorCode() !== 0) {
        throw new RuntimeException('Could not open SQLite database: ' . $db->lastErrorMsg());
    }

    $schemaPath = __DIR__ . '/../../../resource/db/schema.sql';
    $schemaSql = file_get_contents($schemaPath);
    if ($schemaSql === false) {
        throw new RuntimeException('Could not read database schema file: ' . $schemaPath);
    }

    if (!$db->exec($schemaSql)) {
        throw new RuntimeException('Could not initialize database schema: ' . $db->lastErrorMsg());
    }

    return $db;
}

function createAuditLogRecord(
    SQLite3 $db,
    ?string $sessionRef,
    string $actorType,
    string $action,
    string $resourceType,
    ?string $resourceId = null,
    ?string $route = null,
    ?array $details = null
): int {
    $detailsJson = $details === null
        ? null
        : json_encode($details, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
    if ($details !== null && $detailsJson === false) {
        throw new RuntimeException('Could not encode audit log details: ' . json_last_error_msg());
    }

    $statement = $db->prepare(
        'INSERT INTO audit_log (actor_type, action, resource_type, resource_id, session_ref, route, details)
         VALUES (:actor_type, :action, :resource_type, :resource_id, :session_ref, :route, :details)'
    );
    if ($statement === false) {
        throw new RuntimeException('Could not prepare audit log insert: ' . $db->lastErrorMsg());
    }

    $statement->bindValue(':actor_type', $actorType, SQLITE3_TEXT);
    $statement->bindValue(':action', $action, SQLITE3_TEXT);
    $statement->bindValue(':resource_type', $resourceType, SQLITE3_TEXT);
    $statement->bindValue(':resource_id', $resourceId, $resourceId === null ? SQLITE3_NULL : SQLITE3_TEXT);
    $statement->bindValue(':session_ref', $sessionRef, $sessionRef === null ? SQLITE3_NULL : SQLITE3_TEXT);
    $statement->bindValue(':route', $route, $route === null ? SQLITE3_NULL : SQLITE3_TEXT);
    $statement->bindValue(':details', $detailsJson, $detailsJson === null ? SQLITE3_NULL : SQLITE3_TEXT);

    if ($statement->execute() === false) {
        throw new RuntimeException('Could not create audit log record: ' . $db->lastErrorMsg());
    }

    return $db->lastInsertRowID();
}

function createErrorLogRecord(
    SQLite3 $db,
    ?string $sessionRef,
    string $errorCode,
    string $message,
    string $errorType,
    ?string $stackTrace = null,
    ?string $route = null
): int {
    $statement = $db->prepare(
        'INSERT INTO error_log (error_code, message, stack_trace, session_ref, route, error_type)
         VALUES (:error_code, :message, :stack_trace, :session_ref, :route, :error_type)'
    );
    if ($statement === false) {
        throw new RuntimeException('Could not prepare error log insert: ' . $db->lastErrorMsg());
    }

    $statement->bindValue(':error_code', $errorCode, SQLITE3_TEXT);
    $statement->bindValue(':message', $message, SQLITE3_TEXT);
    $statement->bindValue(':stack_trace', $stackTrace, $stackTrace === null ? SQLITE3_NULL : SQLITE3_TEXT);
    $statement->bindValue(':session_ref', $sessionRef, $sessionRef === null ? SQLITE3_NULL : SQLITE3_TEXT);
    $statement->bindValue(':route', $route, $route === null ? SQLITE3_NULL : SQLITE3_TEXT);
    $statement->bindValue(':error_type', $errorType, SQLITE3_TEXT);

    if ($statement->execute() === false) {
        throw new RuntimeException('Could not create error log record: ' . $db->lastErrorMsg());
    }

    return $db->lastInsertRowID();
}

function incrementAndGetPriceOfferNumber(
    SQLite3 $db,
    string $tenantSlug = 'stolarstvo-sucansky',
    string $doorType = 'interior'
): int
{
    if (!in_array($doorType, ['interior', 'security'], true)) {
        throw new InvalidArgumentException('Invalid price offer door type: ' . $doorType);
    }

    if (!$db->exec("BEGIN IMMEDIATE")) {
        throw new RuntimeException('Could not begin price offer number transaction: ' . $db->lastErrorMsg());
    }

    try {
        $statement = $db->prepare("
            INSERT INTO price_offer_sequence_number (tenant_slug, door_type, value)
            VALUES (:tenant_slug, :door_type, 1)
            ON CONFLICT(tenant_slug, door_type)
            DO UPDATE SET value = value + 1
        ");
        if ($statement === false) {
            throw new RuntimeException('Could not prepare price offer number update: ' . $db->lastErrorMsg());
        }

        $statement->bindValue(':tenant_slug', $tenantSlug, SQLITE3_TEXT);
        $statement->bindValue(':door_type', $doorType, SQLITE3_TEXT);
        if ($statement->execute() === false) {
            throw new RuntimeException('Could not increment price offer number: ' . $db->lastErrorMsg());
        }
        $statement->close();

        $statement = $db->prepare(
            'SELECT value FROM price_offer_sequence_number
             WHERE tenant_slug = :tenant_slug AND door_type = :door_type'
        );
        if ($statement === false) {
            throw new RuntimeException('Could not prepare price offer number lookup: ' . $db->lastErrorMsg());
        }

        $statement->bindValue(':tenant_slug', $tenantSlug, SQLITE3_TEXT);
        $statement->bindValue(':door_type', $doorType, SQLITE3_TEXT);
        $result = $statement->execute();
        if ($result === false) {
            throw new RuntimeException('Could not read the updated price offer number: ' . $db->lastErrorMsg());
        }

        $row = $result->fetchArray(SQLITE3_ASSOC);
        $result->finalize();
        $statement->close();

        if ($row === false || !isset($row['value']) || !is_numeric($row['value'])) {
            throw new RuntimeException('Could not read the updated price offer number.');
        }
        $newValue = (int)$row['value'];

        if (!$db->exec("COMMIT")) {
            throw new RuntimeException('Could not commit price offer number transaction: ' . $db->lastErrorMsg());
        }
    } catch (Throwable $exception) {
        if (!$db->exec("ROLLBACK")) {
            throw new RuntimeException(
                'Could not roll back price offer number transaction: ' . $db->lastErrorMsg(),
                0,
                $exception
            );
        }

        throw $exception;
    }

    return (int)$newValue;
}