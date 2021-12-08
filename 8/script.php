<?php

function first(array $list) {
    if (count($list) !== 1) {
        throw new Exception(sprintf('List must be reduced to one element, got "%s"', count($list)));
    }
    $first = reset($list);
    return $first;
}
function byLength(array $list, int $length) {
    return array_filter($list, fn (array $chars) => count($chars) === $length);
}
function contains(array $set, array $list) {
    return array_filter(
        $list,
        fn (
            array $chars
        ) => count(
            array_diff(
                $set,
                $chars
            )
        ) === 0
    );
}
function doesNotContain(array $set, array $list) {
    return array_filter(
        $list,
        fn (
            array $chars
        ) => count(
            array_diff(
                $set,
                $chars
            )
        ) > 0
    );
}
function byNbMatchingSegmentsIn(array $set, int $count, array $list) {
    return array_filter(
        $list,
        function (array $chars) use ($set, $count) {
            $diff = array_diff($chars, $set);
            $matching = count($chars) - count($diff);

            return $matching >= $count;
        }
    );
}

class Display {
    public function __construct(public array $sigc, public array $display) {
    }

    public function resolveSegments(): array {
        $one = first(byLength($this->sigc, 2));
        $three = first(
            contains($one, byLength($this->sigc, 5))
        );
        $four = first(
            byLength($this->sigc, 4)
        );
        $five = first(
            byNbMatchingSegmentsIn(
                $four,
                3,
                doesNotContain($one, byLength($this->sigc, 5))
            )
        );
        $two = first(
            doesNotContain($five, doesNotContain($one, byLength($this->sigc, 5)))
        );
        $six = first(
            contains($five, doesNotContain($one, byLength($this->sigc, 6)))
        );
        $seven = first(
            byLength($this->sigc, 3)
        );
        $eight = first(
            byLength($this->sigc, 7)
        );
        $nine = first(
            contains($four, byLength($this->sigc, 6))
        );
        $zero = first(
            doesNotContain($six, doesNotContain($nine, byLength($this->sigc, 6)))
        );

        return [
            0 => $zero,
            1 => $one,
            2 => $two,
            3 => $three,
            4 => $four,
            5 => $five,
            6 => $six,
            7 => $seven,
            8 => $eight,
            9 => $nine,
        ];
    }

    /**
     * acedgfb
     *
     *  0000       0 => 7, 8
     * 1    2      1 => 4, 8
     * 1    2      2 => 1, 4, 7, 8
     *  3333       3 => 4, 8
     * 4    5      4 => 8
     * 4    5      5 => 1, 4, 7, 8
     *  6666       6 => 8
     *
     * - KNOWNS 1, 4, 7, 8, 9, 3
     *
     * - ✓ 1 ab:
     *   - a + b has two segments => 2 + 5
     * - ✓ 2 gcdfa:
     *   - has 5 segments
     *     - cdfbe
     *     - gcdfa
     *     - fbcad
     *   - does not contain subset of 1
     *     - cdfbe
     *     - gcdfa
     *   - is not 5
     *     - gcdfa
     *
     * - ✓ 3 fbcad:
     *   - has 5 segments
     *     - cdfbe
     *     - gcdfa
     *     - fbcad
     *   - contains subset of 1
     *     - fbcad
     *
     * - ✓ 4 eafb:
     *   - has 4 segments [ 1, 3, 2, 5 ]
     *
     * - ✓ 5 cdfbe:
     *   - has 5 segments
     *     - cdfbe
     *     - gcdfa
     *     - fbcad
     *   - does not contain subset of 1
     *     - cdfbe
     *     - gcdfa
     *   - matches 3 segments from 4
     *     - cdfbe
     *
     * - ✓ 6:
     *   -  has 6 segments
     *     - cefabd
     *     - cdfgeb
     *     - cagedb
     *   - is not containg 1
     *     - cdfgeb
     *     - cagedb
     *   - contains 5 (cdfbe)
     *     - cdfgeb
     *
     * - ✓ 7 dab
     *
     * - ✓ 8 acedgfb
     *    - has 7 segments
     *
     * - ✓ 9 cefabd:
     *   - has 6 segments => [ 0, 1, 2, 3, 5, 6 ]
     *     - cefabd
     *     - cdfgeb
     *     - cagedb
     *   - contains 4 (eafb)
     *     - cefabd
     *
     * - ✓ zero
     *   - has 6 segments
     *     - cefabd
     *     - cdfgeb
     *     - cagedb
     *   - is not 9 or 6
     */
     public function resolve(): int {
         $segments = $this->resolveSegments();
         $numbers = [];

         foreach ($this->display as $chars) {
             foreach ($segments as $number => $sc) {
                 if (count($sc) === count($chars) && count(array_diff($chars, $sc)) === 0) {
                     $numbers[] = $number;
                     continue 2;
                 }
             }

             throw new Exception(sprintf('Could not match "%s"', implode('', $chars)));
         }

         return intval(implode('', $numbers));
     }
}
class Hud {
    public function __construct(/** @var Display[] */public array $displays) {
    }
    public function resolve(): int {
        $total = 0;
        foreach ($this->displays as $display) {
            $total += $display->resolve();
        }
        return $total;
    }
}

$hud = new Hud(array_map(function (string $line) {
    [$sigc, $display] = explode(' | ', $line);
    $conv = fn (string $charString) => array_map('str_split', explode(' ', $charString));
    return new Display($conv($sigc), $conv($display));
}, explode("\n", trim(file_get_contents(__DIR__ . '/input')))));

echo $hud->resolve();
