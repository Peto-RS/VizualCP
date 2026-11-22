import {SecurityDoorsApiRequest} from "@/model/security-doors/price-offer/req/SecurityDoorsApiRequest.js";
import {SecurityDoorsDoorReq} from "@/model/security-doors/price-offer/req/SecurityDoorsDoorReq.js";
import {CurrencyEnum} from "@/model/common/enums/CurrencyEnum.js";
import {PriceOfferViewResponse} from "@/model/security-doors/price-offer/res/PriceOfferViewResponse.js";
import {SecurityDoorsFormPriceOffer} from "./security-doors-price-offer-form-builder.js";

export function prepareRequest(
    reactiveForm: SecurityDoorsFormPriceOffer,
    apiResponse: PriceOfferViewResponse | undefined,
    currency: string | null
): SecurityDoorsApiRequest | undefined {
    if (!apiResponse) {
        return
    }

    const doors: SecurityDoorsDoorReq[] = reactiveForm.doors.map((door, index) => {
        const meta = reactiveForm.doorsMeta[index];
        return {
            frameColor: meta.frameColor,
            material: meta.material,
            type: meta.type,
            width: door.doorWidth || null
        }
    })

    return {
        priceOffer: {
            address: {
                city: reactiveForm.address.city,
                deliveryCountry: reactiveForm.address.deliveryCountry,
                district: reactiveForm.address.district,
                isDeliveryCountryDirty: reactiveForm.address.isDeliveryCountryDirty,
                street: reactiveForm.address.street,
                streetNumber: reactiveForm.address.streetNumber,
                zipCode: reactiveForm.address.zipCode
            },
            assemblyDoorsCount: reactiveForm.assemblyDoorsCount,
            contact: {
                email: reactiveForm.contact.email,
                fullName: reactiveForm.contact.fullName,
                phoneNumber: reactiveForm.contact.phoneNumber
            },
            currency: currency ?? CurrencyEnum.EUR,
            doors,
            handle: reactiveForm.handle ? {
                count: reactiveForm.handle.count,
                id: reactiveForm.handle.id,
                isCountDirty: reactiveForm.handle.isCountDirty
            } : null,
            isAssemblyDoorsCountDirty: reactiveForm.isAssemblyDoorsCountDirty,
            note: reactiveForm.note
        }
    }
}
