<?php
$bytes = array_map(
    fn (string $line) => str_split(trim($line)),
    explode("\n", trim(file_get_contents(__DIR__ . '/input')))
);
$half = count($bytes) / 2;

[$gamma, $epsilon] = array_map(
    fn(array $byte) => bindec(implode('', $byte)),
    (function (array $gamma) {
        return [
            $gamma,
            array_map(fn (int $bit) => $bit == 1 ? 0 : 1, $gamma)
        ];
    })(array_map(function (int $bit) use ($half) {
        return $bit > $half ? 1 : 0;
    }, array_reduce(
        $bytes,
        function (array $counts, array $byte): array {
            foreach ($byte as $index => $bit) {
                $counts[$index]+=$bit;
            }
            return $counts;
        },
        array_fill(0, 12, 0),
    )))
);
echo $gamma * $epsilon;
