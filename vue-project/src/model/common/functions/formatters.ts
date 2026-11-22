export function formatPrice(price: number | undefined | null, currencySuffix: string): string {
    return (price || 0).toLocaleString('sk-SK', {
        minimumFractionDigits: 0,
        maximumFractionDigits: 2
    }) + " " + currencySuffix
}