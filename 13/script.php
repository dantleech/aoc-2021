<?php

class Point { function __construct(public string $x, public int $y) {} }
class Fold { function __construct(public string $axes, public int $y) {} }

[$coords, $folds]  = (fn (string $cords, string $folds) => [
    array_map(fn (string $cords) => new Point(...explode(',', $cords)), explode("\n", $cords)),
    array_map(fn (string $fold) => new Fold(...explode('=', substr($fold, 11))),explode("\n", $folds)),
])(...explode("\n\n", trim(file_get_contents(__DIR__ . '/testinput'))));

var_dump($coords, $folds);
