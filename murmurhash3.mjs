// murmurhash3 - modularised
const constant1 = 0xcc9e2d51;
const constant2 = 0x1b873593;

// Deterministic hashing algorithm
export default function murmurhash3(inputString, seed = 0) {
    const remainingBytes = inputString.length & 3;

    // equivalent to key.length % 4
    const processedBytesLength = inputString.length - remainingBytes;

    let currentHash = seed;

    let index = 0;

    // Process 4 bytes at a time
    while (index < processedBytesLength) {
        // Thoroughly mix the input bits, creating an avalanche effect where a small change in the input causes a large change in the output
        const blockValue = processBlock(inputString, index);

        index += 4;
        currentHash = mixHash(currentHash, blockValue);
    }

    // Process any remaining bytes
    const blockValue = processRemainingBytes(inputString, index, remainingBytes);

    currentHash ^= blockValue;

    // Final mixing of the hash
    return finalizeHash(currentHash, inputString.length);

}

function processBlock(inputString, index) {
    let blockValue =
        (inputString.charCodeAt(index) & 0xff) |
        ((inputString.charCodeAt(index + 1) & 0xff) << 8) |
        ((inputString.charCodeAt(index + 2) & 0xff) << 16) |
        ((inputString.charCodeAt(index + 3) & 0xff) << 24);

    blockValue = Math.imul(blockValue, constant1);
    blockValue = (blockValue << 15) | (blockValue >>> 17);
    blockValue = Math.imul(blockValue, constant2);

    return blockValue;
}

function mixHash(currentHash, blockValue) {
    currentHash ^= blockValue;
    currentHash = (currentHash << 13) | (currentHash >>> 19);
    currentHash = Math.imul(currentHash, 5) + 0xe6546b64;
    return currentHash;
}

function processRemainingBytes(inputString, index, remainingBytes) {
    let blockValue = 0;

    if (remainingBytes >= 3)
        blockValue ^= inputString.charCodeAt(index + 2) << 16;

    if (remainingBytes >= 2)
        blockValue ^= inputString.charCodeAt(index + 1) << 8;

    if (remainingBytes >= 1) {
        blockValue ^= inputString.charCodeAt(index);
        blockValue = Math.imul(blockValue, constant1);
        blockValue = (blockValue << 15) | (blockValue >>> 17);
        blockValue = Math.imul(blockValue, constant2);
    }
    return blockValue;
}

function finalizeHash(currentHash, length) {
    currentHash ^= length;
    currentHash ^= currentHash >>> 16;
    currentHash = Math.imul(currentHash, 0x85ebca6b);
    currentHash ^= currentHash >>> 13;
    currentHash = Math.imul(currentHash, 0xc2b2ae35);
    currentHash ^= currentHash >>> 16;
    return currentHash >>> 0;
}