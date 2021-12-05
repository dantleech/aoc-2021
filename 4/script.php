<?php

class Board {
    /** @var Row[] */public array $rows;
    public function __construct(array $rows) {
        $this->rows = $rows;
    }
    public static function parseBoard(&$lines): ?Board {
        if (empty($lines)) {
            return null;
        }
        $rows = [];
        for ($n = 0; $n < 5; $n++) {
            $rows[] = new Row(array_map(function (int $number) {
                return new Number($number);
            }, array_filter(array_map('trim', explode(' ', array_shift($lines))))));
        }
        return new Board($rows);
    }
    public function markNumber(int $number): void
    {
        foreach ($this->rows as $row) {
            $row->markNumber($number);
        }
    }
    public function isBingo(): bool {
        $columns = [];
        foreach ($this->rows as $row) {
            if ($row->isBingo()) {
                return true;
            }
            foreach ($row->numbers as $i => $n) {
                if ($n->marked === false) {
                    $columns[$i] = false;
                }
            }
        }
        foreach ($columns as $bingo) {
            if ($bingo === false) {
                return false;
            }
        }
        return true;
    }
}
class Row {
    /** @var Number[] */public array $numbers;

    public function __construct(array $numbers) {
        $this->numbers = $numbers;
    }

    public function markNumber(int $numberToMark): void
    {
        foreach ($this->numbers as $number) {
            if ($number->value !== $numberToMark) {
                continue;
            }
            $number->mark();
        }
    }

    public function isBingo(): bool
    {
        foreach ($this->numbers as $number) {
            if ($number->marked === false) {
                return false;
            }
        }

        return true;
    }
}
class Number {
    public int $value;
    public bool $marked = false;

    public function __construct(int $value) {
        $this->value = $value;
    }

    public function mark(): void {
        $this->marked = true;
    }
}
$lines = array_filter(
    explode("\n", file_get_contents(__DIR__ . '/input')),
    fn (string $line) => $line !== "\n" && $line !== ""
);
$numbers = array_map('intval', explode(",", array_shift($lines)));
$boards = [];
while (($board = Board::parseBoard($lines)) !== null) {
    $boards[] = $board;
}
foreach ($numbers as $number) {
    foreach ($boards as $i => $board) {
        $board->markNumber($number);

        if ($board->isBingo()) {
            die("BINGO: ".$i);
        }
    }
}
