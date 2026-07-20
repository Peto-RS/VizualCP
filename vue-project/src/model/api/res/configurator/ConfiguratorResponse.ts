import {Room} from "@/model/api/res/configurator/Room.js";
import {DoorCategory} from "@/model/api/res/configurator/DoorCategory.js";

export interface Handle {
    availableForDoorCategory?: string[]
    hiddenForDoorCategory?: string[]
    imgSrc: string
    label: string
    labelKey: string
    price: number
}

export interface Material {
    availableForDoorCategory?: string[]
    hiddenForDoorCategory?: string[]
    label: string
    labelKey: string
}

export interface ConfiguratorResponse {
    assemblyDoorsCost: number
    doorCategories: DoorCategory[]
    framePrice: number
    gallery: Array<{
        col: number
        height: number
        imgSrc: string
        width: number
    }>
    glasses: Record<string, {
        label: string
    }>
    handles: Record<string, Handle>
    interiorDoorsPriceFrom: number,
    materials: {
        laminates: Record<string, Material>,
        veneers: Record<string, Material>
    }
    rooms: Room[]
}