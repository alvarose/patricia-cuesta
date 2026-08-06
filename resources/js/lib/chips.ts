import type { AppointmentStatusValue } from '@/types/appointments';
import type { PatientStatusValue } from '@/types/patients';

/**
 * Las citas y los pacientes comparten la paleta de chips pero no su
 * semántica, así que cada uno tiene su propio mapa: cambiar el color de
 * "cancelada" no debe arrastrar al de "alta".
 */
const APPOINTMENT_CHIPS: Record<AppointmentStatusValue, string> = {
    pending: 'chip--pend',
    confirmed: 'chip--conf',
    completed: 'chip--done',
    cancelled: 'chip--canc',
};

const PATIENT_CHIPS: Record<PatientStatusValue, string> = {
    active: 'chip--conf',
    paused: 'chip--pend',
    discharged: 'chip--done',
};

export const appointmentChipClass = (status: AppointmentStatusValue): string =>
    APPOINTMENT_CHIPS[status] ?? 'chip--pend';

export const patientChipClass = (status: PatientStatusValue): string =>
    PATIENT_CHIPS[status] ?? 'chip--pend';
