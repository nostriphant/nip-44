# nip44
Nostr NIP-44 implementation in PHP


# Usage
```
use nostriphant\NIP01\Key;
use nostriphant\NIP44\Functions;
use nostriphant\NIP44\ConversationKey;

$recipient_key = Key::fromHex('4b22aa260e4acb7021e32f38a6cdf4b673c6a277755bfce287e370c924dc936d');
$recipient_pubkey = $recipient_key(Key::public());

$sender_key = Key::fromHex('5c0c523f52a5b6fad39ed2403092df8cebc36318b39383bca6c00808626fab3a);

$conversation_key_sent = new ConversationKey($sender_key, hex2bin($recipient_pubkey));
$nonce = random_bytes(32);

$payload = Functions::encrypt('Hello World!', $conversation_key_sent(), $nonce);

$conversation_key_receive = new ConversationKey($recipient_key, hex2bin($sender_key(Key::public())));
assert('Hello World!' === Functions::decrypt($payload, $conversation_key_receive()));
```
