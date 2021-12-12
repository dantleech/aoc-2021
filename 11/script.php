<?php

$grid = new Grid(array_map(
    fn (string $line) => str_split($line),
    explode("\n", trim(
        file_get_contents(__DIR__ . '/input')
    ))
));

class Grid {
    public int $nbFlashes = 0;

    public function __construct(public array $grid) {
    }

    /**
     * - Increase all cells by 1
     * - 10: For each cell now > 9
     *   - has it flashed? 
     *     - increase all surrounding squares by 1
     *   - set cell to 0
     *   - add cell to flashed list
     * - are there any squares > 9?
     *   - goto 10
     */
    public function increment(): self {
        $grid = $this->map(fn (int $level) => $level + 1);
        $grid->flash();

        return $grid;
    }

    public function allFlashed(): bool {
        foreach ($this->grid as $y => $octopi) {
            foreach ($octopi as $x => $energy) {
                if ($energy !== 0) {
                    return false;
                }
            }
        }

        return true;
    }

    public function size(): int {
        foreach ($this->grid as $chars) {
            return count($this->grid) * count($chars);
        }

        return 0;
    }

    private function flash(array $flashed = []): array {
        $didFlash = false;
        foreach ($this->grid as $y => $octopi) {
            foreach ($octopi as $x => $energy) {
                $hash = $this->hash($x, $y);

                if ($energy <= 9) {
                    continue;
                }

                $this->grid[$y][$x] = 0;

                if (isset($flashed[$hash])) {
                    continue;
                }

                $flashed[$hash] = true;
                $didFlash = true;
                $this->nbFlashes++;

                // increase adjacent squares
                foreach ([
                    [0, -1], [ 1, 1], [ 1, -1], [ 1, 0],
                    [0,  1], [-1, 1], [-1, -1], [-1, 0]
                ] as [$xo, $yo]) {
                    $this->apply($flashed, $x + $xo, $y + $yo, fn (int $energy) => $energy + 1);
                }
            }
        }

        if ($didFlash) {
            $this->flash($flashed);
        }

        return $flashed;
    }

    public function apply(array $flashed, int $x, int $y, Closure $closure): void {
        if (isset($flashed[$this->hash($x, $y)])) {
            return;
        }
        if (!isset($this->grid[$y][$x])) {
            return;
        }
        $this->grid[$y][$x] = $closure($this->grid[$y][$x]);
    }

    public function map(Closure $closure): self {
        $ng = $this->grid;
        foreach ($this->grid as $y => $octopi) {
            foreach ($octopi as $x => $octopus) {
                $ng[$y][$x] = $closure($ng[$y][$x]);
            }
        }
        return new self($ng);
    }

    public function toString(): string {
        return implode("\n", array_map(fn (array $chars) => implode(' ', $chars), $this->grid));
    }

    private function hash($x, $y): string
    {
        $hash = $x.'.'.$y;
        return $hash;
    }
}

$nbFlashes = 0;
$stage = 1;
while (!$grid->allFlashed()) {
    $grid = $grid->increment();
    $nbFlashes+= $grid->nbFlashes;
    echo sprintf("Stage: %s\n%s", $stage++, $grid->toString()).PHP_EOL.PHP_EOL;

}

echo $nbFlashes . PHP_EOL;

