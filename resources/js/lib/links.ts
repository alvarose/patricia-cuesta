import type { InertiaLinkProps } from '@inertiajs/vue3';
import { home } from '@/routes';

/**
 * Los helpers de Wayfinder devuelven un objeto de ruta, no una cadena.
 * Esto extrae la URL cuando hace falta compararla o concatenarla.
 */
export const hrefUrl = (href: NonNullable<InertiaLinkProps['href']>): string =>
    typeof href === 'string' ? href : href.url;

/** Enlace a una sección de la portada, desde cualquier página. */
export const homeSection = (anchor: string): string =>
    `${home.url()}#${anchor}`;

/**
 * Enlace de WhatsApp a partir del teléfono configurado. Devuelve null si no
 * hay número, y entonces el botón simplemente no se muestra.
 */
export const whatsappHref = (
    phone: string | null | undefined,
): string | null => {
    const digits = phone?.replace(/\D/g, '');

    return digits ? `https://wa.me/${digits}` : null;
};
