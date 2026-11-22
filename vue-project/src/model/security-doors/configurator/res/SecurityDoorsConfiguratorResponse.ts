import {Room} from "@/model/interior-doors/configurator/Room.js";
import {SecurityDoorCategory} from "@/model/security-doors/configurator/SecurityDoorCategory.js";

export interface EquipmentIncluded {
    imgSrc: string
    label: string
    labelKey: string
}

export interface EquipmentOptional {
    id: string
    imgSrc: string
    labelKey: string
    price: number
}

export interface Handle {
    availableForDoorCategory?: string[]
    hiddenForDoorCategory?: string[]
    imgSrc: string
    label: string
    labelKey: string
    price: number
}

export interface FrameColor {
    color: string
    label: string
}

export interface Material {
    label: string
    labelKey: string
}

export interface SecurityDoorsConfiguratorResponse {
    doorCategories: SecurityDoorCategory[]
    equipmentIncluded: EquipmentIncluded[]
    equipmentOptional: EquipmentOptional[]
    frameColors: Record<string, FrameColor>
    handles: Record<string, Handle>
    materials: Record<string, Material>
    rooms: Room[]
}