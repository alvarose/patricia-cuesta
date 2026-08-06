import { onBeforeUnmount, onMounted } from 'vue';

/**
 * Animación de entrada suave para los elementos .reveal
 * (fade + rise de 18px, respeta prefers-reduced-motion vía CSS).
 */
export function useReveal(): void {
    let observer: IntersectionObserver | null = null;
    let timeout: ReturnType<typeof setTimeout> | null = null;
    let onScroll: (() => void) | null = null;

    onMounted(() => {
        const els = [...document.querySelectorAll<HTMLElement>('.reveal')];
        const inView = (el: HTMLElement) =>
            el.getBoundingClientRect().top < window.innerHeight * 0.94;
        const reveal = (el: HTMLElement, animate: boolean) => {
            if (!animate) {
                el.classList.add('snap');
            }

            el.classList.add('in');
        };

        els.forEach((el) => {
            if (inView(el)) {
                reveal(el, false);
            }
        });

        observer = new IntersectionObserver(
            (entries) => {
                entries.forEach((entry) => {
                    if (entry.isIntersecting) {
                        reveal(entry.target as HTMLElement, true);
                        observer?.unobserve(entry.target);
                    }
                });
            },
            { threshold: 0.12, rootMargin: '0px 0px -8% 0px' },
        );

        els.forEach((el) => {
            if (!el.classList.contains('in')) {
                observer?.observe(el);
            }
        });

        onScroll = () =>
            els.forEach((el) => {
                if (!el.classList.contains('in') && inView(el)) {
                    reveal(el, false);
                }
            });
        window.addEventListener('scroll', onScroll, { passive: true });

        timeout = setTimeout(() => {
            els.forEach((el) => {
                if (!el.classList.contains('in')) {
                    reveal(el, false);
                }
            });
        }, 700);
    });

    onBeforeUnmount(() => {
        observer?.disconnect();

        if (onScroll) {
            window.removeEventListener('scroll', onScroll);
        }

        if (timeout) {
            clearTimeout(timeout);
        }
    });
}
