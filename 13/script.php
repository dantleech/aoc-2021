<?php

[$grid, $folds]  = (fn (string $cords, string $folds) => [
    new Grid(array_map(fn (string $cords) => new Point(...array_map('intval', explode(',', $cords))), explode("\n", $cords))),
    array_map(fn (string $fold) => new Fold(...explode('=', substr($fold, 11))),explode("\n", $folds)),
])(...explode("\n\n", trim(file_get_contents(__DIR__ . '/testinput'))));
assert($grid instanceof Grid);

class Fold { function __construct(public string $axes, public int $pos) {} }
class Point { function __construct(public int $x, public int $y) {} }
class Grid { 
    public function __construct(public array $points) {
    }

    public static function fromPoints(array $array)
    {
    }

    public function toString(): string
    {
        $maxX = max(array_map(fn (Point $p) => $p->x, $this->points));
        $maxY = max(array_map(fn (Point $p) => $p->y, $this->points));

        $grid = [];
        for ($y = 0; $y <= $maxY; $y++) {
            for ($x = 0; $x <= $maxX; $x++) {
                $grid[$y][$x] = '.';
            }
        }

        foreach ($this->points as $point) {
            $grid[$point->y][$point->x] = '#';
        }

        return implode("\n", array_map(fn (array $chars) => implode('', $chars), $grid));
    }
}

echo $grid->toString();
