<?php

/**
 * @param  string $key   Text to hash.
 * @param  integer $seed  Positive integer only
 * @return integer 32-bit positive integer hash
 */
function murmur3a(string $key, int $seed = 0): int
{
    if (PHP_MAJOR_VERSION >= 8 && PHP_MINOR_VERSION >= 1) {
        return (int) base_convert(hash('murmur3a', $key, false, ["seed" => $seed]), 16, 10);
    }

    return 1;
}
