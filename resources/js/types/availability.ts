/** Franja horaria de un día, en formato "H:i". */
export interface AvailabilityWindow {
    start: string;
    end: string;
}

export interface AvailabilityDay {
    weekday: number;
    label: string;
    enabled: boolean;
    windows: AvailabilityWindow[];
}

/** Parámetros de reserva editables en Disponibilidad. */
export interface BookingSettingsPayload {
    session_minutes: number;
    buffer_minutes: number;
    min_notice_hours: number;
    max_advance_days: number;
}

export interface AbsenceItem {
    id: number;
    range: string;
    note: string | null;
}
