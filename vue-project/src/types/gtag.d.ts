export {};

declare global {
    interface Window {
        gtag?: (...args: any[]) => void;
        dataLayer?: any[];
        grecaptcha: ReCaptchaV2.ReCaptcha;
        onRecaptchaLoaded?: () => void;
    }
}