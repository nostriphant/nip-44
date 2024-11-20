# nip44
Nostr NIP-44 implementation in PHP


# Usage
```
use nostriphant\NIP01\Key;
use nostriphant\NIP44\Encrypt;
use nostriphant\NIP44\Decrypt;

$recipient_key = Key::fromHex('4b22aa260e4acb7021e32f38a6cdf4b673c6a277755bfce287e370c924dc936d');
$recipient_pubkey = $recipient_key(Key::public());

$sender_key = Key::fromHex('5c0c523f52a5b6fad39ed2403092df8cebc36318b39383bca6c00808626fab3a');
$sender_pubkey = $sender_key(Key::public());

$payload = Encrypt::make($sender_key, $recipient_pubkey)('Hello World!');

assert('Hello World!' === Decrypt::make($recipient_key, $sender_pubkey)($payload));
```
