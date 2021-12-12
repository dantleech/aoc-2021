<?php

$lines = array_map(
    fn (string $line): array => str_split($line),
    explode("\n", trim(file_get_contents(__DIR__ . '/input')))
);

$parser = new Parser();

$errors = 0;
foreach ($lines as $i => $line) {
    $err = $parser->parse($line);
    if (null !== $err) {
        $errors+= $err;
    }
}

echo $errors . PHP_EOL;

class Parser {
    private const PARENS = [
        '{' => '}',
        '[' => ']',
        '(' => ')',
        '<' => '>',
    ];
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
    public function parse(array $chars): ?int {
        $start = array_shift($chars);
        $stack = [];

        foreach ($chars as $i => $char) {
            if (isset(self::PARENS[$char])) {
                $stack[] = $char;
                continue;
            }

            $s = array_pop($stack);

            if (null === $s) {
                return null;
            }
            $expect = self::PARENS[$s];

            if ($expect != $char) {
                return $this->errorCode($char);
            }
        }

        return null;
    }

    private function errorCode($char): int
    {
        return match ($char) {
            ')' => 3,
            ']' => 57,
            '}' => 1197,
            '>' => 25137,
        };
    }
}



