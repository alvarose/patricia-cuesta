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
    sessionMinutes: number;
    bufferMinutes: number;
    minNoticeHours: number;
    maxAdvanceDays: number;
}

export interface AbsenceItem {
    id: number;
    range: string;
    note: string | null;
}

/** Valores que el panel puede ofrecer para cada ajuste de sesion. */
export type SessionOptions = Record<
    keyof BookingSettingsPayload,
    readonly number[]
>;
