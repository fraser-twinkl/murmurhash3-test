<?php

/**
 * PHP Implementation of MurmurHash3
 *
 * @author Stefano Azzolini (lastguest@gmail.com)
 * @see https://github.com/lastguest/murmurhash-php
 * @author Gary Court (gary.court@gmail.com)
 * @see http://github.com/garycourt/murmurhash-js
 * @author Austin Appleby (aappleby@gmail.com)
 * @see http://sites.google.com/site/murmurhash/
 *
 * @param  string $key   Text to hash.
 * @param  integer $seed  Positive integer only
 * @return integer 32-bit positive integer hash
 */
function hash3Int(string $key, int $seed = 0): int
{
    $key = array_values(unpack('C*', $key));
    $klen = count($key);
    $h1 = $seed < 0 ? -$seed : $seed;
    $i = 0;
    for ($bytes = $klen - ($remainder = $klen & 3); $i < $bytes; ) {
        $k1 = $key[$i]
            | ($key[++$i] << 8)
            | ($key[++$i] << 16)
            | ($key[++$i] << 24);
        ++$i;
        $k1 = (((($k1 & 0xffff) * 0xcc9e2d51) + ((((($k1 >= 0 ? $k1 >> 16 : (($k1 & 0x7fffffff) >> 16) | 0x8000)) * 0xcc9e2d51) & 0xffff) << 16))) & 0xffffffff;
        $k1 = $k1 << 15 | ($k1 >= 0 ? $k1 >> 17 : (($k1 & 0x7fffffff) >> 17) | 0x4000);
        $k1 = (((($k1 & 0xffff) * 0x1b873593) + ((((($k1 >= 0 ? $k1 >> 16 : (($k1 & 0x7fffffff) >> 16) | 0x8000)) * 0x1b873593) & 0xffff) << 16))) & 0xffffffff;
        $h1 ^= $k1;
        $h1 = $h1 << 13 | ($h1 >= 0 ? $h1 >> 19 : (($h1 & 0x7fffffff) >> 19) | 0x1000);
        $h1b = (((($h1 & 0xffff) * 5) + ((((($h1 >= 0 ? $h1 >> 16 : (($h1 & 0x7fffffff) >> 16) | 0x8000)) * 5) & 0xffff) << 16))) & 0xffffffff;
        $h1 = ((($h1b & 0xffff) + 0x6b64) + ((((($h1b >= 0 ? $h1b >> 16 : (($h1b & 0x7fffffff) >> 16) | 0x8000)) + 0xe654) & 0xffff) << 16));
    }
    $k1 = 0;
    switch ($remainder) {
        case 3:
            $k1 ^= $key[$i + 2] << 16;
        case 2:
            $k1 ^= $key[$i + 1] << 8;
        case 1:
            $k1 ^= $key[$i];
            $k1 = ((($k1 & 0xffff) * 0xcc9e2d51) + ((((($k1 >= 0 ? $k1 >> 16 : (($k1 & 0x7fffffff) >> 16) | 0x8000)) * 0xcc9e2d51) & 0xffff) << 16)) & 0xffffffff;
            $k1 = $k1 << 15 | ($k1 >= 0 ? $k1 >> 17 : (($k1 & 0x7fffffff) >> 17) | 0x4000);
            $k1 = ((($k1 & 0xffff) * 0x1b873593) + ((((($k1 >= 0 ? $k1 >> 16 : (($k1 & 0x7fffffff) >> 16) | 0x8000)) * 0x1b873593) & 0xffff) << 16)) & 0xffffffff;
            $h1 ^= $k1;
    }
    $h1 ^= $klen;
    $h1 ^= ($h1 >= 0 ? $h1 >> 16 : (($h1 & 0x7fffffff) >> 16) | 0x8000);
    $h1 = ((($h1 & 0xffff) * 0x85ebca6b) + ((((($h1 >= 0 ? $h1 >> 16 : (($h1 & 0x7fffffff) >> 16) | 0x8000)) * 0x85ebca6b) & 0xffff) << 16)) & 0xffffffff;
    $h1 ^= ($h1 >= 0 ? $h1 >> 13 : (($h1 & 0x7fffffff) >> 13) | 0x40000);
    $h1 = (((($h1 & 0xffff) * 0xc2b2ae35) + ((((($h1 >= 0 ? $h1 >> 16 : (($h1 & 0x7fffffff) >> 16) | 0x8000)) * 0xc2b2ae35) & 0xffff) << 16))) & 0xffffffff;
    $h1 ^= ($h1 >= 0 ? $h1 >> 16 : (($h1 & 0x7fffffff) >> 16) | 0x8000);

    return $h1;
}
