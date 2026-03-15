import {Room} from "@/model/api/res/configurator/Room.js";
import {DoorCategory} from "@/model/api/res/configurator/DoorCategory.js";

export interface Handle {
    availableForDoorCategory?: string[]
    hiddenForDoorCategory?: string[]
    imgSrc: string
    label: string
    price: number
}

export interface Material {
    availableForDoorCategory?: string[]
    hiddenForDoorCategory?: string[]
    label: string
}

export interface ConfiguratorResponse {
    doorCategories: DoorCategory[]
    glasses: Record<string, {
        label: string
    }>
    handles: Record<string, Handle>
    materials: {
        laminates: Record<string, Material>,
        veneers: Record<string, Material>
    }
    rooms: Room[]
}