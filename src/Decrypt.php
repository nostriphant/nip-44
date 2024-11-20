<?php

namespace nostriphant\NIP44;

use nostriphant\NIP01\Key;

readonly class Decrypt {

    private function __construct(private ConversationKey $conversation_key) {

    }

    static function make(Key $recipient_key, string $hex_sender_pubkey): self {
        return new self(new ConversationKey($recipient_key, hex2bin($hex_sender_pubkey)));
    }

    public function __invoke(string $message) : string {
        return Functions::decrypt($message, call_user_func($this->conversation_key));
    }
}
