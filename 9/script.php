<?php

$grid = new Grid(array_map(
    fn ($line) => str_split($line),
    explode("\n", trim(file_get_contents(__DIR__ . '/input')))
));

class Points implements IteratorAggregate {
    public function __construct(public array $points) {
    }

    public function riskLevelSum(): int
    {
        return array_sum(array_map(fn (Point $p) => $p->depth + 1, $this->points));
    }

    public function merge(Points $points): Points
    {
        $map = [];
        foreach ($this as $point) {
            $map[$point->hash()] = $point;
        }
        foreach ($points as $point) {
            $map[$point->hash()] = $point;
        }

        return new Points(array_values($map));
    }

    /**
     * {@inheritDoc}
     */
    public function getIterator()
    {
        return new ArrayIterator($this->points);
    }

    public function count(): int
    {
        return count($this->points);
    }
}

class Grid {
    public function __construct(public array $grid) {
    }

    public function points(): array
    {
        $points = [];
        foreach ($this->grid as $y => $row) {
            foreach ($row as $x => $depth) {
                $points[] =  new Point($x, $y, $depth);
            }
        }

        return $points;
    }

    public function getLowPoints(): Points
    {
        return new Points(array_values(array_filter($this->points(), function (Point $p) {
            return $p->isLowest($this);
        })));
    }

    public function below(Point $point): Point
    {
        return $this->pointAt($point->x, $point->y + 1);
    }

    public function leftOf(Point $point): Point
    {
        return $this->pointAt($point->x - 1, $point->y);
    }

    public function rightOf(Point $point): Point
    {
        return $this->pointAt($point->x + 1, $point->y);
    }

    public function above(Point $point): Point
    {
        return $this->pointAt($point->x, $point->y - 1);
    }

    private function pointAt(int $x, int $y): Point
    {
        if (isset($this->grid[$y][$x])) {
            return new Point($x, $y, $this->grid[$y][$x]);
        }

        return new Point($x, $y, PHP_INT_MAX);
    }

    public function getBasin(Point $op, array &$seen = []): Points
    {
        if (isset($seen[$op->hash()])) {
            return new Points([]);
        }

        $seen[$op->hash()] = true;

        foreach ([[ 1, 0 ], [ -1, 0 ], [ 0, 1], [ 0, -1]] as [ $xo, $yo ]) {
            $point = $op;
            do {
                $points[] = $point;
                $point = $this->pointAt($point->x + $xo, $point->y + $yo);
            } while ($point->depth < 9);
        }

        $points = new Points($points);
        foreach ($points as $point) {
            $points = $points->merge($this->getBasin($point, $seen));
        }

        return $points;
    }
}

class Point {
    public function __construct(public int $x, public int $y, public int $depth){
    }

    public function isLowest(Grid $grid)
    {
        return
            $this->lowerThan($grid->above($this)) &&
            $this->lowerThan($grid->below($this)) &&
            $this->lowerThan($grid->leftOf($this)) &&
            $this->lowerThan($grid->rightOf($this));
    }

    public function lowerThan(Point $point)
    {
        return $this->depth < $point->depth;
    }

    public function hash(): string
    {
        return sprintf('%s,%s', $this->x, $this->y);
    }
}

/**
 * 2199943210
 * 3987894921
 * 9856789892
 * 8767896789
 * 9899965678
 */
$sizes = [];
foreach ($grid->getLowPoints()->points as $i => $point) {
    $sizes[] = $grid->getBasin($point)->count();
}
sort($sizes);

echo array_product(array_slice($sizes, -3));

