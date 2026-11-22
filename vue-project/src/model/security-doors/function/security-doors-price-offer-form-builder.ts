import {PriceOfferViewResponse} from "@/model/security-doors/price-offer/res/PriceOfferViewResponse.js";
import {AddressResponse} from "@/model/interior-doors/price-offer/res/AddressResponse.js";
import {ContactResponse} from "@/model/interior-doors/price-offer/res/ContactResponse.js";
import {HandleResponse} from "@/model/interior-doors/price-offer/res/HandleResponse.js";
import {SecurityDoorsDoorResponse} from "@/model/security-doors/price-offer/res/SecurityDoorsDoorResponse.js";

interface FormContact {
    email: string
    fullName: string
    phoneNumber: string
}

export interface FormAddress {
    city: string
    deliveryCountry: string
    district: string
    isDeliveryCountryDirty: boolean
    street: string
    streetNumber: string
    zipCode: string
}

export interface SecurityDoorsFormPriceOffer {
    address: FormAddress
    assemblyDoorsCount: number
    contact: FormContact
    doors: FormDoor[]
    doorsMeta: DoorMeta[]
    handle: FormHandle | null
    isAssemblyDoorsCountDirty: boolean
    note: string
}

export interface FormDoor {
    doorWidth: string
    isDtdSelected: boolean
    isDoorFrameEnabled: boolean
}

export interface DoorMeta {
    category: string
    frameColor: string
    type: string
    material: string
}

export interface FormHandle {
    count: number
    id: string
    isCountDirty: boolean | null
}

export function constructReactiveFormPriceOffer(json: PriceOfferViewResponse | undefined): SecurityDoorsFormPriceOffer {
    const doors = json?.priceOffer.doors ?? [];

    return {
        address: constructReactiveFormAddress(json?.priceOffer.address),
        assemblyDoorsCount: json?.priceOffer.assemblyDoorsCount ?? 0,
        contact: constructReactiveFormContact(json?.priceOffer.contact),
        doors: constructReactiveFormDoors(doors),
        doorsMeta: constructDoorsMeta(doors),
        handle: json?.priceOffer.handle ? constructReactiveFormHandle(json.priceOffer.handle) : null,
        isAssemblyDoorsCountDirty: json?.priceOffer.isAssemblyDoorsCountDirty ?? false,
        note: json?.priceOffer.note ?? ""
    }
}

export function constructReactiveFormContact(contact: ContactResponse | undefined): FormContact {
    return {
        email: contact?.email ?? "",
        fullName: contact?.fullName ?? "",
        phoneNumber: contact?.phoneNumber ?? ""
    }
}

export function constructReactiveFormAddress(address: AddressResponse | undefined): FormAddress {
    return {
        city: address?.city ?? "",
        deliveryCountry: address?.deliveryCountry ?? "",
        district: address?.district ?? "",
        isDeliveryCountryDirty: address?.isDeliveryCountryDirty ?? false,
        street: address?.street ?? "",
        streetNumber: address?.streetNumber ?? "",
        zipCode: address?.zipCode ?? ""
    }
}

export function constructReactiveFormDoors(doors: SecurityDoorsDoorResponse[]): FormDoor[] {
    return doors.map(door => ({
        doorWidth: door.width ?? "",
        isDtdSelected: false,
        isDoorFrameEnabled: false
    }))
}

export function constructDoorsMeta(doors: SecurityDoorsDoorResponse[]): DoorMeta[] {
    return doors.map(door => ({
        category: "security",
        frameColor: door.frameColor ?? "",
        material: door.material ?? "",
        type: door.type ?? ""
    }))
}

export function constructReactiveFormHandle(handle: HandleResponse): FormHandle {
    return {
        count: handle.count ?? 0,
        id: handle.id ?? "",
        isCountDirty: handle.isCountDirty
    }
}
