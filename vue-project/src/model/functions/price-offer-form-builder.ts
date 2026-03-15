import {ApiResponse} from "@/model/api/res/price-offer/ApiResponse.js";
import {SelectedDoorResponse} from "@/model/api/res/price-offer/SelectedDoorResponse.js";
import {CustomHandleResponse} from "@/model/api/res/price-offer/CustomHandleResponse.js";
import {AddressResponse} from "@/model/api/res/price-offer/AddressResponse.js";
import {ContactResponse} from "@/model/api/res/price-offer/ContactResponse.js";
import {RosetteResponse} from "@/model/api/res/price-offer/RosetteResponse.js";
import {SpecialAccessoriesResponse} from "@/model/api/res/price-offer/SpecialAccessoriesResponse.js";
import {SpecialSurchargeResponse} from "@/model/api/res/price-offer/SpecialSurchargeResponse.js";
import {PossibleAdditionalChargeResponse} from "@/model/api/res/price-offer/PossibleAdditionalChargeResponse.js";
import {LineItemResponse} from "@/model/api/res/price-offer/LineItemResponse.js";
import {SelectedDoorLineItemResponse} from "@/model/api/res/price-offer/SelectedDoorLineItemResponse.js";
import {Handle} from "@/model/api/res/configurator/ConfiguratorResponse.js";
import {HandleResponse} from "@/model/api/res/price-offer/HandleResponse.js";

interface FormContact {
    email: string
    fullName: string
    phoneNumber: string
}

export interface FormAddress {
    city: string
    district: string
    street: string
    streetNumber: string
    zipCode: string
}

export interface FormPriceOffer {
    address: FormAddress
    assemblyDoorsCount: number
    assemblyPriceHandlesRosettesCount: number
    contact: FormContact
    doors: FormDoor[],
    doorsMeta: DoorMeta[],
    customHandle: FormCustomHandle,
    handle: FormHandle | null,
    isAssemblyDoorsCountDirty: boolean,
    note: string,
    possibleAdditionalCharges: FormPossibleAdditionalCharge[],
    possibleAdditionalChargesLineItems: FormLineItem[],
    rosettes: FormRosette[],
    rosettesLineItems: FormLineItem[],
    selectedDoorsLineItems: FormSelectedDoorLineItem[],
    specialAccessories: FormSpecialAccessory[],
    specialAccessoriesLineItems: FormLineItem[],
    specialSurcharges: FormSpecialSurcharge[],
    specialSurchargesLineItems: FormLineItem[]
}

export interface FormDoor {
    doorWidth: number
    isDtdSelected: boolean
    isDoorFrameEnabled: boolean
}

export interface DoorMeta {
    category: string
    type: string
    material: string
}

export interface FormCustomHandle {
    count: number
    name: string
    price: number
}

export interface FormHandle {
    count: number
    id: string
    isCountDirty: boolean | null
}

export interface FormLineItem {
    count: number
    name: string
    price: number
}

export interface FormRosette {
    id: string
    count: number
}

export interface FormSelectedDoorLineItem {
    isDoorFrameEnabled: boolean
    name: string
    price: number
    width: string
}

export interface FormSpecialAccessory {
    id: string
    count: number
    selectedPrice: number
}

export interface FormSpecialSurcharge {
    id: string
    count: number
    isAssemblySelected: boolean
    isAssemblySelectedDirty: boolean
}

export interface FormPossibleAdditionalCharge {
    id: string
    count: number
    isCountDirty: boolean
}

export function constructReactiveFormPriceOffer(json: ApiResponse | undefined): FormPriceOffer {
    return {
        address: constructReactiveFormAddress(json?.priceOffer.address),
        assemblyDoorsCount: json?.priceOffer.assemblyDoorsCount || 0,
        assemblyPriceHandlesRosettesCount: json?.priceOffer.assemblyPriceHandlesRosettesCount || 0,
        contact: constructReactiveFormContact(json?.priceOffer.contact),
        doors: constructReactiveFormDoors(json?.priceOffer.doors || []),
        doorsMeta: constructDoorsMeta(json?.priceOffer.doors || []),
        customHandle: constructReactiveFormCustomHandle(json?.priceOffer.customHandle),
        handle: json?.priceOffer.handle ? constructReactiveFormHandle(json?.priceOffer.handle) : null,
        isAssemblyDoorsCountDirty: json?.priceOffer.isAssemblyDoorsCountDirty || false,
        note: json?.priceOffer.note || "",
        possibleAdditionalCharges: constructReactiveFormPossibleAdditionalCharges(json?.priceOffer.possibleAdditionalCharges || []),
        possibleAdditionalChargesLineItems: json?.priceOffer.possibleAdditionalChargesLineItems.map(it => constructReactiveFormLineItem(it)) || [],
        rosettes: constructReactiveFormRosettes(json?.priceOffer.rosettes || []),
        rosettesLineItems: json?.priceOffer.rosettesLineItems.map(it => constructReactiveFormLineItem(it)) || [],
        selectedDoorsLineItems: constructReactiveSelectedDoorsLineItems(json?.priceOffer?.selectedDoorsLineItems ?? []),
        specialAccessories: constructReactiveSpecialAccessories(json?.priceOffer.specialAccessories || []),
        specialAccessoriesLineItems: json?.priceOffer.specialAccessoriesLineItems.map(it => constructReactiveFormLineItem(it)) || [],
        specialSurcharges: constructReactiveSpecialSurcharges(json?.priceOffer.specialSurcharges || []),
        specialSurchargesLineItems: json?.priceOffer.specialSurchargesLineItems.map(it => constructReactiveFormLineItem(it)) || []
    }
}

