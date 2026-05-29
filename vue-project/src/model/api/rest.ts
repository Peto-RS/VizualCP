import {ApiRequest} from "@/model/api/req/price-offer/ApiRequest.js";
import {AppConfigResponse} from "@/model/api/res/price-offer/AppConfigResponse.js";
import {ConfiguratorAddDoorReq} from "@/model/api/req/configurator/configuratorAddDoorReq.js";

const API_URL_PRICE_OFFER = 'api.php';
const API_CONFIGURATOR_URL = 'api-configurator.php';
const API_URL_SUBMISSION = 'api-submission.php';
const API_URL_CONFIG = 'api-app-config.php';

export async function getAppConfig(): Promise<AppConfigResponse> {
    return fetch(API_URL_CONFIG).then(response => response.json())
}

export function getConfigurator(currency: string): Promise<any> {
    return fetch(API_CONFIGURATOR_URL + `?currency=${currency}`)
}

export function getForm(currency: string): Promise<any> {
    return fetch(API_URL_PRICE_OFFER + `?currency=${currency}`)
}

export function updateForm(req: ApiRequest): Promise<any> {
    return fetch(API_URL_PRICE_OFFER, {
        method: 'POST',
        headers: {'Content-Type': 'application/json'},
        body: JSON.stringify(req)
    })
}

export function submitForm(recaptchaToken: string | undefined): Promise<any> {
    return fetch(API_URL_SUBMISSION, {
        method: 'POST',
        headers: {'Content-Type': 'application/json'},
        body: JSON.stringify({
            'g-recaptcha-response': recaptchaToken
        })
    })
}

export function postAddDoor(req: ConfiguratorAddDoorReq): Promise<any> {
    return fetch(API_CONFIGURATOR_URL, {
        method: 'POST',
        headers: {'Content-Type': 'application/json'},
        body: JSON.stringify(req)
    })
}