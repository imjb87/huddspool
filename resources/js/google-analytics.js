let googleAnalyticsBooted = false;

function googleAnalyticsMeasurementId() {
    return document.body?.dataset.googleAnalyticsMeasurementId?.trim() ?? '';
}

function bootGoogleAnalytics() {
    if (googleAnalyticsBooted) {
        return;
    }

    const measurementId = googleAnalyticsMeasurementId();

    if (!measurementId) {
        return;
    }

    googleAnalyticsBooted = true;

    window.dataLayer = window.dataLayer || [];
    window.gtag = window.gtag || function () {
        window.dataLayer.push(arguments);
    };

    window.gtag('js', new Date());
    window.gtag('config', measurementId);

    const script = document.createElement('script');
    script.async = true;
    script.src = `https://www.googletagmanager.com/gtag/js?id=${encodeURIComponent(measurementId)}`;
    document.head.appendChild(script);
}

export function bootDeferredGoogleAnalytics() {
    if (!googleAnalyticsMeasurementId()) {
        return;
    }

    let hasInteracted = false;
    const passiveListenerOptions = { passive: true, once: true };

    const handleFirstInteraction = () => {
        if (hasInteracted) {
            return;
        }

        hasInteracted = true;
        bootGoogleAnalytics();

        ['pointerdown', 'keydown', 'touchstart'].forEach((eventName) => {
            window.removeEventListener(eventName, handleFirstInteraction, passiveListenerOptions);
        });
    };

    ['pointerdown', 'keydown', 'touchstart'].forEach((eventName) => {
        window.addEventListener(eventName, handleFirstInteraction, passiveListenerOptions);
    });
}
