<?php

require_once __DIR__ . '/../repository/tenant-repository.php';
require_once __DIR__ . '/../res/api-tenant-response-objects.php';

function getSecurityDoorsTenantBySlug(string $slug): ?SecurityDoorsTenantResponse
{
    $tenantRecord = findSecurityDoorsTenantRecordBySlug($slug);
    return $tenantRecord === null ? null : mapSecurityDoorsTenantToResponse($tenantRecord);
}

function mapSecurityDoorsTenantToResponse(array $tenantRecord): SecurityDoorsTenantResponse
{
    return new SecurityDoorsTenantResponse($tenantRecord['slug'], $tenantRecord['name']);
}
