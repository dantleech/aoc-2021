<?php

[$template, $rules] = (fn (array $p) => [
    $p[0],
    (fn (array $rules) => array_combine(array_map(fn (Rule $r) => $r->pair, $rules), $rules))(
        array_map(
            fn (string $rule) => new Rule(...explode(' -> ', $rule)),
            explode("\n", $p[1])
        )
    )
])(explode("\n\n", trim(file_get_contents(__DIR__ .'/testinput'))));

class Rule {
    public function __construct(public string $pair, public string $insert) {}
}

$chars = str_split($template);

$newChars = [];
$lastChar = null;

for ($i = 0; $i < 10; $i++) {
    foreach ($chars as $char) {
        if (empty($newChars)) {
            $newChars = [$char];
            continue;
        }

        $key = array_slice($newChars, -1)[0].$char;
        $rule = $rules[$key];
        $newChars[] = $rule->insert;
        $newChars[] = $char;
    }
    $chars = $newChars;
    $newChars = [];
}
$counts = [];
foreach ($chars as $char) {
    if (!isset($counts[$char])) {
        $counts[$char] = 0;
    }
    $counts[$char]++;
}
echo max($counts) - min($counts);
