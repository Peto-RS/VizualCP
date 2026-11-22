<?php

require_once __DIR__ . "/../../php/api/common/facade/api-common-facade.php";
require_once __DIR__ . "/../../php/api/security-doors/facade/tenant-facade.php";

if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    header('Allow: GET');
    sendJsonResponse(['error' => 'Unsupported request method'], 405);
}

$slug = $_GET['slug'] ?? null;
if (!is_string($slug) || !preg_match('/\A[a-z0-9]+(?:-[a-z0-9]+)*\z/', $slug)) {
    sendJsonResponse(['error' => 'A valid tenant slug is required'], 400);
}

$tenant = getSecurityDoorsTenantBySlug($slug);
if ($tenant === null) {
    sendJsonResponse(['error' => 'Tenant not found'], 404);
}

sendJsonResponse($tenant, 200);
