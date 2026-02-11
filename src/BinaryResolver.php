<?php

namespace Melkmeshi\GoTools;

use Melkmeshi\GoTools\Exceptions\BinaryNotFoundException;

class BinaryResolver
{
    public function resolve(): string
    {
        $customPath = config('go-tools.binary_path');

        if ($customPath && file_exists($customPath)) {
            return $customPath;
        }

        $binaryName = $this->binaryName();
        $packageBin = $this->packageBinPath($binaryName);

        if (file_exists($packageBin)) {
            return $packageBin;
        }

        throw new BinaryNotFoundException(
            "Go tools binary not found. Run: php artisan go-tools:install"
        );
    }

    public function binaryName(): string
    {
        $os = $this->detectOs();
        $arch = $this->detectArch();

        return "go-tools-{$os}-{$arch}" . ($os === 'windows' ? '.exe' : '');
    }

    public function detectOs(): string
    {
        return match (PHP_OS_FAMILY) {
            'Darwin' => 'darwin',
            'Linux' => 'linux',
            'Windows' => 'windows',
            default => strtolower(PHP_OS_FAMILY),
        };
    }

    public function detectArch(): string
    {
        $arch = php_uname('m');

        return match (true) {
            in_array($arch, ['x86_64', 'amd64']) => 'amd64',
            in_array($arch, ['aarch64', 'arm64']) => 'arm64',
            default => $arch,
        };
    }

    public function packageBinPath(string $binaryName = null): string
    {
        $binaryName ??= $this->binaryName();

        return dirname(__DIR__) . '/bin/' . $binaryName;
    }

    public function platform(): string
    {
        return $this->detectOs() . '-' . $this->detectArch();
    }
}
