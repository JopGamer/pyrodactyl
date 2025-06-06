import { Action, action } from 'easy-peasy';

// Define captcha configuration type
interface CaptchaConfig {
    driver: 'none' | 'hcaptcha' | 'mcaptcha' | 'turnstile' | 'friendly';
    hcaptcha: {
        siteKey: string;
    };
    mcaptcha: {
        siteKey: string;
        endpoint: string;
    };
    turnstile: {
        siteKey: string;
    };
    friendly: {
        siteKey: string;
    };
    recaptcha: {
        siteKey: string;
    };
}

// Define OpenID Connect configuration type
interface OpenIDConfig {
    enabled: boolean;
    client_id: string;
    issuer: string;
    name?: string;
    icon?: string;
}

export interface SiteSettings {
    name: string;
    locale: string;
    captcha: CaptchaConfig;
    openid?: OpenIDConfig;
}

export interface SettingsStore {
    data?: SiteSettings;
    setSettings: Action<SettingsStore, SiteSettings>;
}

const settings: SettingsStore = {
    data: undefined,
    setSettings: action((state, payload) => {
        state.data = payload;
    }),
};

export default settings;
