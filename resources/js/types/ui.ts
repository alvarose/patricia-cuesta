export type Appearance = 'light' | 'dark' | 'system';
export type ResolvedAppearance = 'light' | 'dark';

export type AppVariant = 'header' | 'sidebar';

export type FlashToast = {
    type: 'success' | 'info' | 'warning' | 'error';
    message: string;
};

/** Tarjeta de cabecera del panel: etiqueta, cifra grande y nota al pie. */
export interface Stat {
    label: string;
    value: string;
    note: string;
}
