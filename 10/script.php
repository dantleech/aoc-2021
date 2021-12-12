<?php

$lines = array_map(
    fn (string $line): array => str_split($line),
    explode("\n", trim(file_get_contents(__DIR__ . '/input')))
);
class Parser {
    private const PARENS = [
        '{' => '}',
        '[' => ']',
        '(' => ')',
        '<' => '>',
    ];
    
    public function parse(array $chars): Result {
        $stack = [];

        foreach ($chars as $i => $char) {
            if (isset(self::PARENS[$char])) {
                $stack[] = $char;
                continue;
            }

            $s = array_pop($stack);

            if (null === $s) {
                return new Result(0, []);
            }

            $expect = self::PARENS[$s];

            if ($expect != $char) {
                return Result::fromInvalidChar($char);
            }
        }

        return new Result(0, array_map(fn (string $char) => self::PARENS[$char], array_reverse($stack)));
    }
}

final class Result {
    public function __construct(public int $err, public array $completion) {
    }

    public static function fromInvalidChar(string $char): self {
        return new self(match ($char) {
            ')' => 3,
            ']' => 57,
            '}' => 1197,
            '>' => 25137,
        }, []);
    }

    public function completionScore(): int {
        $total = 0;
        foreach ($this->completion as $char) {
            $total *= 5;
            $total += match ($char) {
                ')' => 1,
                ']' => 2,
                '}' => 3,
                '>' => 4,
            };
        }

        return $total;
    }
}



$parser = new Parser();
$errors = 0;
$compScores = [];
foreach ($lines as $i => $line) {
    $r = $parser->parse($line);
    $errors += $r->err;

    if ($r->completion) {
        $compScores[] = $r->completionScore();
    }
}

sort($compScores);
echo $compScores[count($compScores) / 2] . "\n";
