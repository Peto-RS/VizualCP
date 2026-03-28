import {HintInterface} from "../../../interface/HintInterface.js";

export interface AestheticAccessoriesResponse extends HintInterface {
    id: string
    calculatedPrice: number
    configuredPrice: number | null
    count: number | null
    header: string | null
    headerKey: string
    hint: string | null
    hintKey: string | null
    imgSrc: string | null
    label: string | null
    labelKey: string | null
    selectedPrice: number | null
    youtubeVideoCode: string | null
    videoSrc: string | null
}