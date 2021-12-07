<?php

$positions = array_map('intval', explode(',',trim(file_get_contents(__DIR__ . '/input'))));

$mean = array_sum($positions) / count($positions);
$min = null;
$offset = null;
for ($o = min($positions); $o <= max($positions); $o++) {
    $costs = array_map(fn (int $pos) => abs($pos - $o), $positions);
    $costs = array_map(fn (int $c) => array_sum(range(0, $c)), $costs);
    $total = array_sum($costs);

    if (null === $min || $total < $min) {
        $min = $total;
        $offset = $o;
    }
}
var_dump($min, $offset);
