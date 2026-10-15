<div class="home-hero-orbits pointer-events-none absolute inset-0 -z-10" aria-hidden="true" data-home-hero-orbits>
    <div class="home-hero-glow absolute inset-0"></div>

    <div class="home-hero-orbit-field absolute inset-0" x-data="homeHeroOrbits()" data-home-hero-orbit-field>
        <svg class="absolute size-0" xmlns="http://www.w3.org/2000/svg">
            <defs>
                <radialGradient id="home-hero-red-gradient" cx="30%" cy="22%" r="75%">
                    <stop offset="0%" stop-color="#ef4444" />
                    <stop offset="48%" stop-color="#dc2626" />
                    <stop offset="100%" stop-color="#991b1b" />
                </radialGradient>
                <radialGradient id="home-hero-yellow-gradient" cx="30%" cy="22%" r="75%">
                    <stop offset="0%" stop-color="#facc15" />
                    <stop offset="48%" stop-color="#eab308" />
                    <stop offset="100%" stop-color="#ca8a04" />
                </radialGradient>
                <radialGradient id="home-hero-eight-gradient" cx="30%" cy="22%" r="75%">
                    <stop offset="0%" class="application-logo-eight-ball-gradient-start" />
                    <stop offset="48%" class="application-logo-eight-ball-gradient-middle" />
                    <stop offset="100%" class="application-logo-eight-ball-gradient-end" />
                </radialGradient>
                <radialGradient id="home-hero-ball-shine" cx="74%" cy="78%" r="42%">
                    <stop offset="0%" stop-color="#f8fafc" stop-opacity="0.16" />
                    <stop offset="45%" stop-color="#cbd5e1" stop-opacity="0.06" />
                    <stop offset="100%" stop-color="#cbd5e1" stop-opacity="0" />
                </radialGradient>
                <symbol id="home-hero-solid-ball" viewBox="64 64 512 512">
                    <path d="M64 320C64 178.6 178.6 64 320 64C461.4 64 576 178.6 576 320C576 461.4 461.4 576 320 576C178.6 576 64 461.4 64 320z" />
                    <path d="M64 320C64 178.6 178.6 64 320 64C461.4 64 576 178.6 576 320C576 461.4 461.4 576 320 576C178.6 576 64 461.4 64 320z" fill="url(#home-hero-ball-shine)" />
                </symbol>
                <symbol id="home-hero-eight-ball" viewBox="64 64 512 512">
                    <circle cx="320" cy="248" r="136" fill="#f8fafc" data-home-hero-eight-face />
                    <path d="M320 576C461.4 576 576 461.4 576 320C576 178.6 461.4 64 320 64C178.6 64 64 178.6 64 320C64 461.4 178.6 576 320 576zM320 112C395.1 112 456 172.9 456 248C456 323.1 395.1 384 320 384C244.9 384 184 323.1 184 248C184 172.9 244.9 112 320 112zM320 184C328.8 184 336 191.2 336 200C336 208.8 328.8 216 320 216C311.2 216 304 208.8 304 200C304 191.2 311.2 184 320 184zM363.8 234.9C371.5 225.3 376 213.2 376 200C376 169.1 350.9 144 320 144C289.1 144 264 169.1 264 200C264 213.2 268.6 225.4 276.2 234.9C266.1 245.6 260 260.1 260 276C260 309.1 286.9 336 320 336C353.1 336 380 309.1 380 276C380 260.1 373.8 245.7 363.8 234.9zM320 296C309 296 300 287 300 276C300 265 309 256 320 256C331 256 340 265 340 276C340 287 331 296 320 296z" />
                    <circle cx="320" cy="320" r="256" fill="url(#home-hero-ball-shine)" />
                </symbol>
            </defs>
        </svg>

        @foreach ([
            ['duration' => 18, 'delay' => -5, 'balls' => [['red', 32, 24], ['yellow', 151, 26], ['eight', 278, 28]]],
            ['duration' => 20, 'delay' => -7, 'balls' => [['yellow', 15, 28], ['eight', 105, 32], ['red', 195, 30], ['yellow', 285, 26]]],
            ['duration' => 24, 'delay' => -12, 'balls' => [['red', 24, 36], ['yellow', 84, 30], ['eight', 144, 40], ['yellow', 204, 34], ['red', 264, 32], ['eight', 324, 38]]],
            ['duration' => 30, 'delay' => -19, 'balls' => [['yellow', 13, 44], ['eight', 58, 46], ['red', 103, 38], ['yellow', 148, 40], ['red', 193, 42], ['eight', 238, 40], ['yellow', 283, 36], ['red', 328, 38]]],
            ['duration' => 40, 'delay' => -8, 'balls' => [['red', 11, 52], ['yellow', 56, 48], ['eight', 101, 44], ['red', 146, 46], ['yellow', 191, 50], ['red', 236, 48], ['eight', 281, 46], ['yellow', 326, 44]]],
        ] as $orbit)
            <div class="home-hero-orbit home-hero-orbit-{{ $loop->iteration }} absolute left-1/2 top-1/2" style="--orbit-index: {{ $loop->index }}; --orbit-duration: {{ $orbit['duration'] }}s; --orbit-delay: {{ $orbit['delay'] }}s; --orbit-start: {{ $orbit['delay'] / $orbit['duration'] * 360 }}deg;" data-home-hero-orbit>
                <div class="home-hero-orbit-track absolute inset-0 rounded-full border">
                    @foreach ($orbit['balls'] as [$color, $angle, $size])
                        <div class="home-hero-orbit-arm absolute inset-0" style="--ball-angle: {{ $angle }}deg; --ball-size: {{ $size }}px;">
                            <div class="home-hero-orbit-ball absolute left-1/2 top-0" data-home-hero-ball="{{ $color }}">
                                <svg class="block size-full" width="{{ $size }}" height="{{ $size }}" viewBox="0 0 512 512" fill="url(#home-hero-{{ $color }}-gradient)" focusable="false">
                                    <use href="#home-hero-{{ $color === 'eight' ? 'eight' : 'solid' }}-ball" width="512" height="512" />
                                </svg>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @endforeach
    </div>
</div>
