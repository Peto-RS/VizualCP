import {AppConfigResponse} from "@/model/interior-doors/price-offer/res/AppConfigResponse.js";

const API_URL_CONFIG = 'api/app-config.php';

export async function getAppConfig(): Promise<AppConfigResponse> {
    return fetch(API_URL_CONFIG).then(response => response.json())
}