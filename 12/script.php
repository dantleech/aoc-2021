<?php

class PathFinder {
    private array $graph = [];

    public function __construct(array $segments) {
        foreach ($segments as [$from, $to]) {
            if (!isset($this->graph[$from])) {
                $this->graph[$from] = [];
            }
            $this->graph[$from][] = $to;
            if (!isset($this->graph[$to])) {
                $this->graph[$to] = [];
            }
            $this->graph[$to][] = $from;
        }
        $this->graph = array_map(fn (array $nodes) => array_unique($nodes), $this->graph);
    }

    public function findPath(): array
    {
        return $this->walk('start');
    }

    public function walk(string $node, array $smallCaves = [], $path = []) {
        if ($path[array_key_last($path)] === 'end') {
            return $path;
        }

        // if we saw this small cave before
        if (isset($smallCaves[$node])) {
            return $path;
        }

        $path[] = $node;

        if ($node === 'end') {
            return $path;
        }

        if ($this->isSmall($node)) {
            $smallCaves[$node] = true;
        }

        foreach ($this->graph[$node] as $to) {
            if ($to === 'start') {
                continue;
            }
            $path = $this->walk($to, $smallCaves, $path);
        }

        return $path;
    }

    private function isSmall(string $to): bool
    {
        return $to === strtolower($to);
    }
}

$f = new PathFinder(array_map(
    fn(string $line) => explode('-', $line),
    explode("\n", trim(file_get_contents(__DIR__ . '/testinput')))
));
var_dump($f->findPath());
