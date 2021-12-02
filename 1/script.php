<?php

echo array_reduce(
    array_map(
        'intval',
        explode(
            "\n",
            file_get_contents(
                __DIR__ . '/input'
            )
        )
    ), 
    static function (int $count, int $number): int {
        static $prev = PHP_INT_MAX;
        $count += $number > $prev ? 1 : 0;
        $prev = $number;
        return $count;
    },
    0
);
