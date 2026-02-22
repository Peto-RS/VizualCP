import {Room} from "@/model/api/res/configurator/Room.js";
import {DoorCategory} from "@/model/api/res/configurator/DoorCategory.js";

export interface Material {
    availableFor?: string[]
    hiddenFor?: string[]
    label: string
}

export interface ConfiguratorResponse {
    doorCategories: DoorCategory[]
    glasses: Record<string, {
        label: string
    }>
    handles: Record<string, {
        label: string
        price: number
    }>
    materials: {
        laminates: Record<string, Material>,
        veneers: Record<string, Material>
    }
    rooms: Room[]
}