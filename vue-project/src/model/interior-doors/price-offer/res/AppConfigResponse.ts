export interface AppConfigResponse {
    baseUrl: string | null
    profile: "dev" | "prod" | "stage"
    reCaptchaEnabled: boolean
    reCaptchaSiteKey: string | null
}