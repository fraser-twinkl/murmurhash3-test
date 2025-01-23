import murmurhash3 from './murmurhash3.mjs';

const { keys, seed } = JSON.parse(await new Response(process.stdin).text());
console.log(JSON.stringify(keys.map(key => murmurhash3(key, seed))));
