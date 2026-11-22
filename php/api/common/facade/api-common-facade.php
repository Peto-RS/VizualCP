<?php
function startApiSession(): string
{
    if (session_status() !== PHP_SESSION_ACTIVE && !session_start()) {
        throw new RuntimeException('Could not start API session.');
    }

    if (!isset($_SESSION['session_ref'])) {
        $_SESSION['session_ref'] = bin2hex(random_bytes(32));
    }

    return $_SESSION['session_ref'];
}

function sendJsonResponse($data, int $statusCode)
{
    header('Content-Type: application/json');
    http_response_code($statusCode);
    echo json_encode($data);
    exit;
}

function isPriceOfferInSessionValid(array $session): bool
{
    return isset($session['priceOffer'])
        && !($session['priceOffer'] instanceof __PHP_Incomplete_Class);
}

function isSecurityDoorsPriceOfferInSessionValid(array $session): bool
{
    return isset($session['securityDoorsPriceOffer'])
        && !($session['securityDoorsPriceOffer'] instanceof __PHP_Incomplete_Class);
}