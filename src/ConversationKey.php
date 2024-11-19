<?php

namespace nostriphant\NIP44;
use nostriphant\NIP01\Key;

readonly class ConversationKey {
    private string $conversation_key;
    public function __construct(#[\SensitiveParameter] Key $recipient_key, string $sender_pubkey) {
        if (false === ($secret = $recipient_key(Key::sharedSecret('02' . bin2hex($sender_pubkey))))) {
            throw new \InvalidArgumentException('Can not find shared secret for given keys');
        }
        $this->conversation_key = (new Hash('nip44-v2'))(hex2bin($secret));
    }
    public function __invoke(): MessageKeys {
        return new MessageKeys($this->conversation_key);
    }
    public function __toString(): string {
        return $this->conversation_key;
    }
}
