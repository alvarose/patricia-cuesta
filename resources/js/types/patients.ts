export type PatientStatusValue = 'active' | 'paused' | 'discharged';

/** Fila del listado de pacientes. */
export interface Patient {
    id: number;
    name: string;
    initials: string;
    since: string;
    topic: string;
    topicValue: string | null;
    sessions: number;
    next: string | null;
    status: PatientStatusValue;
    statusLabel: string;
    email: string | null;
    phone: string | null;
    notes: string | null;
}

export interface PatientFilters {
    search: string;
    status: string;
}

export interface StatusOption {
    value: PatientStatusValue;
    label: string;
}
