window.homeHeroOrbits = () => ({
    init() {
        this.$el.querySelectorAll('[data-home-hero-orbit]').forEach((orbit) => {
            const arms = orbit.querySelectorAll('.home-hero-orbit-arm');
            const angles = this.anglesForOrbit(arms.length);

            arms.forEach((arm, index) => {
                arm.style.setProperty('--ball-angle', `${angles[index]}deg`);
            });
        });
    },
    anglesForOrbit(count, random = Math.random) {
        if (count <= 0) {
            return [];
        }

        const phase = random() * 360;
        const spacing = 360 / count;

        return Array.from({ length: count }, (_, index) => {
            const jitter = (random() - 0.5) * spacing * 0.4;

            return (phase + index * spacing + jitter + 360) % 360;
        });
    },
});
