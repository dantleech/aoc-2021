<?php

$grid = array_map(
    fn ($line) => str_split($line),
    explode("\n", trim(file_get_contents(__DIR__ . '/testinput')))
);

class Point {
    public function __construct(public $x, public $y){}

    public function value(array $grid)
    {
        return $grid[$this->y][$this->x];
    }

    public function hash()
    {
        return sprintf('%s.%s', $this->x, $this->y);
    }

    public function isLowest(array $grid)
    {
        return
            $this->up()->lowerThan($grid, $this) &&
            $this->down()->lowerThan($grid, $this) &&
            $this->left()->lowerThan($grid, $this) &&
            $this->right()->lowerThan($grid, $this);
    }

    public function lowerThan(array $grid, Point $point)
    {
        return $this->value($grid) < $point->value($grid);
    }

    public function up(): self
    {
        return new self($this->x, $this->y - 1);
    }

    public function down(): self
    {
        return new self($this->x, $this->y + 1);
    }

    public function left(): self
    {
        return new self($this->x - 1, $this->y);
    }

    public function right(): self
    {
        return new self($this->x + 1, $this->y);
    }

    public function existsIn(array $grid)
    {
        return isset($grid[$this->y][$this->x]);
    }
}

/**
 * 2199943210
 * 3987894921
 * 9856789892
 * 8767896789
 * 9899965678
 */
function getLowPoints(
    array $grid,
    Point $point
) {
    if (!$point->existsIn($grid)) {
        return null;
    }
    if ($point->isLowest($grid)) {
        $lowPoints[] = $point;
    }

    getLowPoints($grid, $point->left());
    getLowPoints($grid, $point->down());

    return $lowPoints;
}

var_dump(getLowPoints($grid, new Point(0, 0)));

