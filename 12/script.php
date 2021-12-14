<?php

class PathFinder {
    private array $graph;

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

    public function walk(string $node, array $path = [], array $seen = []) {
        if ($this->isSmall($node) && in_array($node, $path)) {
            return $seen;
        }

        $path[] = $node;
        $hash = implode(',',$path);

        if ($node === 'end') {
            $seen[$hash] = $node;
            return $seen;
        }

        if (array_key_exists($hash, $seen)) {
            return $seen;
        }

        $seen[$hash] = $node;

        foreach ($this->graph[$node] as $to) {
            $seen = $this->walk($to, $path, $seen);
        }

        return $seen;
    }

    private function isSmall(string $to): bool
    {
        return $to === strtolower($to);
    }
}

$f = new PathFinder(array_map(
    fn(string $line) => explode('-', $line),
    explode("\n", trim(file_get_contents(__DIR__ . '/input')))
));

var_dump(count(array_filter(array_keys($f->findPath()), fn ($line) => substr($line, -3) === 'end')));
