<?php

namespace nostriphant\NIP44;

use nostriphant\NIP01\Key;

readonly class Encrypt {

    private function __construct(private ConversationKey $conversation_key) {
        
    }

    static function make(Key $sender_key, string $hex_recipient_pubkey): self {
        return new self(new ConversationKey($sender_key, hex2bin($hex_recipient_pubkey)));
    }
    public function __invoke(string $message) : string {
        return Functions::encrypt($message, call_user_func($this->conversation_key), random_bytes(32));
    }
}
