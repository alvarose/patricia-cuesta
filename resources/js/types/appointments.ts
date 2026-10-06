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

/** Celda de la rejilla de mes de la agenda. */
export interface MonthDayCell {
    date: string;
    num: number;
    sessions: number;
    closed: boolean;
    absent: boolean;
    today: boolean;
    inMonth: boolean;
}

/** Rejilla de mes de la agenda: siempre 42 celdas, empezando en lunes. */
export interface MonthCalendar {
    label: string;
    prev: string;
    next: string;
    today: string;
    isCurrentMonth: boolean;
    days: MonthDayCell[];
}
