import {
    SecurityDoorsConfiguratorAddDoorReq
} from "@/model/security-doors/configurator/req/SecurityDoorsConfiguratorAddDoorReq.js";
import {SecurityDoorsApiRequest} from "@/model/security-doors/price-offer/req/SecurityDoorsApiRequest.js";

const API_URL_PRICE_OFFER = 'api/security-doors/price-offer.php';
const API_CONFIGURATOR_URL = 'api/security-doors/configurator.php';
const API_URL_SUBMISSION = 'api/security-doors/price-offer-submission.php';
const API_URL_TENANT = 'api/security-doors/tenant.php';

export function getSecurityDoorsTenant(slug: string): Promise<Response> {
    return fetch(API_URL_TENANT + `?slug=${encodeURIComponent(slug)}`)
}

export function getConfiguratorSecurityDoors(currency: string): Promise<Response> {
    return fetch(API_CONFIGURATOR_URL + `?currency=${encodeURIComponent(currency)}`)
}

export function deleteFormSecurityDoors(): Promise<Response> {
    return fetch(API_URL_PRICE_OFFER, {
        method: 'DELETE'
    })
}

export function getFormSecurityDoors(currency: string, lang: string): Promise<Response> {
    const params = new URLSearchParams({currency, lang})
    return fetch(API_URL_PRICE_OFFER + `?${params}`)
}

export function updateFormSecurityDoors(req: SecurityDoorsApiRequest, lang: string): Promise<Response> {
    return fetch(API_URL_PRICE_OFFER + `?lang=${encodeURIComponent(lang)}`, {
        method: 'POST',
        headers: {'Content-Type': 'application/json'},
        body: JSON.stringify(req)
    })
}

export function submitFormSecurityDoors(lang: string, recaptchaToken: string | undefined): Promise<Response> {
    return fetch(API_URL_SUBMISSION + `?lang=${encodeURIComponent(lang)}`, {
        method: 'POST',
        headers: {'Content-Type': 'application/json'},
        body: JSON.stringify({
            'g-recaptcha-response': recaptchaToken
        })
    })
}

export function postAddDoorSecurityDoors(req: SecurityDoorsConfiguratorAddDoorReq, lang: string): Promise<Response> {
    return fetch(API_CONFIGURATOR_URL + `?lang=${encodeURIComponent(lang)}`, {
        method: 'POST',
        headers: {'Content-Type': 'application/json'},
        body: JSON.stringify(req)
    })
}
