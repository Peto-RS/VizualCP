export interface DoorCategory {
    categoryId: string
    description: string
    doorConfiguratorDefault: {
        handle: string | null
        material: string
        type: string
    }
    doorTypes: Record<string, { price: number }>
    gallery: Array<{ imgSrc: string }>
    hiddenConfiguratorCategories: string[]
    name: string
}