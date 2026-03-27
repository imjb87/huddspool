<?php

namespace App\Support;

use App\Models\Result;
use Illuminate\Contracts\Filesystem\Filesystem;
use Illuminate\Support\Facades\Storage;
use RuntimeException;
use Symfony\Component\Process\Process;

class ResultShareImageGenerator
{
    /**
     * @var array<int, string>
     */
    private array $browserCandidates = [
        '/usr/bin/chromium',
        '/usr/bin/google-chrome',
        '/usr/bin/chromium-browser',
    ];

    public function version(Result $result): string
    {
        return (string) ($result->updated_at?->timestamp ?? $result->created_at?->timestamp ?? $result->getKey());
    }

    public function isAvailable(): bool
    {
        return $this->browserBinary() !== null;
    }

    public function url(Result $result): string
    {
        return route('result.share-image.versioned', [
            'result' => $result,
            'version' => $this->version($result),
        ]);
    }

    public function cachePath(Result $result, ?string $version = null): string
    {
        $version ??= $this->version($result);

        return sprintf('share-images/results/%d/share-image-%s.png', $result->getKey(), $version);
    }

    public function ensureGenerated(Result $result, ?string $requestedVersion = null): string
    {
        $disk = Storage::disk('local');

        if (filled($requestedVersion)) {
            $requestedPath = $this->cachePath($result, $requestedVersion);

            if ($this->hasCachedImage($requestedPath, $disk)) {
                return $requestedPath;
            }
        }

        $currentPath = $this->cachePath($result);

        if (! $this->hasCachedImage($currentPath, $disk)) {
            $disk->put($currentPath, $this->render($result));
        }

        return $currentPath;
    }

    public function render(Result $result): string
    {
        $browserBinary = $this->browserBinary();

        if ($browserBinary === null) {
            throw new RuntimeException('A headless Chromium or Chrome binary is required to render result share images.');
        }

        $html = view('result.share-image', [
            'result' => $result,
            'fixture' => $result->fixture,
            'section' => $result->fixture?->section,
            'ruleset' => $result->fixture?->section?->ruleset,
            'venue' => $result->fixture?->venue,
            'logoDataUri' => $this->logoDataUri(),
        ])->render();

        $tempDirectory = sys_get_temp_dir();
        $htmlPath = sprintf('%s/result-share-%s.html', $tempDirectory, uniqid('', true));
        $pngPath = sprintf('%s/result-share-%s.png', $tempDirectory, uniqid('', true));

        if (file_put_contents($htmlPath, $html) === false) {
            throw new RuntimeException('Unable to create a temporary HTML file for result share image rendering.');
        }

        $process = new Process([
            $browserBinary,
            '--headless',
            '--disable-gpu',
            '--hide-scrollbars',
            '--no-sandbox',
            '--window-size=1200,630',
            '--force-device-scale-factor=1',
            '--screenshot='.$pngPath,
            'file://'.$htmlPath,
        ]);

        $process->setTimeout(30);
        $process->run();

        if (! $process->isSuccessful()) {
            @unlink($htmlPath);
            @unlink($pngPath);

            throw new RuntimeException(trim($process->getErrorOutput()) ?: 'Unable to render result share image screenshot.');
        }

        $blob = file_get_contents($pngPath);

        @unlink($htmlPath);
        @unlink($pngPath);

        if ($blob === false) {
            throw new RuntimeException('Unable to read the generated result share image.');
        }

        return $blob;
    }

    private function logoDataUri(): ?string
    {
        $path = public_path('images/logo.png');

        if (! is_file($path)) {
            return null;
        }

        $contents = file_get_contents($path);

        if ($contents === false) {
            return null;
        }

        return 'data:image/png;base64,'.base64_encode($contents);
    }

    private function browserBinary(): ?string
    {
        foreach ($this->browserCandidates as $candidate) {
            if (is_executable($candidate)) {
                return $candidate;
            }
        }

        return null;
    }

    private function hasCachedImage(string $path, Filesystem $disk): bool
    {
        return $disk->exists($path) && (int) $disk->size($path) > 0;
    }
}
