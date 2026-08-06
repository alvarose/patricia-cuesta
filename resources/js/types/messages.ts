export type ContactPreferenceValue = 'whatsapp' | 'call' | 'email';

/** Mensaje en una lista (bandeja de entrada y resumen del panel). */
export interface ContactMessageListItem {
    id: number;
    name: string;
    preview: string;
    when: string;
    unread: boolean;
    preference: string;
}

/** Mensaje abierto en el panel de lectura. */
export interface ContactMessageDetail {
    id: number;
    name: string;
    when: string;
    email: string;
    phone: string | null;
    preference: string;
    preferredTime: string | null;
    body: string;
    replied: boolean;
    hasPatient: boolean;
}
