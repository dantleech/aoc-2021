<?php

echo array_reduce(
    array_map(
        fn (string $in) => explode(' ', $in),
        explode("\n", trim(file_get_contents(__DIR__ . '/input')))
    ),
    function (object $nav, array $cmd): object {
        switch ($cmd[0]) {
            case 'forward':
                $nav->position+=$cmd[1];
                $nav->depth += ($nav->aim * $cmd[1]);
                break;
            case 'down':
                $nav->aim+=$cmd[1];
                break;
            case 'up':
                $nav->aim-=$cmd[1];
        };
        return $nav;
    },
    new class() {
        public int $depth = 0;
        public int $position = 0;
        public int $aim = 0;
        public function multiply(): int {
            return $this->depth * $this->position;
        }
    },
)->multiply();

