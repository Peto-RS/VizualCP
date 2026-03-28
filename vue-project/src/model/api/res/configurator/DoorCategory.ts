export interface DoorCategory {
    categoryId: string
    constructionPossibilities: Array<{
        description: string
        items: Array<{
            imgSrc: string
            label: string
        }>
    }>
    description: string
    descriptionKey: string
    doorConfiguratorDefault: {
        handle: string | null
        material: string
        type: string
    }
    doorTypes: Record<string, { price: number }>
    excludedDoorPartsFromCanvas: string[]
    gallery: Array<{ imgSrc: string }>
    hiddenConfiguratorSections: string[]
    name: string
    nameKey: string
}