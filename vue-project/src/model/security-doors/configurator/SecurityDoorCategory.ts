export interface SecurityDoorCategory {
    categoryId: string
    doorConfiguratorDefault: {
        handle: string | null
        material: string
        type: string
    }
    doorTypes: Record<string, { price: number }>
    name: string
    nameKey: string
}