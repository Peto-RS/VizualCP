import {ref} from "vue";

export function useRecaptcha() {
    const token = ref<string>();

    async function load(language: string): Promise<void> {
        // Remove previous script
        document
            .querySelector('script[data-recaptcha]')
            ?.remove();

        // Remove previous global object
        (window as any).grecaptcha = undefined;

        await new Promise<void>((resolve, reject) => {
            window.onRecaptchaLoaded = () => {
                resolve();
            };

            const script = document.createElement("script");

            script.dataset.recaptcha = "true";
            script.async = true;
            script.defer = true;
            script.src =
                `https://www.google.com/recaptcha/api.js` +
                `?render=explicit&hl=${language}&onload=onRecaptchaLoaded`;

            script.onerror = () => reject(new Error("Failed to load reCAPTCHA"));

            document.head.appendChild(script);
        });

        delete window.onRecaptchaLoaded;
    }

    async function render(
        element: HTMLElement,
        siteKey: string,
        language: string
    ): Promise<void> {
        await load(language);

        window.grecaptcha.render(element, {
            sitekey: siteKey,
            callback(tokenValue: string) {
                token.value = tokenValue;
            },
            "expired-callback"() {
                token.value = undefined;
            }
        });
    }

    return {
        token,
        render
    };
}