<?php

require 'murmurhash3-fixed.php';
require 'randomkey.php';

function runTests($numTests, $seed)
{
    echo "Running: $numTests iterations\n";
    echo "PHP version: " . phpversion() . "\n";
    echo "Node.js version: " . trim(shell_exec("node -v")) . "\n";
    echo "Seed: " . $seed . "\n";

    $divergentHashes = [];

    for ($i = 0; $i < $numTests; $i++) {

        echo "Progress: " . ($i + 1) . "/$numTests\r";

        $key = generateRandomKey();
        $jsHash = trim(shell_exec("node -e \"import('./murmurhash3.mjs').then(m => console.log(m.default('" . $key . "', " . $seed . "))) \""));
        $phpHash = hash3Int_fc($key, $seed);
        if ((int) $jsHash !== $phpHash) {
            $divergentHashes[] = [
                'key' => $key,
                'jsHash' => $jsHash,
                'phpHash' => $phpHash
            ];
        }
    }

    echo "\n";

    return $divergentHashes;
}

$numTests = isset($argv[1]) ? (int) $argv[1] : 1000;
$seed = isset($argv[2]) ? (int) $argv[2] : 0;
$divergentHashes = runTests($numTests, $seed);

if (empty($divergentHashes)) {
    echo "All hashes matched!\n";
} else {
    echo "Divergent hashes found:\n";
    print_r($divergentHashes);
}
