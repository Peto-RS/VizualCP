import {AddressReq} from "@/model/interior-doors/price-offer/req/AddressReq.js";
import {ContactReq} from "@/model/interior-doors/price-offer/req/ContactReq.js";
import {HandleReq} from "@/model/interior-doors/price-offer/req/HandleReq.js";
import {SecurityDoorsDoorReq} from "./SecurityDoorsDoorReq.js";

export interface PriceOfferReq {
    address: AddressReq
    assemblyDoorsCount: number
    contact: ContactReq
    currency: string
    doors: SecurityDoorsDoorReq[]
    handle: HandleReq | null
    isAssemblyDoorsCountDirty: boolean
    note: string
}