export function constructReactiveFormContact(contact: ContactResponse | undefined): FormContact {
    return {
        email: contact?.email || "",
        fullName: contact?.fullName || "",
        phoneNumber: contact?.phoneNumber || ""
    }
}

export function constructReactiveFormAddress(address: AddressResponse | undefined): FormAddress {
    return {
        city: address?.city || "",
        district: address?.district || "",
        street: address?.street || "",
        streetNumber: address?.streetNumber || "",
        zipCode: address?.zipCode || ""
    }
}

export function constructReactiveFormDoors(doors: SelectedDoorResponse[]): FormDoor[] {
    return doors.map((it: SelectedDoorResponse) => {
        return {
            doorWidth: it.width,
            isDtdSelected: it.isDtdSelected,
            isDoorFrameEnabled: it.isDoorFrameEnabled
        } as FormDoor
    })
}

export function constructDoorsMeta(doors: SelectedDoorResponse[]): DoorMeta[] {
    return doors.map((it: SelectedDoorResponse) => {
        return {
            category: it.category,
            material: it.material,
            type: it.type
        } as DoorMeta
    })
}

export function constructReactiveFormCustomHandle(handle: CustomHandleResponse | undefined | null): FormCustomHandle {
    return {
        count: handle?.count || 0,
        name: handle?.name || "",
        price: handle?.price || 0
    }
}

export function constructReactiveFormHandle(handle: HandleResponse): FormHandle {
    return {
        count: handle.count || 0,
        id: handle.id || "",
        isCountDirty: handle.isCountDirty
    }
}

export function constructReactiveFormLineItem(handle: LineItemResponse): FormLineItem {
    return {
        count: handle?.count || 0,
        name: handle?.name || "",
        price: handle?.price || 0
    }
}

export function constructReactiveFormPossibleAdditionalCharges(charges: PossibleAdditionalChargeResponse[]): FormPossibleAdditionalCharge[] {
    return charges.map(it => {
        return {
            id: it.id,
            count: it.count ?? 0,
            isCountDirty: it.isCountDirty ?? false
        }
    })
}

export function constructReactiveFormRosettes(rosettes: RosetteResponse[]): FormRosette[] {
    return rosettes.map(rosette => {
        return {
            id: rosette.id,
            count: rosette.count ?? 0
        }
    })
}

export function constructReactiveSpecialAccessories(specialAccessories: SpecialAccessoriesResponse[]): FormSpecialAccessory[] {
    return specialAccessories.map(specialAccessory => {
        return {
            id: specialAccessory.id,
            count: specialAccessory.count ?? 0,
            selectedPrice: specialAccessory.selectedPrice ?? 0.0
        }
    })
}

export function constructReactiveSpecialSurcharges(specialSurcharges: SpecialSurchargeResponse[]): FormSpecialSurcharge[] {
    return specialSurcharges.map(specialSurcharge => {
        return {
            id: specialSurcharge.id,
            count: specialSurcharge.count ?? 0,
            isAssemblySelected: specialSurcharge.isAssemblySelected ?? false,
            isAssemblySelectedDirty: specialSurcharge.isAssemblySelectedDirty ?? false
        }
    })
}

export function constructReactiveSelectedDoorsLineItems(selectedDoorsLineItems: SelectedDoorLineItemResponse[]): FormSelectedDoorLineItem[] {
    return selectedDoorsLineItems.map(it => {
        return {
            isDoorFrameEnabled: it.isDoorFrameEnabled ?? false,
            name: it.name ?? "",
            price: it.price ?? 0,
            width: it.width ?? ""
        }
    })
}

/*
const rosetteMetaById = computed(() =>
  Object.fromEntries(
    apiResponse.priceOffer.rosettes.map(r => [
      r.id,
      {
        label: r.label,
        hint: r.hint,
        imgSrc: r.imgSrc,
        price: r.price
      }
    ])
  )
)
*/

export function findPossibleAdditionalChargeById(id: string, apiResponse: ApiResponse | undefined): PossibleAdditionalChargeResponse | undefined {
    return apiResponse?.priceOffer.possibleAdditionalCharges.find(it => it.id === id)
}

export function findRosetteById(id: string, apiResponse: ApiResponse | undefined): RosetteResponse | undefined {
    return apiResponse?.priceOffer.rosettes.find(it => it.id === id)
}

export function findSpecialAccessoryById(id: string, apiResponse: ApiResponse | undefined): SpecialAccessoriesResponse | undefined {
    return apiResponse?.priceOffer.specialAccessories.find(it => it.id === id)
}

export function findSpecialSurchargeById(id: string, apiResponse: ApiResponse | undefined): SpecialSurchargeResponse | undefined {
    return apiResponse?.priceOffer.specialSurcharges.find(it => it.id === id)
}