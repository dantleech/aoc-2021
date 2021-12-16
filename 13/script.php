<?php

[$grid, $folds]  = (fn (string $cords, string $folds) => [
    new Grid(array_map(fn (string $cords) => new Point(...array_map('intval', explode(',', $cords))), explode("\n", $cords))),
    array_map(fn (string $fold) => new Fold(...explode('=', substr($fold, 11))),explode("\n", $folds)),
])(...explode("\n\n", trim(file_get_contents(__DIR__ . '/input'))));
assert($grid instanceof Grid);

class Fold { function __construct(public string $axes, public int $pos) {} }
class Point {
    function __construct(public int $x, public int $y) {}
    function hash() { return $this->x . '.' . $this->y; }
}
class Grid { 
    public function __construct(public array $points) {
    }

    public function fold(Fold $fold): Grid
    {
        if ($fold->axes === 'y') {
            return $this->foldY($fold->pos);
        }

        return $this->foldX($fold->pos);
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
            $grid[$point->y][$point->x] = '█';
        }

        return implode("\n", array_map(fn (array $chars) => implode('', $chars), $grid)) ."\n\n";
    }

    private function foldY(int $pos, int $xo, int $yo): Grid
    {
        $side = array_filter($this->points, fn (Point $point) => $point->y >= $pos);
        $side = array_map(fn (Point $point) => new Point($point->x, $pos + (($point->y - $pos) * - 1)), $side);
        $side = array_filter($side, fn (Point $point) => $point->y >= 0 && $point->y <= $pos);

        return (new Grid($side))->merge($this)->truncateY($pos);
    }

    private function foldX(int $pos): Grid
    {
        $side = array_filter($this->points, fn (Point $point) => $point->x >= $pos);
        $side = array_map(fn (Point $point) => new Point($pos + (($point->x - $pos) * - 1), $point->y), $side);
        $side = array_filter($side, fn (Point $point) => $point->x >= 0 && $point->x <= $pos);

        return (new Grid($side))->merge($this)->truncateX($pos);
    }

    private function merge(Grid $g): Grid
    {
        $points = [];
        foreach ($this->points as $p1) {
            $points[$p1->hash()] = $p1;
        }
        foreach ($g->points as $p2) {
            $points[$p2->hash()] = $p2;
        }

        return new self($points);
    }

    private function truncateY(int $int): Grid
    {
        return new Grid(array_filter($this->points, fn (Point $p) => $p->y <= $int));
    }

    private function truncateX(int $int): Grid
    {
        return new Grid(array_filter($this->points, fn (Point $p) => $p->x <= $int));
    }
}

foreach ($folds as $fold) {
    $grid = $grid->fold($fold);
}

echo $grid->toString();
