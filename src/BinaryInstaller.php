<?php

namespace Melkmeshi\GoTools;

use Illuminate\Support\Facades\Http;
use Melkmeshi\GoTools\Exceptions\BinaryNotFoundException;
use RuntimeException;

class BinaryInstaller
{
    public function __construct(
        protected BinaryResolver $resolver,
    ) {}

    /**
     * Download the correct binary for the current platform.
     */
    public function install(?string $version = null): string
    {
        $version = $version ?? config('go-tools.version', 'latest');
        $repo = config('go-tools.github_repo', 'melkmeshi/laravel-go-tools');
        $platform = $this->resolver->platform();

        $releaseUrl = $this->resolveReleaseUrl($repo, $version);
        $assetUrl = $this->findAssetUrl($releaseUrl, $platform);

        $binaryPath = $this->resolver->packageBinPath();
        $this->downloadAndExtract($assetUrl, $binaryPath);

        return $binaryPath;
    }

    /**
     * Check if the binary is already installed.
     */
    public function isInstalled(): bool
    {
        try {
            $this->resolver->resolve();

            return true;
        } catch (BinaryNotFoundException) {
            return false;
        }
    }

    /**
     * Get the installed binary version, or null if not installed.
     */
    public function installedVersion(): ?string
    {
        if (! $this->isInstalled()) {
            return null;
        }

        try {
            $runner = new BinaryRunner($this->resolver);
            $result = $runner->run(['version']);

            return $result['version'] ?? null;
        } catch (\Throwable) {
            return null;
        }
    }

    protected function resolveReleaseUrl(string $repo, string $version): string
    {
        if ($version === 'latest') {
            return "https://api.github.com/repos/{$repo}/releases/latest";
        }

        return "https://api.github.com/repos/{$repo}/releases/tags/{$version}";
    }

    protected function findAssetUrl(string $releaseUrl, string $platform): string
    {
        $response = Http::withHeaders([
            'Accept' => 'application/vnd.github.v3+json',
        ])->get($releaseUrl);

        if (! $response->successful()) {
            throw new RuntimeException(
                "Failed to fetch release info from GitHub: {$response->status()}"
            );
        }

        $release = $response->json();
        $assets = $release['assets'] ?? [];
        $searchName = "go-tools-{$platform}.tar.gz";

        foreach ($assets as $asset) {
            if (str_contains($asset['name'], $searchName)) {
                return $asset['browser_download_url'];
            }
        }

        throw new RuntimeException(
            "No binary found for platform '{$platform}' in release. Available assets: "
            . implode(', ', array_column($assets, 'name'))
        );
    }

    protected function downloadAndExtract(string $url, string $binaryPath): void
    {
        $tempFile = tempnam(sys_get_temp_dir(), 'go-tools-') . '.tar.gz';

        $response = Http::withOptions(['sink' => $tempFile])->get($url);

        if (! $response->successful()) {
            @unlink($tempFile);
            throw new RuntimeException("Failed to download binary from: {$url}");
        }

        $binDir = dirname($binaryPath);
        if (! is_dir($binDir)) {
            mkdir($binDir, 0755, true);
        }

        // Extract tar.gz
        $phar = new \PharData($tempFile);
        $phar->extractTo($binDir, null, true);

        // Find and rename the binary
        $extractedBinary = $binDir . '/go-tools';
        if (file_exists($extractedBinary) && $extractedBinary !== $binaryPath) {
            rename($extractedBinary, $binaryPath);
        }

        chmod($binaryPath, 0755);

        @unlink($tempFile);
    }
}
