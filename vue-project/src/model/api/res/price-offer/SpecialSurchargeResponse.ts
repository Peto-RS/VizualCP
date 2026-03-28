import {HintInterface} from "../../../interface/HintInterface.js";

export interface SpecialSurchargeResponse extends HintInterface {
    id: string
    calculatedPrice: number
    configuredPrice: number | null
    count: number | null
    header: string | null
    headerKey: string | null
    hint: string | null
    hintKey: string | null
    imgSrc: string | null
    isAssemblySelected: boolean | null
    isAssemblySelectedDirty: boolean | null
    label: string | null
    labelKey: string | null
    labelAssembly: string | null
    labelAssemblyKey: string | null
    youtubeVideoCode: string | null
    videoSrc: string | null
}