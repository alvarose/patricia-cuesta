import type { InertiaLinkProps } from '@inertiajs/vue3';
import type { LucideIcon } from '@lucide/vue';

export type BreadcrumbItem = {
    title: string;
    href: NonNullable<InertiaLinkProps['href']>;
};

export type NavItem = {
    title: string;
    href: NonNullable<InertiaLinkProps['href']>;
    icon?: LucideIcon;
    isActive?: boolean;
};

/**
 * Entrada del sidebar del panel. Además del enlace lleva el encabezado que
 * la cabecera muestra al estar en esa sección.
 */
export type AdminNavItem = {
    label: string;
    href: NonNullable<InertiaLinkProps['href']>;
    eyebrow: string;
    heading: string;
    badge?: number;
};
