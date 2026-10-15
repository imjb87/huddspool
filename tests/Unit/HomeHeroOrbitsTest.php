<?php

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;
use Symfony\Component\Process\Process;

class HomeHeroOrbitsTest extends TestCase
{
    public function test_randomized_angles_preserve_spacing_including_across_zero_degrees(): void
    {
        $this->assertScenario(<<<'JS'
            for (const count of [3, 4, 6, 8]) {
                for (let seed = 1; seed <= 100; seed++) {
                    let state = seed;
                    const random = () => {
                        state = (state * 16807) % 2147483647;
                        return state / 2147483647;
                    };
                    const angles = Array.from(component.anglesForOrbit(count, random)).sort((a, b) => a - b);
                    assert.equal(angles.length, count);
                    angles.forEach((angle, index) => {
                        assert.ok(angle >= 0 && angle < 360);
                        const next = angles[(index + 1) % count] + (index === count - 1 ? 360 : 0);
                        assert.ok(next - angle >= (360 / count) * 0.6 - 0.000001);
                    });
                }
            }
            assert.equal(component.anglesForOrbit(0).length, 0);
            JS);
    }

    public function test_placements_change_with_random_input_and_are_not_cardinal_slots(): void
    {
        $this->assertScenario(<<<'JS'
            const first = Array.from(component.anglesForOrbit(4, () => 0.37));
            const second = Array.from(component.anglesForOrbit(4, () => 0.71));
            assert.notDeepEqual(first, second);
            assert.ok(first.every(angle => angle % 90 !== 0));
            let call = 0;
            const varied = Array.from(component.anglesForOrbit(4, () => [0.37, 0.1, 0.8, 0.3, 0.6][call++]));
            const gaps = varied.slice(1).map((angle, index) => (angle - varied[index] + 360) % 360);
            assert.ok(new Set(gaps.map(gap => gap.toFixed(3))).size > 1);
            JS);
    }

    public function test_initialization_sets_independent_orbit_angles_without_touching_ball_sizes(): void
    {
        $this->assertScenario(<<<'JS'
            const layouts = [[13, 91, 207], [32, 128, 196, 301]];
            const groups = layouts.map(angles => angles.map(() => ({
                properties: {'--ball-size': '32px'},
                style: {setProperty(name, value) { this.owner.properties[name] = value; }},
            })));
            groups.flat().forEach(arm => { arm.style.owner = arm; });
            let index = 0;
            component.anglesForOrbit = count => {
                const angles = layouts[index++];
                assert.equal(count, angles.length);
                return angles;
            };
            component.$el = {querySelectorAll: () => groups.map(arms => ({querySelectorAll: () => arms}))};
            component.init();
            groups.forEach((arms, orbitIndex) => arms.forEach((arm, ballIndex) => {
                assert.equal(arm.properties['--ball-angle'], `${layouts[orbitIndex][ballIndex]}deg`);
                assert.equal(arm.properties['--ball-size'], '32px');
            }));
            JS);
    }

    private function assertScenario(string $scenario): void
    {
        $harness = <<<'JS'
            const assert = require('node:assert/strict');
            const fs = require('node:fs');
            const vm = require('node:vm');
            const window = {};
            vm.runInNewContext(fs.readFileSync('resources/js/home-hero-orbits.js', 'utf8'), {window});
            const component = window.homeHeroOrbits();
            JS;

        $process = new Process(['node', '-e', $harness."\n".$scenario], dirname(__DIR__, 2));
        $process->run();

        $this->assertSame(0, $process->getExitCode(), $process->getErrorOutput().$process->getOutput());
    }
}
