import {SelectedDoorResponse} from "./SelectedDoorResponse.js";
import {RosetteResponse} from "./RosetteResponse.js";
import {CustomHandleResponse} from "./CustomHandleResponse.js";
import {AddressResponse} from "./AddressResponse.js";
import {ContactResponse} from "./ContactResponse.js";
import {SectionsCalculatedPriceResponse} from "./SectionsCalculatedPriceResponse.js";
import {AestheticAccessoriesResponse} from "./AestheticAccessoriesResponse.js";
import {TechnicalSurchargeResponse} from "./TechnicalSurchargeResponse.js";
import {SpecialSurchargeResponse} from "./SpecialSurchargeResponse.js";
import {LineItemResponse} from "./LineItemResponse.js";
import {SelectedDoorLineItemResponse} from "./SelectedDoorLineItemResponse.js";
import {HandleResponse} from "@/model/interior-doors/price-offer/res/HandleResponse.js";
import {CampaignDetailResponse} from "@/model/interior-doors/price-offer/res/CampaignDetailResponse.js";

export interface PriceOfferResponse {
    address: AddressResponse
    assemblyDoorsCount: number | null
    assemblyDoorsCost: number | null
    assemblyDoorsCostMin: number | null
    assemblyDoorsCalculatedPrice: number
    assemblyHandlesRosettesCount: number | null
    assemblyPriceHandlesRosettesCalculatedPrice: number
    calculatedPrice: number
    calculatedPriceVat: number
    campaignDetails: CampaignDetailResponse[]
    contact: ContactResponse
    currency: string
    customHandle: CustomHandleResponse
    deliveryPrice: number
    deliveryPricePerKm: number
    doors: SelectedDoorResponse[]
    handle: HandleResponse | null
    isAssemblyDoorsCountDirty: boolean | null
    isAssemblyHandlesRosettesCountDirty: boolean | null
    note: string | null
    technicalSurcharges: TechnicalSurchargeResponse[]
    technicalSurchargesLineItems: LineItemResponse[]
    rosettes: RosetteResponse[]
    rosettesLineItems: LineItemResponse[]
    sectionsCalculatedPrice: SectionsCalculatedPriceResponse
    selectedDoorsLineItems: SelectedDoorLineItemResponse[]
    aestheticAccessories: AestheticAccessoriesResponse[]
    aestheticAccessoriesLineItems: LineItemResponse[]
    specialSurcharges: SpecialSurchargeResponse[]
    specialSurchargesLineItems: LineItemResponse[]
}