<?php

namespace Melkmeshi\GoTools;

use Melkmeshi\GoTools\Exceptions\BinaryExecutionException;
use Symfony\Component\Process\Process;

class BinaryRunner
{
    public function __construct(
        protected BinaryResolver $resolver,
    ) {}

    /**
     * Run a Go binary command with JSON input/output.
     *
     * @param  array<string>  $command  Command and subcommands (e.g. ['h3', 'index'])
     * @param  array<string, mixed>  $input  Data to send as JSON on stdin
     * @return array<string, mixed>
     *
     * @throws BinaryExecutionException
     */
    public function run(array $command, array $input = [], ?int $timeout = null): array
    {
        $binary = $this->resolver->resolve();
        $timeout ??= config('go-tools.timeout', 30);

        $process = new Process(
            array_merge([$binary], $command),
            null,
            null,
            json_encode($input),
            $timeout,
        );

        $process->run();

        if (! $process->isSuccessful()) {
            throw new BinaryExecutionException(
                'Go binary command failed: ' . trim($process->getErrorOutput()),
                $process->getExitCode(),
            );
        }

        $output = trim($process->getOutput());

        if (empty($output)) {
            return [];
        }

        $decoded = json_decode($output, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new BinaryExecutionException(
                'Invalid JSON response from Go binary: ' . json_last_error_msg(),
            );
        }

        return $decoded;
    }
}
