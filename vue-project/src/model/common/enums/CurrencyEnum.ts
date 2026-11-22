export enum CurrencyEnum {
    CZK = 'CZK',
    EUR = 'EUR'
}

export const CURRENCY_SIGN: Record<string, string> = {
    [CurrencyEnum.CZK]: 'Kč',
    [CurrencyEnum.EUR]: '€',
}