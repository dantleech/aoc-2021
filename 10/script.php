<?php

$lines = array_map(
    fn (string $line): array => str_split($line),
    explode("\n", trim(file_get_contents(__DIR__ . '/testinput')))
);

foreach ($lines as $i => $line) {
    try {
        parse($line);
    } catch (Exception $e) {
        echo sprintf('Error on line "%s": %s', $i, $e->getMessage()) . PHP_EOL;
    }
}

/**
 * - Is character open char?
 *   - Yes: Add to stack, parse [ ( ]
 *   - No: Pop from stack, is [] => (
 *
 * ()
 *
 * - is open? add to stack
 * - is close?
 * [ ( { ( < ( () )[]>[[{[]{<()<>>
 */
function parse(array &$chars): void {
    $o = [
        '{' => '}',
        '[' => ']',
        '(' => ')',
        '<' => '>',
    ];
    $c = array_flip($o);

    $start = array_shift($chars);

    $stack = [];

    foreach ($chars as $i => $char) {
        if (isset($o[$char])) {
            $stack[] = $char;
            continue;
        }

        $s = array_pop($stack);

        if (null === $s) {
            return;
        }
        $expect = $o[$s];
        if ($expect != $char) {
            throw new RuntimeException(sprintf(
                'Expected "%s" got "%s"',
                $expect, $char
            ));
        }
    }
}
