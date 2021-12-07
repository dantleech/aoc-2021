<?php

$positions = array_map('intval', explode(',',trim(file_get_contents(__DIR__ . '/input'))));

[ $min, $pos ] = array_reduce(range(min($positions), max($positions)), function (array $mt, int $o) use ($positions) {
    [$min, $offset] = $mt;

    $fuel = array_sum(array_map(
        fn (int $c) => $c * ($c + 1) / 2,
        array_map(
            fn (int $pos) => abs($pos - $o),
            $positions
        )
    ));

    if (null === $min || $fuel < $min) {
        $min = $fuel;
        $offset = $o;
    }

    return [$min, $offset];
}, [null, null]);

var_dump($min, $pos);
