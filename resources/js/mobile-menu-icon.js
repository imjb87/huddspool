import gsap from 'gsap';
import { MorphSVGPlugin } from 'gsap/MorphSVGPlugin';

gsap.registerPlugin(MorphSVGPlugin);

const burgerShapes = {
    top: 'M4 6l16 0',
    middle: 'M4 12l16 0',
    bottom: 'M4 18l16 0',
};

const closeShapes = {
    top: 'M6 6l12 12',
    middle: 'M12 12l0 0',
    bottom: 'M18 6l-12 12',
};

const wobbleShapes = {
    top: 'M4 10 C8 9 16 15 20 14',
    middle: 'M4 12 C8 12 16 12 20 12',
    bottom: 'M4 14 C8 15 16 9 20 10',
};

const springEase = 'elastic.out(1.2, 0.55)';

const timelines = new WeakMap();

const prefersReducedMotion = () => window.matchMedia?.('(prefers-reduced-motion: reduce)').matches ?? false;

const getIconPaths = (icon) => ({
    top: icon.querySelector('[data-mobile-menu-icon-top]'),
    middle: icon.querySelector('[data-mobile-menu-icon-middle]'),
    bottom: icon.querySelector('[data-mobile-menu-icon-bottom]'),
});

const getIconGroup = (icon) => icon.querySelector('[data-mobile-menu-icon-group]');

const resetIconGroup = (group) => {
    gsap.set(group, {
        rotation: 0,
        scale: 1,
        transformOrigin: '50% 50%',
        svgOrigin: '12 12',
    });
};

const setIconState = (icon, isOpen) => {
    const paths = getIconPaths(icon);
    const group = getIconGroup(icon);
    const shapes = isOpen ? closeShapes : burgerShapes;

    resetIconGroup(group);
    gsap.set(paths.top, { morphSVG: shapes.top });
    gsap.set(paths.middle, { autoAlpha: isOpen ? 0 : 1, morphSVG: shapes.middle });
    gsap.set(paths.bottom, { morphSVG: shapes.bottom });
};

const animateIcon = (icon, isOpen) => {
    if (!icon) {
        return;
    }

    const paths = getIconPaths(icon);
    const group = getIconGroup(icon);

    if (Object.values(paths).some((path) => !path) || !group) {
        return;
    }

    const nextState = isOpen ? 'open' : 'closed';
    const activeTimeline = timelines.get(icon);

    if (icon.dataset.mobileMenuIconState === nextState) {
        return;
    }

    activeTimeline?.kill();

    if (prefersReducedMotion()) {
        setIconState(icon, isOpen);
        icon.dataset.mobileMenuIconState = nextState;

        return;
    }

    const timeline = gsap.timeline({
        defaults: {
            duration: 0.24,
            ease: 'power2.inOut',
        },
        onComplete: () => {
            setIconState(icon, isOpen);

            if (timelines.get(icon) === timeline) {
                timelines.delete(icon);
            }
        },
    });

    if (isOpen) {
        timeline
            .to(group, { rotation: -14, scale: 0.78, duration: 0.14, ease: 'power3.in' }, 0)
            .to(paths.top, { morphSVG: wobbleShapes.top, duration: 0.2, ease: 'power3.out' }, 0)
            .to(paths.bottom, { morphSVG: wobbleShapes.bottom, duration: 0.2, ease: 'power3.out' }, 0)
            .to(paths.middle, {
                autoAlpha: 0,
                morphSVG: wobbleShapes.middle,
                duration: 0.14,
                ease: 'power2.in',
            }, 0)
            .to(group, { rotation: 7, scale: 1.1, duration: 0.16, ease: 'power2.inOut' }, 0.13)
            .to(paths.top, { morphSVG: closeShapes.top, duration: 0.28, ease: 'back.out(2.2)' }, 0.14)
            .to(paths.bottom, { morphSVG: closeShapes.bottom, duration: 0.28, ease: 'back.out(2.2)' }, 0.16)
            .to(group, { rotation: -3.5, scale: 0.95, duration: 0.2, ease: 'power2.inOut' }, 0.28)
            .to(group, { rotation: 1.75, scale: 1.025, duration: 0.12, ease: 'power2.inOut' }, 0.48)
            .to(group, { rotation: -0.75, scale: 0.99, duration: 0.1, ease: 'power2.inOut' }, 0.6)
            .to(group, { rotation: 0, scale: 1, duration: 0.22, ease: springEase }, 0.7);
    } else {
        timeline
            .to(group, { rotation: 14, scale: 0.78, duration: 0.14, ease: 'power3.in' }, 0)
            .to(paths.top, { morphSVG: wobbleShapes.top, duration: 0.2, ease: 'power3.out' }, 0)
            .to(paths.bottom, { morphSVG: wobbleShapes.bottom, duration: 0.2, ease: 'power3.out' }, 0)
            .to(paths.middle, {
                autoAlpha: 1,
                morphSVG: wobbleShapes.middle,
                duration: 0.14,
                ease: 'power2.out',
            }, 0.08)
            .to(group, { rotation: -7, scale: 1.1, duration: 0.16, ease: 'power2.inOut' }, 0.13)
            .to(paths.top, { morphSVG: burgerShapes.top, duration: 0.27, ease: 'back.out(2)' }, 0.15)
            .to(paths.bottom, { morphSVG: burgerShapes.bottom, duration: 0.27, ease: 'back.out(2)' }, 0.15)
            .to(group, { rotation: 3.5, scale: 0.95, duration: 0.2, ease: 'power2.inOut' }, 0.28)
            .to(group, { rotation: -1.75, scale: 1.025, duration: 0.12, ease: 'power2.inOut' }, 0.48)
            .to(group, { rotation: 0.75, scale: 0.99, duration: 0.1, ease: 'power2.inOut' }, 0.6)
            .to(group, { rotation: 0, scale: 1, duration: 0.22, ease: springEase }, 0.7);
    }

    timelines.set(icon, timeline);
    icon.dataset.mobileMenuIconState = nextState;
};

