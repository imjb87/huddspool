const sponsorCarousel = (totalSlides, cloneCount = 3) => ({
    totalSlides,
    cloneCount,
    currentIndex: cloneCount,
    visibleCount: 3,
    autoplayIntervalId: null,
    transitionTimeoutId: null,
    resizeHandler: null,
    isFocused: false,
    isHovered: false,
    isJumping: false,
    isTransitioning: false,
    reducedMotion: false,

    start() {
        this.visibleCount = this.getVisibleCount();
        this.reducedMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
        this.resizeHandler = () => this.handleResize();

        window.addEventListener('resize', this.resizeHandler, { passive: true });
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

    next() {
        this.move(1);
    },

    previous() {
        this.move(-1);
    },

    move(direction) {
        if (this.isTransitioning || this.isJumping) {
            return;
        }

        this.currentIndex += direction;
        this.isTransitioning = true;

        if (this.reducedMotion) {
            this.completeTransition();

            return;
        }

        this.transitionTimeoutId = window.setTimeout(() => this.completeTransition(), 650);
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
            this.jumpTo(this.cloneCount);
        } else if (this.currentIndex < this.cloneCount) {
            this.jumpTo(this.cloneCount + this.totalSlides - 1);
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
