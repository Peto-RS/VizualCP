import {ApiRequest} from "@/model/interior-doors/price-offer/req/ApiRequest.js";
import {ConfiguratorAddDoorReq} from "@/model/interior-doors/configurator/req/configuratorAddDoorReq.js";

const API_URL_PRICE_OFFER = 'api/interior-doors/price-offer.php';
const API_CONFIGURATOR_URL = 'api/interior-doors/configurator.php';
const API_URL_SUBMISSION = 'api/interior-doors/price-offer-submission.php';

export function getConfigurator(currency: string): Promise<Response> {
    return fetch(API_CONFIGURATOR_URL + `?currency=${currency}`)
}

export function deleteForm(): Promise<Response> {
    return fetch(API_URL_PRICE_OFFER, {
        method: 'DELETE'
    })
}

export function getForm(currency: string, lang: string): Promise<Response> {
    return fetch(API_URL_PRICE_OFFER + `?currency=${currency}&lang=${lang}`)
}

export function updateForm(req: ApiRequest, lang: string): Promise<Response> {
    return fetch(API_URL_PRICE_OFFER + `?lang=${lang}`, {
        method: 'POST',
        headers: {'Content-Type': 'application/json'},
        body: JSON.stringify(req)
    })
}

export function submitForm(lang: string, recaptchaToken: string | undefined): Promise<Response> {
    return fetch(API_URL_SUBMISSION + `?lang=${lang}`, {
        method: 'POST',
        headers: {'Content-Type': 'application/json'},
        body: JSON.stringify({
            'g-recaptcha-response': recaptchaToken
        })
    })
}

export function postAddDoorInteriorDoors(req: ConfiguratorAddDoorReq, lang: string): Promise<Response> {
    return fetch(API_CONFIGURATOR_URL + `?lang=${lang}`, {
        method: 'POST',
        headers: {'Content-Type': 'application/json'},
        body: JSON.stringify(req)
    })
}
