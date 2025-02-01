<?php


/**
 * Working and optimised PHP Implementation of MurmurHash3
 *
 * Based on lastguest/murmurhash-php 
 *
 * @author Fraser Chapman (fraser.chapman@twinkl.co.uk)
 */

/**
 * @param  string $key    Text to hash.
 * @param  integer $seed  Positive integer only
 * @return integer 32-bit positive integer hash
 */
function hash3Int_fc_opt($key, $seed = 0)
{
    $key = array_values(unpack('C*', $key));
    $klen = count($key);
    $h1 = $seed < 0 ? -$seed : $seed;
    $bytes = $klen - ($remainder = $klen & 3);

    for ($i = 0; $i < $bytes; ) {
        $k1 = $key[$i] |
            ($key[++$i] << 8) |
            ($key[++$i] << 16) |
            ($key[++$i] << 24);
        ++$i;

        $k1 = (($k1 & 0xFFFF) * 0x2D51 + ((($k1 >> 16) * 0x2D51 + ($k1 & 0xFFFF) * 0xCC9E) << 16)) & 0xFFFFFFFF;
        $k1 = (($k1 << 15) | (($k1 >> 17) & 0x7FFF)) & 0xFFFFFFFF;
        $k1 = (($k1 & 0xFFFF) * 0x3593 + ((($k1 >> 16) * 0x3593 + ($k1 & 0xFFFF) * 0x1B87) << 16)) & 0xFFFFFFFF;

        $h1 ^= $k1;
        $h1 = (($h1 << 13) | (($h1 >> 19) & 0x1FFF)) & 0xFFFFFFFF;
        $h1 = (($h1 & 0xFFFF) * 5 + ((($h1 >> 16) * 5) << 16)) & 0xFFFFFFFF;
        $h1 = ($h1 + 0xe6546b64) & 0xFFFFFFFF;
    }

    if ($remainder) {
        $k1 = 0;
        switch ($remainder) {
            case 3:
                $k1 ^= $key[$i + 2] << 16;
            case 2:
                $k1 ^= $key[$i + 1] << 8;
            case 1:
                $k1 ^= $key[$i];
                $k1 = (($k1 & 0xFFFF) * 0x2D51 + ((($k1 >> 16) * 0x2D51 + ($k1 & 0xFFFF) * 0xCC9E) << 16)) & 0xFFFFFFFF;
                $k1 = (($k1 << 15) | (($k1 >> 17) & 0x7FFF)) & 0xFFFFFFFF;
                $k1 = (($k1 & 0xFFFF) * 0x3593 + ((($k1 >> 16) * 0x3593 + ($k1 & 0xFFFF) * 0x1B87) << 16)) & 0xFFFFFFFF;
                $h1 ^= $k1;
        }
    }

    $h1 ^= $klen;
    $h1 ^= ($h1 >> 16) & 0xFFFF;
    $h1 = (($h1 & 0xFFFF) * 0xCA6B + ((($h1 >> 16) * 0xCA6B + ($h1 & 0xFFFF) * 0x85EB) << 16)) & 0xFFFFFFFF;
    $h1 ^= ($h1 >> 13) & 0x7FFFF;
    $h1 = (($h1 & 0xFFFF) * 0xAE35 + ((($h1 >> 16) * 0xAE35 + ($h1 & 0xFFFF) * 0xC2B2) << 16)) & 0xFFFFFFFF;
    $h1 ^= ($h1 >> 16) & 0xFFFF;

    return $h1;
}
