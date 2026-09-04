export interface AppConfigResponse {
    baseUrl: string | null
    profile: "DEV" | "PROD" | "STAGE"
    reCaptchaEnabled: boolean
    reCaptchaSiteKey: string | null
}