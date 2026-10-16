// The power gesture owns its pointer. Cancellation must never become a shot.
export function bindPowerMeter({ element, signal, isEnabled, onPower, onCommit }) {
    let gesture = null;
    const on = (type, fn) => element.addEventListener(type, fn, { signal });
    const cancel = () => {
        const previous = gesture; gesture = null;
        if (previous && element.hasPointerCapture?.(previous.id)) element.releasePointerCapture(previous.id);
        onPower(0);
    };
    on('pointerdown', event => {
        if (!isEnabled() || gesture || event.button !== 0) return;
        gesture = { id: event.pointerId, y: event.clientY, moved: false };
        element.setPointerCapture(event.pointerId); onPower(0);
    });
    on('pointermove', event => {
        if (!gesture || gesture.id !== event.pointerId) return;
        if (!isEnabled()) { cancel(); return; }
        gesture.moved ||= Math.abs(event.clientY - gesture.y) > 4;
        onPower(Math.max(0, Math.min(1, (event.clientY - gesture.y) / element.getBoundingClientRect().height)));
    });
    on('pointerup', event => {
        if (!gesture || gesture.id !== event.pointerId) return;
        const shouldCommit = gesture.moved && isEnabled(); gesture = null;
        if (element.hasPointerCapture?.(event.pointerId)) element.releasePointerCapture(event.pointerId);
        if (shouldCommit) onCommit(); else onPower(0);
    });
    on('pointercancel', event => { if (gesture?.id === event.pointerId) cancel(); });
    on('lostpointercapture', event => { if (gesture?.id === event.pointerId) cancel(); });
    return { cancel, get active() { return gesture !== null; } };
}
