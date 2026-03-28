import {DoorReq} from "./DoorReq.js";
import {RosetteReq} from "./RosetteReq.js";
import {CustomHandleReq} from "./CustomHandleReq.js";
import {AddressReq} from "./AddressReq.js";
import {ContactReq} from "./ContactReq.js";
import {AestheticAccessoriesReq} from "./AestheticAccessoriesReq.js";
import {TechnicalSurchargeReq} from "./TechnicalSurchargeReq.js";
import {SpecialSurchargeReq} from "./SpecialSurchargeReq.js";
import {LineItemReq} from "./LineItemReq.js";
import {SelectedDoorLineItemRequest} from "./SelectedDoorLineItemRequest.js";
import {HandleReq} from "@/model/api/req/price-offer/HandleReq.js";

export interface PriceOfferReq {
    address: AddressReq
    assemblyDoorsCount: number
    assemblyHandlesRosettesCount: number
    contact: ContactReq
    customHandle: CustomHandleReq
    doors: DoorReq[]
    handle: HandleReq | null
    isAssemblyDoorsCountDirty: boolean
    isAssemblyHandlesRosettesCountDirty: boolean
    note: string
    technicalSurcharges: TechnicalSurchargeReq[]
    technicalSurchargesLineItems: LineItemReq[]
    rosettes: RosetteReq[]
    rosettesLineItems: LineItemReq[]
    selectedDoorsLineItems: SelectedDoorLineItemRequest[]
    aestheticAccessories: AestheticAccessoriesReq[]
    aestheticAccessoriesLineItems: LineItemReq[]
    specialSurcharges: SpecialSurchargeReq[]
    specialSurchargesLineItems: LineItemReq[]
}