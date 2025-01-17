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

`<string (10-30 characters)><radom date (YYYYMMDD)><random int (1000-9999999)>`

These are approximate to the kinds of keys we generate in real world use. 

## Findings

### PHP murmur3a vs JS murmurhash3
```
PHP version: 8.1.2-1ubuntu2.20
Node.js version: v18.20.4
Seed: 0
```

Initial testing revealed no discrepancies between PHP's built-in `murmur3a` function and the `murmurhash3` modularised JavaScript implementation, this was across a large number of iterations (100,000) in 3 rounds of testing. 

Even the previously problematic key `sideBarRecommendations202501135278833` returns the same hash in this case. 

### PHP hash3Int vs JS murmurhash3

```
PHP version: 8.1.2-1ubuntu2.20
Node.js version: v18.20.4
Seed: 0
```

Testing with a custom PHP implementation `hash3Int` identified a small number of divergent hashes (5 out of 100,000). These keys are listed in `known_bad_keys_0.txt`.

### PHP hash3Int vs JS murmurhash3

```
PHP version: 8.1.2-1ubuntu2.20
Node.js version: v18.20.4
Seed: 123
```

Using a non-zero seed of `123` also identified a small number of divergent hashes (4 out of 100,000). These keys are listed in `known_bad_keys_123.text`

## Conclusion

While PHP's built-in `murmur3a` appears consistent with the JavaScript `murmurhash3` modularised JavaScript implementation, the custom PHP implementation of `hash3Int` does not. 

## Contributing

Feel free to submit issues or pull requests if you find any bugs or have improvements to suggest :)