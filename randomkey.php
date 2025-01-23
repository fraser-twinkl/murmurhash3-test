<?php

function generateRandomKey()
{
    $stringPart = substr(str_shuffle(str_repeat("abcdefghijklmnopqrstuvwxyz", 10)), 0, rand(10, 30));
    $datePart = date("Ymd", timestamp: rand(strtotime("2000-01-01"), strtotime("2030-12-31")));
    $intPart = rand(1000, 9999999);

    return $stringPart . $datePart . $intPart;
}
