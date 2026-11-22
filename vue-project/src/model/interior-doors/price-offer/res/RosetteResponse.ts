import {HintInterface} from "@/model/common/interfaces/HintInterface.js";

export interface RosetteResponse extends HintInterface {
    id: string
    calculatedPrice: number
    count: number | null
    header: string | null
    headerKey: string | null
    hint: string | null
    hintKey: string | null
    imgSrc: string | null
    label: string | null
    labelKey: string | null
    price: number | null
    youtubeVideoCode: string | null
    videoSrc: string | null
}