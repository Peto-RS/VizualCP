import {ApiResponse} from "@/model/api/res/price-offer/ApiResponse.js";
import {SelectedDoorResponse} from "@/model/api/res/price-offer/SelectedDoorResponse.js";
import {CustomHandleResponse} from "@/model/api/res/price-offer/CustomHandleResponse.js";
import {AddressResponse} from "@/model/api/res/price-offer/AddressResponse.js";
import {ContactResponse} from "@/model/api/res/price-offer/ContactResponse.js";
import {RosetteResponse} from "@/model/api/res/price-offer/RosetteResponse.js";
import {AestheticAccessoriesResponse} from "@/model/api/res/price-offer/AestheticAccessoriesResponse.js";
import {SpecialSurchargeResponse} from "@/model/api/res/price-offer/SpecialSurchargeResponse.js";
import {TechnicalSurchargeResponse} from "@/model/api/res/price-offer/TechnicalSurchargeResponse.js";
import {LineItemResponse} from "@/model/api/res/price-offer/LineItemResponse.js";
import {SelectedDoorLineItemResponse} from "@/model/api/res/price-offer/SelectedDoorLineItemResponse.js";
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
    assemblyHandlesRosettesCount: number
    contact: FormContact
    doors: FormDoor[],
    doorsMeta: DoorMeta[],
    customHandle: FormCustomHandle,
    handle: FormHandle | null,
    isAssemblyDoorsCountDirty: boolean,
    isAssemblyHandlesRosettesCountDirty: boolean,
    note: string,
    technicalSurcharges: FormTechnicalSurcharge[],
    technicalSurchargesLineItems: FormLineItem[],
    rosettes: FormRosette[],
    rosettesLineItems: FormLineItem[],
    selectedDoorsLineItems: FormSelectedDoorLineItem[],
    aestheticAccessories: FormAestheticAccessory[],
    aestheticAccessoriesLineItems: FormLineItem[],
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

export interface FormAestheticAccessory {
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

export interface FormTechnicalSurcharge {
    id: string
    count: number
    isCountDirty: boolean
}

export function constructReactiveFormPriceOffer(json: ApiResponse | undefined): FormPriceOffer {
    return {
        address: constructReactiveFormAddress(json?.priceOffer.address),
        assemblyDoorsCount: json?.priceOffer.assemblyDoorsCount || 0,
        assemblyHandlesRosettesCount: json?.priceOffer.assemblyHandlesRosettesCount || 0,
        contact: constructReactiveFormContact(json?.priceOffer.contact),
        doors: constructReactiveFormDoors(json?.priceOffer.doors || []),
        doorsMeta: constructDoorsMeta(json?.priceOffer.doors || []),
        customHandle: constructReactiveFormCustomHandle(json?.priceOffer.customHandle),
        handle: json?.priceOffer.handle ? constructReactiveFormHandle(json?.priceOffer.handle) : null,
        isAssemblyDoorsCountDirty: json?.priceOffer.isAssemblyDoorsCountDirty || false,
        isAssemblyHandlesRosettesCountDirty: json?.priceOffer.isAssemblyHandlesRosettesCountDirty || false,
        note: json?.priceOffer.note || "",
        technicalSurcharges: constructReactiveFormTechnicalSurcharges(json?.priceOffer.technicalSurcharges || []),
        technicalSurchargesLineItems: json?.priceOffer.technicalSurchargesLineItems.map(it => constructReactiveFormLineItem(it)) || [],
        rosettes: constructReactiveFormRosettes(json?.priceOffer.rosettes || []),
        rosettesLineItems: json?.priceOffer.rosettesLineItems.map(it => constructReactiveFormLineItem(it)) || [],
        selectedDoorsLineItems: constructReactiveSelectedDoorsLineItems(json?.priceOffer?.selectedDoorsLineItems ?? []),
        aestheticAccessories: constructReactiveAestheticAccessories(json?.priceOffer.aestheticAccessories || []),
        aestheticAccessoriesLineItems: json?.priceOffer.aestheticAccessoriesLineItems.map(it => constructReactiveFormLineItem(it)) || [],
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

export function constructReactiveFormTechnicalSurcharges(charges: TechnicalSurchargeResponse[]): FormTechnicalSurcharge[] {
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

export function constructReactiveAestheticAccessories(aestheticAccessories: AestheticAccessoriesResponse[]): FormAestheticAccessory[] {
    return aestheticAccessories.map(aestheticAccessory => {
        return {
            id: aestheticAccessory.id,
            count: aestheticAccessory.count ?? 0,
            selectedPrice: aestheticAccessory.selectedPrice ?? 0.0
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

export function findTechnicalSurchargeById(id: string, apiResponse: ApiResponse | undefined): TechnicalSurchargeResponse | undefined {
    return apiResponse?.priceOffer.technicalSurcharges.find(it => it.id === id)
}

export function findRosetteById(id: string, apiResponse: ApiResponse | undefined): RosetteResponse | undefined {
    return apiResponse?.priceOffer.rosettes.find(it => it.id === id)
}

export function findAestheticAccessoryById(id: string, apiResponse: ApiResponse | undefined): AestheticAccessoriesResponse | undefined {
    return apiResponse?.priceOffer.aestheticAccessories.find(it => it.id === id)
}

export function findSpecialSurchargeById(id: string, apiResponse: ApiResponse | undefined): SpecialSurchargeResponse | undefined {
    return apiResponse?.priceOffer.specialSurcharges.find(it => it.id === id)
}