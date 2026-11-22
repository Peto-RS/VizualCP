import {DistrictResponse} from "@/model/interior-doors/price-offer/res/DistrictResponse.js";
import {PriceOfferResponse} from "@/model/security-doors/price-offer/res/PriceOfferResponse.js";

export interface PriceOfferViewResponse {
    deliveryCountries: string[]
    districts: DistrictResponse[]
    priceOffer: PriceOfferResponse
}