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

    public function walk(
        string $node,
        array $path = [],
        array &$seen = [],
        array $smalls = []
    ) {
        if (!isset($smalls[$node])) {
            $smalls[$node] = 0;
        }

        if ($node === 'start' && in_array($node, $path)) {
            return $seen;
        }
        if ($node === 'end' && in_array($node, $path)) {
            return $seen;
        }

        if ($this->isSmall($node) && in_array($node, $path)) {
            if ($this->haveAnyBeenVisitedTwice($smalls)) {
                return $seen;
            }

            if ($smalls[$node] == 2) {
                return $seen;
            }
        }

        if ($this->isSmall($node)) {
            $smalls[$node]++;
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
            $seen = $this->walk($to, $path, $seen, $smalls);
        }

        return $seen;
    }

    private function isSmall(string $to): bool
    {
        return $to === strtolower($to);
    }

    private function haveAnyBeenVisitedTwice(array $smalls)
    {
        return in_array(2, $smalls);
    }
}

$f = new PathFinder(array_map(
    fn(string $line) => explode('-', $line),
    explode("\n", trim(file_get_contents(__DIR__ . '/input')))
));

var_dump(count(
    array_filter(
        array_keys(
            $f->walk('start')
        ),
        fn ($line) => substr($line, -3) === 'end'
    )
));
