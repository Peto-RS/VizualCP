import {DoorCategory} from "@/model/api/res/configurator/DoorCategory.js";

export interface SelectedDoor {
    categoryId: string
    doorCategory: DoorCategory
    handleId: string | null
    material: string
    type: string
}