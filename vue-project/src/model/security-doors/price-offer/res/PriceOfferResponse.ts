import {AddressResponse} from "@/model/interior-doors/price-offer/res/AddressResponse.js";
import {ContactResponse} from "@/model/interior-doors/price-offer/res/ContactResponse.js";
import {HandleResponse} from "@/model/interior-doors/price-offer/res/HandleResponse.js";
import {SecurityDoorsDoorResponse} from "./SecurityDoorsDoorResponse.js";
import {SecurityDoorsSectionsCalculatedPriceResponse} from "./SecurityDoorsSectionsCalculatedPriceResponse.js";

export interface PriceOfferResponse {
    address: AddressResponse
    assemblyDoorsCalculatedPrice: number
    assemblyDoorsCost: number | null
    assemblyDoorsCount: number | null
    calculatedPrice: number
    calculatedPriceVat: number
    contact: ContactResponse
    currency: string
    deliveryPrice: number
    deliveryPricePerKm: number
    doors: SecurityDoorsDoorResponse[]
    handle: HandleResponse | null
    isAssemblyDoorsCountDirty: boolean | null
    note: string | null
    sectionsCalculatedPrice: SecurityDoorsSectionsCalculatedPriceResponse
}
