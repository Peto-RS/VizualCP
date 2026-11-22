import {SecurityDoorCategory} from "@/model/security-doors/configurator/SecurityDoorCategory.js";

export interface SelectedSecurityDoor {
    categoryId: string
    doorCategory: SecurityDoorCategory
    frameColor: string
    handleId: string | null
    material: string
    type: string
}