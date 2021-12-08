<?php

class Display {
    public function __construct(public array $sigc, public array $display) {
    }
}
class Hud {
    const LENGTHS = [
        2 => 1,
        4 => 4,
        3 => 7,
        7 => 8,
    ];
    public function __construct(/** @var Display[] */public array $displays) {
    }
    public function countUnique(): int { 
        $count = 0;
        foreach ($this->displays as $display) {
            $lengths = array_map(fn (string $chars) => strlen($chars), $display->display);
            $count += count(array_filter($lengths, fn (int $length): bool => array_key_exists($length, self::LENGTHS)));
        }

        return $count;
    }
}

$hud = new Hud(array_map(function (string $line) {
    [$sigc, $display] = explode(' | ', $line);
    $conv = fn (string $charString) => explode(' ', $charString);
    return new Display($conv($sigc), $conv($display));
}, explode("\n", trim(file_get_contents(__DIR__ . '/input')))));

echo $hud->countUnique();
