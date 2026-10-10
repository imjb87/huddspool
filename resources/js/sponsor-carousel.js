const sponsorCarousel = (totalSlides, cloneCount = 3) => ({
    totalSlides,
    cloneCount,
    currentIndex: cloneCount,
    visibleCount: 3,
    autoplayIntervalId: null,
    transitionTimeoutId: null,
    resizeHandler: null,
    motionPreferenceQuery: null,
    motionPreferenceHandler: null,
    isFocused: false,
    isHovered: false,
    isJumping: false,
    isTransitioning: false,
    reducedMotion: false,
    touchStartPoint: null,
    suppressClickUntil: 0,

    start() {
        this.visibleCount = this.getVisibleCount();
        this.motionPreferenceQuery = window.matchMedia('(prefers-reduced-motion: reduce)');
        this.reducedMotion = this.motionPreferenceQuery.matches;
        this.resizeHandler = () => this.handleResize();
        this.motionPreferenceHandler = (event) => this.handleMotionPreferenceChange(event);

        window.addEventListener('resize', this.resizeHandler, { passive: true });
        this.motionPreferenceQuery.addEventListener?.('change', this.motionPreferenceHandler);
        this.resume();
    },

    destroy() {
        this.pause();

        if (this.transitionTimeoutId !== null) {
            window.clearTimeout(this.transitionTimeoutId);
        }

        if (this.resizeHandler !== null) {
            window.removeEventListener('resize', this.resizeHandler);
        }

        this.motionPreferenceQuery?.removeEventListener?.('change', this.motionPreferenceHandler);
    },

    getVisibleCount() {
        if (window.innerWidth >= 1024) {
            return 3;
        }

        if (window.innerWidth >= 640) {
            return 2;
        }

        return 2;
    },

    slideOffset() {
        return this.currentIndex * (100 / this.visibleCount);
    },

    next(steps = 1) {
        this.move(1, steps);
    },

    previous(steps = 1) {
        this.move(-1, steps);
    },

    move(direction, steps = 1) {
        if (this.isTransitioning || this.isJumping) {
            return;
        }

        this.currentIndex += direction * steps;
        this.isTransitioning = true;

        if (this.reducedMotion) {
            this.completeTransition();

            return;
        }

        this.transitionTimeoutId = window.setTimeout(() => this.completeTransition(), 520);
    },

    handleTransitionEnd(event) {
        if (event.target !== this.$refs.track || event.propertyName !== 'transform') {
            return;
        }

        this.completeTransition();
    },

    completeTransition() {
        if (!this.isTransitioning) {
            return;
        }

        this.isTransitioning = false;

        if (this.transitionTimeoutId !== null) {
            window.clearTimeout(this.transitionTimeoutId);
            this.transitionTimeoutId = null;
        }

        if (this.currentIndex >= this.cloneCount + this.totalSlides) {
            this.jumpTo(this.currentIndex - this.totalSlides);
        } else if (this.currentIndex < this.cloneCount) {
            this.jumpTo(this.currentIndex + this.totalSlides);
        }
    },

    jumpTo(index) {
        this.isJumping = true;
        this.currentIndex = index;

        this.$nextTick(() => {
            window.requestAnimationFrame(() => {
                window.requestAnimationFrame(() => {
                    this.isJumping = false;
                });
            });
        });
    },

    handleResize() {
        const nextVisibleCount = this.getVisibleCount();

        if (nextVisibleCount === this.visibleCount) {
            return;
        }

        this.visibleCount = nextVisibleCount;

        if (this.currentIndex >= this.cloneCount + this.totalSlides || this.currentIndex < this.cloneCount) {
            this.currentIndex = this.cloneCount;
        }

        this.jumpTo(this.currentIndex);
    },

    handleMotionPreferenceChange(event) {
        this.reducedMotion = event.matches;

        if (this.reducedMotion) {
            this.pause();
            this.completeTransition();

            return;
        }

        this.resume();
    },

    handleTouchStart(event) {
        if (event.touches.length !== 1) {
            this.touchStartPoint = null;

            return;
        }

        const touch = event.touches[0];
        this.touchStartPoint = { x: touch.clientX, y: touch.clientY };
        this.pause();
    },

    handleTouchEnd(event) {
        const start = this.touchStartPoint;
        const touch = event.changedTouches?.[0];
        this.touchStartPoint = null;

        if (start && touch) {
            const deltaX = touch.clientX - start.x;
            const deltaY = touch.clientY - start.y;

            if (Math.abs(deltaX) >= 40 && Math.abs(deltaX) > Math.abs(deltaY)) {
                this.suppressClickUntil = Date.now() + 500;

                if (deltaX < 0) {
                    this.next(2);
                } else {
                    this.previous(2);
                }
            }
        }

        this.resume();
    },

    handleTouchCancel() {
        this.touchStartPoint = null;
        this.resume();
    },

    handleSwipeClick(event) {
        if (Date.now() > this.suppressClickUntil || !event.target.closest?.('a')) {
            return;
        }

        event.preventDefault();
        event.stopPropagation();
        this.suppressClickUntil = 0;
    },

    handleMouseEnter() {
        this.isHovered = true;
        this.pause();
    },

    handleMouseLeave() {
        this.isHovered = false;
        this.resume();
    },

    handleFocusIn() {
        this.isFocused = true;
        this.pause();
    },

    handleFocusOut(event) {
        if (event.relatedTarget && this.$root.contains(event.relatedTarget)) {
            return;
        }

        this.isFocused = false;
        this.resume();
    },

    handleVisibilityChange() {
        if (document.hidden) {
            this.pause();

            return;
        }

        this.resume();
    },

    pause() {
        if (this.autoplayIntervalId === null) {
            return;
        }

        window.clearInterval(this.autoplayIntervalId);
        this.autoplayIntervalId = null;
    },

    resume() {
        if (this.reducedMotion || this.isHovered || this.isFocused || document.hidden || this.autoplayIntervalId !== null) {
            return;
        }

        this.autoplayIntervalId = window.setInterval(() => this.next(), 5000);
    },
});

window.sponsorCarousel = sponsorCarousel;

export { sponsorCarousel };
