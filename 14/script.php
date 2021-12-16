<?php

[$template, $rules] = (fn (array $p) => [
    $p[0],
    array_map(fn (string $rule) => new Rule(...explode(' -> ', $rule)), explode("\n", $p[1]))
])(explode("\n\n", trim(file_get_contents(__DIR__ .'/testinput'))));

class Rule {
    public function __construct(public string $pair, public string $insert) {}
}
