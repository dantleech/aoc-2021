<?php

echo array_reduce(
    array_map(
        fn (string $in) => explode(' ', $in),
        explode("\n", trim(file_get_contents(__DIR__ . '/input')))
    ),
    function (object $position, array $cmd): object {
        match ($cmd[0]) {
            'forward' => $position->position+=$cmd[1],
            'down' => $position->depth+=$cmd[1],
            'up' => $position->depth-=$cmd[1],
        };
        return $position;
    },
    new class() {
        public int $depth = 0;
        public int $position = 0;
        public function multiply(): int {
            return $this->depth * $this->position;
        }
    },
)->multiply();

