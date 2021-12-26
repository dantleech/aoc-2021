<?php

class Pair {
    public function __construct(public string $left, public string $right) {}
    public function __toString(): string { return $this->left.$this->right; }
}

class Rule {
    public function __construct(public string $pair, public string $insert) {}
}

class Counter {
    public function __construct(public array $counts = []) {}
    public function inc(Pair $pair, int $amount = 1) {
        if (!isset($this->counts[(string)$pair])) {
            $this->counts[(string)$pair] = 0;
        }
        $this->counts[(string)$pair]+= $amount;
    }
    public function pairs(): array {
        return array_map(fn (string $pair) => new Pair(...str_split($pair)), array_keys($this->counts));
    }
    public function count(Pair $pair): int {
        return $this->counts[(string)$pair];
    }
    public function charCounts(string $firstChar): array {
        $counts = [
            $firstChar => 1
        ];
        foreach ($this->pairs() as $i => $pair) {
            if (!isset($counts[$pair->left])) {
                $counts[$pair->left] = 0;
            }
            $counts[$pair->left]+= $this->count($pair);
        }
        var_dump($counts);

        return $counts;
    }

    public function max(): int {
        return max($this->counts);
    }
    public function min(): int {
        return min($this->counts);
    }

    public function sum(): int {
        return array_sum($this->counts);
    }
}

[$chars, $rules] = (fn (array $p) => [
    str_split($p[0]),
    (fn (array $rules) => array_combine(array_map(fn (Rule $r) => $r->pair, $rules), $rules))(
        array_map(
            fn (string $rule) => new Rule(...explode(' -> ', $rule)),
            explode("\n", $p[1])
        )
    )
])(explode("\n\n", trim(file_get_contents(__DIR__ .'/input'))));

$pairs = [];
for ($i = 0; $i < count($chars) - 1; $i++) {
    $pairs[] = new Pair($chars[$i], $chars[$i+1]);
}

$counter = new Counter([]);

foreach ($pairs as $i => $pair) {
    $counter->inc($pair);
}

for ($i = 0; $i < 40; $i++) {
    $newCounter = new Counter();
    foreach ($counter->pairs() as $pair) {
        $insert = $rules[(string)$pair]->insert;

        $newCounter->inc(
            new Pair($pair->left, $insert),
            $counter->count($pair)
        );

        $newCounter->inc(
            new Pair($insert, $pair->right),
            $counter->count($pair)
        );
    }
    $counter = $newCounter;
}
$firstChar = $pairs[array_key_last($pairs)]->right;
var_dump(max($counter->charCounts($firstChar)) - min($counter->charCounts($firstChar)));
