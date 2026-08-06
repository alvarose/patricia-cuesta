/** Datos de contacto de la consulta, compartidos en toda la web pública. */
export interface Clinic {
    whatsapp: string | null;
    email: string;
    license: string;
}

/** Contadores del sidebar del panel. Null para quien no ha entrado. */
export interface AdminCounts {
    pendingAppointments: number;
    unreadMessages: number;
}

/** Perfil editable en Ajustes. */
export interface ClinicProfile {
    name: string;
    license_number: string;
    email: string;
    phone: string;
    whatsapp: string;
    photo_url: string | null;
}

/** Motivo de consulta, tal y como lo sirve ConsultationTopic::options(). */
export interface TopicOption {
    value: string;
    label: string;
    description: string;
}
