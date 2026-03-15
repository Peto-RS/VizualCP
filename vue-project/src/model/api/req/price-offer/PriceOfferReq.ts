import {DoorReq} from "./DoorReq.js";
import {RosetteReq} from "./RosetteReq.js";
import {CustomHandleReq} from "./CustomHandleReq.js";
import {AddressReq} from "./AddressReq.js";
import {ContactReq} from "./ContactReq.js";
import {SpecialAccessoriesReq} from "./SpecialAccessoriesReq.js";
import {PossibleAdditionalChargeReq} from "./PossibleAdditionalChargeReq.js";
import {SpecialSurchargeReq} from "./SpecialSurchargeReq.js";
import {LineItemReq} from "./LineItemReq.js";
import {SelectedDoorLineItemRequest} from "./SelectedDoorLineItemRequest.js";
import {HandleReq} from "@/model/api/req/price-offer/HandleReq.js";

export interface PriceOfferReq {
    address: AddressReq
    assemblyDoorsCount: number
    assemblyPriceHandlesRosettesCount: number
    contact: ContactReq
    customHandle: CustomHandleReq
    doors: DoorReq[]
    handle: HandleReq | null
    isAssemblyDoorsCountDirty: boolean
    note: string
    possibleAdditionalCharges: PossibleAdditionalChargeReq[]
    possibleAdditionalChargesLineItems: LineItemReq[]
    rosettes: RosetteReq[]
    rosettesLineItems: LineItemReq[]
    selectedDoorsLineItems: SelectedDoorLineItemRequest[]
    specialAccessories: SpecialAccessoriesReq[]
    specialAccessoriesLineItems: LineItemReq[]
    specialSurcharges: SpecialSurchargeReq[]
    specialSurchargesLineItems: LineItemReq[]
}