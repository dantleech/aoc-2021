<?php

class Pos {
    public function __construct(public int $x, public int $y) {}
}
class Seg {
    public function __construct(public Pos $start, public Pos $end) {}
    public function isStraight(): bool {
        return $this->start->x === $this->end->x || $this->start->y === $this->end->y;
    }
    public function isDiagonal(): bool {
        return abs($this->start->x - $this->end->x) === abs($this->start->y - $this->end->y);

    }
}
class Diagram {
    private array $grid = [];
    public function plot(Seg $seg): void {
        $xRange = range($seg->start->x, $seg->end->x);
        $yRange = range($seg->start->y, $seg->end->y);
        if ($seg->isStraight()) {
            foreach ($xRange as $x) {
                foreach ($yRange as $y) {
                    $this->inc($x, $y);
                }
            }
        }
        if ($seg->isDiagonal()) {
            foreach (range($seg->start->x, $seg->end->x) as $i => $x) {
                $y = $yRange[$i];
                $this->inc($x, $y);
            }
        }
    }
    public static function plotFromSegments(array $segments): Diagram {
        return array_reduce($segments, function (Diagram $dia, Seg $seg) {
            $dia->plot($seg);
            return $dia;
        }, new Diagram());
    }
    public function overlapingPointCount(): int {
        return array_reduce($this->grid, function (int $count, array $yAxis) {
            $count += count(array_filter($yAxis, fn (int $count) => $count > 1));
            return $count;
        }, 0);
    }
    private function inc(int $x, int $y): void {
        if (!isset($this->grid[$x])) {
            $this->grid[$x] = [];
        }
        if (isset($this->grid[$x][$y])) {
            $this->grid[$x][$y]++;
            return;
        }
        $this->grid[$x][$y] = 1;
    }
}

$diagram = Diagram::plotFromSegments(array_map(function (string $line) {
    return new Seg(...array_map(function (string $pos) {
        return new Pos(...array_map('intval', explode(',', $pos)));
    }, explode(' -> ', $line)));
}, explode("\n", trim(file_get_contents(__DIR__ . '/input')))));

echo $diagram->overlapingPointCount();
