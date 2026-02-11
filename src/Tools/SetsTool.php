<?php

namespace Melkmeshi\GoTools\Tools;

use Melkmeshi\GoTools\BinaryRunner;

class SetsTool
{
    public function __construct(
        protected BinaryRunner $runner,
    ) {}

    /**
     * Return elements present in both arrays.
     *
     * @param  array<mixed>  $setA
     * @param  array<mixed>  $setB
     * @return array<mixed>
     */
    public function intersect(array $setA, array $setB): array
    {
        $result = $this->runner->run(['sets', 'intersect'], [
            'set_a' => array_values($setA),
            'set_b' => array_values($setB),
        ]);

        return $result['result'] ?? [];
    }

    /**
     * Return all unique elements from both arrays.
     *
     * @param  array<mixed>  $setA
     * @param  array<mixed>  $setB
     * @return array<mixed>
     */
    public function union(array $setA, array $setB): array
    {
        $result = $this->runner->run(['sets', 'union'], [
            'set_a' => array_values($setA),
            'set_b' => array_values($setB),
        ]);

        return $result['result'] ?? [];
    }

    /**
     * Return elements in setA that are not in setB.
     *
     * @param  array<mixed>  $setA
     * @param  array<mixed>  $setB
     * @return array<mixed>
     */
    public function diff(array $setA, array $setB): array
    {
        $result = $this->runner->run(['sets', 'diff'], [
            'set_a' => array_values($setA),
            'set_b' => array_values($setB),
        ]);

        return $result['result'] ?? [];
    }
}
