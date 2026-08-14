export type AppointmentStatusValue =
    'pending' | 'confirmed' | 'cancelled' | 'completed';

/** Cita en una lista de agenda. */
export interface Appointment {
    id: number;
    time: string;
    name: string;
    topic: string;
    status: AppointmentStatusValue;
    statusLabel: string;
    statusBadge: string;
    statusDot: string;
}

/** Cita con los datos de contacto, para la agenda del panel. */
export interface AppointmentWithContact extends Appointment {
    hasPatient: boolean;
    email: string;
    phone: string | null;
}

/** Solicitud llegada desde la web, a la espera de confirmación. */
export interface PendingAppointment {
    id: number;
    name: string;
    when: string;
    topic: string;
}

/** Celda del selector de días de la semana. */
export interface WeekDayCell {
    date: string;
    dow: string;
    num: number;
    hasAppointments: boolean;
}
