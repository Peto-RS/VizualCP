<?php

require_once __DIR__ . '/../req/api-price-offer-request-objects.php';
require_once __DIR__ . '/../../common/validation-message.php';

/**
 * @return ValidationMessage[]
 */
function validateSecurityDoorsPriceOfferForm(SecurityDoorsPriceOfferApiRequest $apiRequest): array
{
    return array_merge(
        validateContactEmail($apiRequest->priceOffer->contact)
    );
}
?>