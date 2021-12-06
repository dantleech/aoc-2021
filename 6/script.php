<?php

$lanterns = array_map('intval', explode(',', trim(file_get_contents(__DIR__ . '/input'))));

$ageCounts = array_fill(0, 9, 0);
foreach ($lanterns as $lantern) {
    $ageCounts[$lantern]++;
}

for ($x = 0; $x < 256; $x++) {
    $gen = $ageCounts;
    foreach ($ageCounts as $age => $count) {
        if ($age === 0) {
            $gen[0]-=$count;
            $gen[6]+=$count;
            $gen[8]+=$count;
            continue;
        }

        $gen[$age]-=$count;
        $gen[$age-1]+=$count;
    }
    $ageCounts = $gen;
}

echo array_sum($gen);

