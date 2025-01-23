<?php

require 'murmur3a.php';
require 'murmurhash3-fixed.php';
require 'randomkey.php';

function runTests($numTests, $batchSize)
{
    echo "Running: $numTests iterations\n";
    echo "PHP version: " . phpversion() . "\n";
    echo "Node version: " . trim(shell_exec("node -v")) . "\n";
    echo "Batch size: $batchSize\n";

    $divergentHashes = [];

    for ($i = 0; $i < $numTests; $i += $batchSize) {
        $seed = rand(0, 99999);
        $batchKeys = [];
        for ($j = 0; $j < $batchSize && ($i + $j) < $numTests; $j++) {
            $batchKeys[] = generateRandomKey();
        }

        $process = proc_open('node murmurhash-batch.mjs', [0 => ['pipe', 'r'], 1 => ['pipe', 'w']], $pipes);
        
        fwrite($pipes[0], json_encode(['keys' => $batchKeys, 'seed' => $seed]));
        fclose($pipes[0]);

        $jsMurmurhash = json_decode(stream_get_contents($pipes[1]), true);

        fclose($pipes[1]);
        proc_close($process);

        foreach ($batchKeys as $idx => $key) {
            $hash3Int_fc = hash3Int_fc($key, $seed);
            $murmur3a = murmur3a($key, $seed);
            if ($jsMurmurhash[$idx] !== $murmur3a || $hash3Int_fc !== $murmur3a || $jsMurmurhash[$idx] !== $hash3Int_fc) {
                $divergentHashes[] = [
                    'key' => $key,
                    'seed' => $seed,
                    'js_murmurhash' => $jsMurmurhash[$idx],
                    'php_hash3Int_fc' => $hash3Int_fc,
                    'php_murmur3a' => $murmur3a
                ];
            }

        }

        echo "Batch: " . ($i / $batchSize) + 1 . " Seed: $seed Progress: " . min($i + $batchSize, $numTests) . "/$numTests\r";
    }

    echo "\n";

    return $divergentHashes;
}

$numTests = isset($argv[1]) ? (int) $argv[1] : 1000000;
$batchSize = isset($argv[2]) ? (int) $argv[2] : 1000;
$divergentHashes = runTests($numTests, $batchSize);

if (empty($divergentHashes)) {
    echo "All hashes matched!\n";
} else {
    echo "Divergent hashes found:\n";
    print_r($divergentHashes);
}
