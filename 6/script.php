<?php

$lanterns = array_map('intval', explode(',', trim(file_get_contents(__DIR__ . '/input'))));

for ($day = 1; $day <= 80; $day++) {
    foreach ($lanterns as &$lantern) {
        if ($lantern === 0) {
            $lantern = 6;
            $lanterns[] = 9;
        } else {
            $lantern--;
        }
    }
}

echo count($lanterns);