export const mobileMenuIcon = {
    set(icon, isOpen) {
        animateIcon(icon, Boolean(isOpen));
    },
};
const headerActionShapes = {
    search: {
        closed: {
            primary: 'M3 10a7 7 0 1 0 14 0a7 7 0 1 0 -14 0',
            secondary: 'M21 21l-6 -6',
        },
        wobble: {
            primary: 'M4 10 C8 8 16 16 20 14',
            secondary: 'M20 20 C16 16 8 8 4 4',
        },
        open: {
            primary: 'M6 6l12 12',
            secondary: 'M18 6l-12 12',
        },
    },
    notifications: {
        closed: {
            primary: 'M10 5a2 2 0 1 1 4 0a7 7 0 0 1 4 6v3a4 4 0 0 0 2 3h-16a4 4 0 0 0 2 -3v-3a7 7 0 0 1 4 -6',
            secondary: 'M9 17v1a3 3 0 0 0 6 0v-1',
        },
        wobble: {
            primary: 'M4 12 C8 10 16 14 20 12',
            secondary: 'M8 16 C10 18 14 18 16 16',
        },
        open: {
            primary: 'M6 6l12 12',
            secondary: 'M18 6l-12 12',
        },
    },
};

const headerActionTimelines = new WeakMap();

const getHeaderActionIconParts = (icon) => ({
    primary: icon.querySelector('[data-header-action-icon-primary]'),
    secondary: icon.querySelector('[data-header-action-icon-secondary]'),
    group: icon.querySelector('[data-header-action-icon-group]'),
});

const setHeaderActionIconState = (icon, isOpen) => {
    const shapeSet = headerActionShapes[icon.dataset.headerActionIcon];
    const parts = getHeaderActionIconParts(icon);

    if (!shapeSet || Object.values(parts).some((part) => !part)) {
        return;
    }

    resetIconGroup(parts.group);
    gsap.set(parts.primary, { morphSVG: shapeSet[isOpen ? 'open' : 'closed'].primary });
    gsap.set(parts.secondary, { morphSVG: shapeSet[isOpen ? 'open' : 'closed'].secondary });
};

const animateHeaderActionIcon = (icon, isOpen) => {
    if (!icon) {
        return;
    }

    const shapeSet = headerActionShapes[icon.dataset.headerActionIcon];
    const parts = getHeaderActionIconParts(icon);

    if (!shapeSet || Object.values(parts).some((part) => !part)) {
        return;
    }

    const nextState = isOpen ? 'open' : 'closed';
    const activeTimeline = headerActionTimelines.get(icon);

    if (icon.dataset.headerActionIconState === nextState) {
        return;
    }

    activeTimeline?.kill();

    if (prefersReducedMotion()) {
        setHeaderActionIconState(icon, isOpen);
        icon.dataset.headerActionIconState = nextState;

        return;
    }

    const direction = isOpen ? 1 : -1;
    const targetShapes = shapeSet[isOpen ? 'open' : 'closed'];
    const timeline = gsap.timeline({
        onComplete: () => {
            setHeaderActionIconState(icon, isOpen);

            if (headerActionTimelines.get(icon) === timeline) {
                headerActionTimelines.delete(icon);
            }
        },
    });

    timeline
        .to(parts.group, { rotation: direction * 9, scale: 0.82, duration: 0.14, ease: 'power3.in' }, 0)
        .to(parts.primary, { morphSVG: shapeSet.wobble.primary, duration: 0.22, ease: 'power3.out' }, 0)
        .to(parts.secondary, { morphSVG: shapeSet.wobble.secondary, duration: 0.22, ease: 'power3.out' }, 0)
        .to(parts.group, { rotation: direction * -4.5, scale: 1.08, duration: 0.18, ease: 'power2.inOut' }, 0.16)
        .to(parts.primary, { morphSVG: targetShapes.primary, duration: 0.25, ease: 'back.out(1.8)' }, 0.17)
        .to(parts.secondary, { morphSVG: targetShapes.secondary, duration: 0.25, ease: 'back.out(1.8)' }, 0.19)
        .to(parts.group, { rotation: direction * 2.25, scale: 0.96, duration: 0.18, ease: 'power2.inOut' }, 0.36)
        .to(parts.group, { rotation: direction * -1, scale: 1.025, duration: 0.14, ease: 'power2.inOut' }, 0.54)
        .to(parts.group, { rotation: direction * 0.4, scale: 0.99, duration: 0.12, ease: 'power2.inOut' }, 0.68)
        .to(parts.group, { rotation: 0, scale: 1, duration: 0.22, ease: springEase }, 0.8);

    headerActionTimelines.set(icon, timeline);
    icon.dataset.headerActionIconState = nextState;
};

export const headerActionIcon = {
    set(icon, isOpen) {
        animateHeaderActionIcon(icon, Boolean(isOpen));
    },
};
