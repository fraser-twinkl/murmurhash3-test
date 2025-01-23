# MurmurHash3 Cross-Language test

This test compares the output of MurmurHash3 implementations in JavaScript and PHP to identify potential inconsistencies between them.

It was put together to investigate a discrepancy observed in the `MurmurHash3` algorithm when implemented in JavaScript and PHP. Specifically, we encountered a key, `sideBarRecommendations202501135278833`, that produced different hash values between the `murmurhash3` modularised JavaScript implementation and a PHP implementation `hash3Int`.

This inconsistency raised concerns about the reliability of MurmurHash3 for cross-language applications where consistent hashing is crucial. To further explore this issue and identify potential inconsistencies, this test systematically compares the output of MurmurHash3 implementations across JavaScript and PHP using a variety of keys and seed values.

## Prerequisites

* Node.js (with npm)
* PHP

## Usage

Run the test suite using the following command:

```bash
php test_murmurhash3.php <iterations> <seed>
```

`<iterations>`: (Optional) The number of random keys to generate and test. Defaults to `1000`.

`<seed>`: (Optional) The seed value to use for the MurmurHash3 algorithm in both JavaScript and PHP. Defaults to `0`.

## Key Format

The test suite generates random keys in the following format:

`random <string (10-30 characters)><random date (YYYYMMDD)><random int (1000-9999999)>`

These are approximate to the kinds of keys we generate in real world use. 

## Findings

### PHP murmur3a vs JS murmurhash3
```
PHP version: 8.1.2-1ubuntu2.20
Node.js version: v18.20.4
Seed: 0
```

Initial testing revealed no discrepancies between PHP's built-in `murmur3a` function and the `murmurhash3` modularised JavaScript implementation, this was across a large number of iterations (300,000) in 3 rounds (100,000) of testing. 

Even the previously problematic key `sideBarRecommendations202501135278833` returns the same hash in this case. 

### PHP hash3Int vs JS murmurhash3 (zero seed)

```
PHP version: 8.1.2-1ubuntu2.20
Node.js version: v18.20.4
Seed: 0
```

Testing with a custom PHP implementation `hash3Int` identified a small number of divergent hashes (5 out of 100,000). These keys are listed in `known_bad_keys_0.txt`.

### PHP hash3Int vs JS murmurhash3 (non-zero seed)

```
PHP version: 8.1.2-1ubuntu2.20
Node.js version: v18.20.4
Seed: 123
```

Using a non-zero seed of `123` also identified a small number of divergent hashes (4 out of 100,000). These keys are listed in `known_bad_keys_123.text`

## Conclusion

While PHP's built-in `murmur3a` appears consistent with the `murmurhash3` modularised JavaScript implementation, the custom PHP implementation of `hash3Int` does not. 

I have rasied this as an issue on `lastguest/murmurhash-php` here: https://github.com/lastguest/murmurhash-php/issues/16

## Resolution 

The main issues in the original implementation were:

- Incorrect handling of unsigned multiplication, especially for large numbers
- Inconsistent handling of 32-bit unsigned integers
- The complex bit manipulation for the "mixing" steps wasn't properly accounting for PHP's signed integer behaviour

The key changes I've made to fix the implementation are 

- Added proper unsigned multiplication handling through a helper function. This was the main source of divergence, as PHP's native multiplication can produce incorrect results with large 32-bit values.
- Improved the unsigned right shift implementation to handle edge cases.
- Fixed the final mixing steps to properly handle unsigned operations.

When I test PHP's built-in `murmur3a` and/or the `murmurhash3` modularised JavaScript implementation against `hash3Int_fc` there are no divergences.
Even in a large number of iterations (0 out of 1,000,000)

### JS murmurhash3 vs PHP hash3Int_fc
see https://github.com/fraser-twinkl/murmurhash3-test/blob/main/test_murmurhash3_fixed.php 

```bash
php test_murmurhash3_fixed.php <iterations> <seed>
```

```
Running: 1000000 iterations
PHP version: 8.1.2-1ubuntu2.20
Node.js version: v18.20.4
Seed: 0
Progress: 1000000/1000000
All hashes matched!
```

### PHP murmur3a vs PHP hash3Int_fc
see https://github.com/fraser-twinkl/murmurhash3-test/blob/main/test2_murmurhash3_fixed.php

```bash
php test2_murmurhash3_fixed.php <iterations> <seed>
```

```
Running: 1000000 iterations
PHP version: 8.1.2-1ubuntu2.20
Seed: 0
Progress: 1000000/1000000
All hashes matched!
```

### PHP murmur3a vs JS murmurhash3 vs PHP hash3Int_fc (non-zero seeds)
see https://github.com/fraser-twinkl/murmurhash3-test/blob/main/test_all.php

This is a combined test that generates hashes in all 3 implementations and compares them against each other.
It batches the keys and uses a random seed for each batch.
It is simply much faster way of comparing the implemenations whilst also testing non-zero keys. 

```bash
php test_all.php <iterations> <batchsize>
```

`<iterations>`: (Optional) The number of random keys to generate and test. Defaults to `1000000`.

`<batchsize>`: (Optional) The size for each batch of tests. Defaults to `1000`.

```
Running: 1000000 iterations
PHP version: 8.1.2-1ubuntu2.20
Node version: v18.20.4
Batch size: 1000
Batch: 1000 Seed: 87053 Progress: 1000000/1000000
All hashes matched!
```

## Contributing

Feel free to submit issues or pull requests if you find any bugs or have improvements to suggest :)
