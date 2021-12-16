<?php

[$grid, $folds]  = (fn (string $cords, string $folds) => [
    Grid::fromPoints(array_map(fn (string $cords) => explode(',', $cords), explode("\n", $cords))),
    array_map(fn (string $fold) => new Fold(...explode('=', substr($fold, 11))),explode("\n", $folds)),
])(...explode("\n\n", trim(file_get_contents(__DIR__ . '/input'))));
assert($grid instanceof Grid);

class Fold { function __construct(public string $axes, public int $pos) {} }
class Point { function __construct(public string $x, public int $y) {} }
class Grid { 
    public function __construct(public array $grid) {
    }

    public static function fromPoints(array $points): Grid {
        $maxX = max(array_map(fn (array $p) => $p[0], $points));
        $maxY = max(array_map(fn (array $p) => $p[1], $points));

        $grid = [];
        for ($x = 0; $x <= $maxX; $x++) {
            for ($y = 0; $y <= $maxY; $y++) {
                $grid[$y][$x] = '.';
            }
        }

        foreach ($points as [$x, $y]) {
            $grid[$y][$x] = '#';
        }

        return new self($grid);
    }

    public function fold(Fold $fold): Grid {
        if ($fold->axes === 'y') {
            return $this->foldY($fold->pos)->merge($this);
        }

        return $this->foldX($fold->pos)->merge($this);
    }

    public function toString(): string {
        return implode("\n", array_map(
            fn (array $xs) => implode("", $xs),
            $this->grid
        ))."\n\n";
    }

    public function dots(): array {
        $dots = [];
        foreach ($this->grid as $row) {
            foreach ($row as $char) {
                $dots[] = $char;
            }
        }

        return $dots;
    }

    private function foldY(int $int): Grid {
        $grid = array_slice($this->grid, $int + 1, null);
        return new Grid(array_reverse($grid));
    }

    private function foldX(int $int)
    {
        $new = [];
        foreach ($this->grid as $y => $row) {
            $new[$y] = array_reverse(array_slice($row, $int + 1, null));
        }
        return new Grid($new);
    }

    private function merge(Grid $grid) {
        $new = $this->grid;
        foreach ($grid->grid as $y => $row) {
            if (!isset($new[$y])) {
                continue;
            }
            foreach ($row as $x => $mark) {
                if (!isset($new[$y][$x])) {
                    continue;
                }
                if ($mark === '.') {
                    continue;
                }

                $new[$y][$x] = $mark;
            }
        }
        return new self($new);
    }
}

foreach ($folds as $fold) {
    $grid = $grid->fold($fold);
    echo count(array_filter($grid->dots(), fn($c) => $c === '#'));
    die();
}

echo $grid->toString();
