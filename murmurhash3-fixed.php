<?php

/**
 * Working PHP Implementation of MurmurHash3
 *
 * Based on lastguest/murmurhash-php 
 *
 * Fixes 
 *
 * 1) The incorrect handling of unsigned multiplication, especially for large numbers
 * 2) Inconsistent handling of 32-bit unsigned integers
 * 3) Bit manipulation for the "mixing" steps to account for PHP's signed integer behaviour
 *
 * @author Fraser Chapman (fraser.chapman@twinkl.co.uk)
 */

/**
 * @param  string $key    Text to hash.
 * @param  integer $seed  Positive integer only
 * @return integer 32-bit positive integer hash
 */
function hash3Int_fc($key, $seed = 0)
{
    $key = array_values(unpack('C*', $key));
    $klen = count($key);
    $h1 = $seed < 0 ? -$seed : $seed;

    //  (fix #1) - correctly perform unsigned right shift
    $u32rs = function ($a, $b) {
        if ($b == 0) {
            return $a;
        }

        $shifted = $a >> $b;
        $mask = 0xFFFFFFFF >> $b;

        return $shifted & $mask;
    };

    // (fix #2) - correctly handle unsigned 32-bit multiplication
    $u32m = function ($a, $b) {
        $a &= 0xFFFFFFFF;
        $b &= 0xFFFFFFFF;
        $lo = ($a & 0xFFFF) * ($b & 0xFFFF);
        $mid = ($a >> 16) * ($b & 0xFFFF) + ($a & 0xFFFF) * ($b >> 16);
        $result = $lo + ($mid << 16);
        
        return $result & 0xFFFFFFFF;
    };

    for ($i = 0, $bytes = $klen - ($remainder = $klen & 3); $i < $bytes; ) {
        $k1 = (($key[$i] & 0xff))
            | (($key[++$i] & 0xff) << 8)
            | (($key[++$i] & 0xff) << 16)
            | (($key[++$i] & 0xff) << 24);
        ++$i;

        $k1 = $u32m($k1, 0xcc9e2d51);
        $k1 = (($k1 << 15) | $u32rs($k1, 17)) & 0xFFFFFFFF;
        $k1 = $u32m($k1, 0x1b873593);

        $h1 ^= $k1;
        $h1 = (($h1 << 13) | $u32rs($h1, 19)) & 0xFFFFFFFF;
        $h1 = ($u32m($h1, 5) + 0xe6546b64) & 0xFFFFFFFF;
    }

    $k1 = 0;
    switch ($remainder) {
        case 3:
            $k1 ^= ($key[$i + 2] & 0xff) << 16;
        case 2:
            $k1 ^= ($key[$i + 1] & 0xff) << 8;
        case 1:
            $k1 ^= $key[$i] & 0xff;
            $k1 = $u32m($k1, 0xcc9e2d51);
            $k1 = (($k1 << 15) | $u32rs($k1, 17)) & 0xFFFFFFFF;
            $k1 = $u32m($k1, 0x1b873593);
            $h1 ^= $k1;
    }

    $h1 ^= $klen;

    // (fix #3) - proper handling of signed/unsigned bit manipulation
    $h1 ^= $u32rs($h1, 16);
    $h1 = $u32m($h1, 0x85ebca6b);
    $h1 ^= $u32rs($h1, 13);
    $h1 = $u32m($h1, 0xc2b2ae35);
    $h1 ^= $u32rs($h1, 16);

    return $h1 & 0xFFFFFFFF;
}
