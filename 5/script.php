<?php

class Pos {
    public function __construct(public int $x, public int $y) {}
}
class Seg {
    public function __construct(public Pos $start, public Pos $end) {}
    public function isStraight(): bool {
        return $this->start->x === $this->end->x || $this->start->y === $this->end->y;
    }
}
class Diagram {
    private array $grid = [];
    public function plot(Seg $seg): void {
        if (!$seg->isStraight()) {
            return;
        }
        foreach (range($seg->start->x, $seg->end->x) as $x) {
            if (!isset($this->grid[$x])) {
                $this->grid[$x] = [];
            }
            foreach (range($seg->start->y, $seg->end->y) as $y) {
                if (isset($this->grid[$x][$y])) {
                    $this->grid[$x][$y]++;
                    continue;
                }
                $this->grid[$x][$y] = 1;
            }
        }
    }

    public static function plotFromSegments(array $segments): Diagram
    {
        return array_reduce($segments, function (Diagram $dia, Seg $seg) {
            $dia->plot($seg);
            return $dia;
        }, new Diagram());
    }

    public function overlapingPoints(): int
    {
        return array_reduce($this->grid, function (int $count, array $yAxis) {
            $count += count(array_filter($yAxis, fn (int $count) => $count > 1));
            return $count;
        }, 0);
    }
}

$diagram = Diagram::plotFromSegments(array_map(function (string $line) {
    return new Seg(...array_map(function (string $pos) {
        return new Pos(...array_map('intval', explode(',', $pos)));
    }, explode(' -> ', $line)));
}, explode("\n", trim(file_get_contents(__DIR__ . '/input')))));

echo $diagram->overlapingPoints();

