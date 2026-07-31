import {PriceOfferResponse} from "./PriceOfferResponse.js";
import {DistrictResponse} from "./DistrictResponse.js";

export interface PriceOfferViewResponse {
    deliveryCountries: string[]
    districts: DistrictResponse[]
    priceOffer: PriceOfferResponse
}