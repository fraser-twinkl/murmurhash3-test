<?php

require 'murmur3a.php';
require 'murmurhash3-optimised.php';
require 'randomkey.php';

function runTests($numTests, $seed)
{
    echo "Running: $numTests iterations\n";
    echo "PHP version: " . phpversion() . "\n";
    echo "Seed: $seed\n";
    $divergentHashes = [];
    for ($i = 0; $i < $numTests; $i++) {
        echo "Progress: " . ($i + 1) . "/$numTests\r";
        $key = generateRandomKey();
        $hash3Int = hash3Int_fc_opt($key, $seed);
        $murmur3a = murmur3a($key, $seed);
        if ($hash3Int !== $murmur3a) {
            $divergentHashes[] = [
                'key' => $key,
                'hash3Int' => $hash3Int,
                'murmur3a' => $murmur3a
            ];
        }
    }

    echo "\n";

    return $divergentHashes;
}

$numTests = isset($argv[1]) ? (int) $argv[1] : 100000;
$seed = isset($argv[2]) ? (int) $argv[2] : 0;
$divergentHashes = runTests($numTests, $seed);

if (empty($divergentHashes)) {
    echo "All hashes matched!\n";
} else {
    echo "Divergent hashes found:\n";
    print_r($divergentHashes);
}