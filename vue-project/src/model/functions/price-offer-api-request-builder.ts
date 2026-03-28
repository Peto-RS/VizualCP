import {FormLineItem, FormPriceOffer, FormSelectedDoorLineItem} from "./price-offer-form-builder.js";
import {ApiResponse} from "@/model/api/res/price-offer/ApiResponse.js";
import {ApiRequest} from "@/model/api/req/price-offer/ApiRequest.js";
import {DoorReq} from "@/model/api/req/price-offer/DoorReq.js";
import {RosetteReq} from "@/model/api/req/price-offer/RosetteReq.js";
import {AestheticAccessoriesReq} from "@/model/api/req/price-offer/AestheticAccessoriesReq.js";
import {TechnicalSurchargeReq} from "@/model/api/req/price-offer/TechnicalSurchargeReq.js";
import {SpecialSurchargeReq} from "@/model/api/req/price-offer/SpecialSurchargeReq.js";
import {LineItemReq} from "@/model/api/req/price-offer/LineItemReq.js";
import {SelectedDoorLineItemRequest} from "@/model/api/req/price-offer/SelectedDoorLineItemRequest.js";

export function prepareRequest(
    reactiveForm: FormPriceOffer,
    apiResponse: ApiResponse | undefined
): ApiRequest | undefined {
    if (!apiResponse) {
        return
    }

    const doors: DoorReq[] = reactiveForm.doors.map((door, i) => {
        const meta = reactiveForm.doorsMeta[i]
        return {
            category: meta.category,
            material: meta.material,
            type: meta.type,
            width: door.doorWidth,
            isDoorFrameEnabled: door.isDoorFrameEnabled,
            isDtdSelected: door.isDtdSelected
        }
    })

    const technicalSurcharges: TechnicalSurchargeReq[] = reactiveForm.technicalSurcharges.map(r => {
        return {
            id: r.id,
            count: r.count || 0,
            isCountDirty: r.isCountDirty
        }
    })
    const technicalSurchargesLineItems: LineItemReq[] = reactiveForm.technicalSurchargesLineItems.map(it => mapLineItem(it));

    const rosettes: RosetteReq[] = reactiveForm.rosettes.map(it => {
        return {
            id: it.id,
            count: it.count || 0
        }
    })

    const rosettesLineItems: LineItemReq[] = reactiveForm.rosettesLineItems.map(it => mapLineItem(it));

    const selectedDoorsLineItems: SelectedDoorLineItemRequest[] = reactiveForm.selectedDoorsLineItems.map(it => mapSelectedDoorLineItem(it));
    const aestheticAccessories: AestheticAccessoriesReq[] = reactiveForm.aestheticAccessories.map(it => {
        return {
            id: it.id,
            count: it.count || 0,
            selectedPrice: it.selectedPrice || 0
        }
    })
    const aestheticAccessoriesLineItems: LineItemReq[] = reactiveForm.aestheticAccessoriesLineItems.map(it => mapLineItem(it));

    const specialSurcharges: SpecialSurchargeReq[] = reactiveForm.specialSurcharges.map(it => {
        return {
            id: it.id,
            count: it.count || 0,
            isAssemblySelected: it.isAssemblySelected || false,
            isAssemblySelectedDirty: it.isAssemblySelectedDirty || false
        }
    })
    const specialSurchargesLineItems: LineItemReq[] = reactiveForm.specialSurchargesLineItems.map(it => mapLineItem(it));

    return {
        priceOffer: {
            address: {
                city: reactiveForm.address.city || "",
                district: reactiveForm.address.district || "",
                street: reactiveForm.address.street || "",
                streetNumber: reactiveForm.address.streetNumber || "",
                zipCode: reactiveForm.address.zipCode || ""
            },
            assemblyDoorsCount: reactiveForm.assemblyDoorsCount || 0,
            assemblyHandlesRosettesCount: reactiveForm.assemblyHandlesRosettesCount || 0,
            contact: {
                email: reactiveForm.contact.email || "",
                fullName: reactiveForm.contact.fullName || "",
                phoneNumber: reactiveForm.contact.phoneNumber || ""
            },
            doors: doors,
            customHandle: {
                name: reactiveForm.customHandle.name || "",
                price: reactiveForm.customHandle.price || 0,
                count: reactiveForm.customHandle.count || 0
            },
            handle: reactiveForm.handle ? {
                count: reactiveForm.handle.count,
                id: reactiveForm.handle.id,
                isCountDirty: reactiveForm.handle.isCountDirty
            } : null,
            isAssemblyDoorsCountDirty: reactiveForm.isAssemblyDoorsCountDirty || false,
            isAssemblyHandlesRosettesCountDirty: reactiveForm.isAssemblyHandlesRosettesCountDirty || false,
            note: reactiveForm.note || "",
            technicalSurcharges: technicalSurcharges,
            technicalSurchargesLineItems: technicalSurchargesLineItems,
            rosettes: rosettes,
            rosettesLineItems: rosettesLineItems,
            selectedDoorsLineItems: selectedDoorsLineItems,
            aestheticAccessories: aestheticAccessories,
            aestheticAccessoriesLineItems: aestheticAccessoriesLineItems,
            specialSurcharges: specialSurcharges,
            specialSurchargesLineItems: specialSurchargesLineItems
        }
    };
}

function mapLineItem(it: FormLineItem): LineItemReq {
    return {
        name: it.name,
        price: it.price || 0,
        count: it.count || 0
    }
}

function mapSelectedDoorLineItem(it: FormSelectedDoorLineItem): SelectedDoorLineItemRequest {
    return {
        isDoorFrameEnabled: it.isDoorFrameEnabled,
        name: it.name,
        price: it.price,
        width: it.width
    }
}