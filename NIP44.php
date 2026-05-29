<?php

namespace nostriphant;

class NIP44 {
    
    static function encrypt(NIP01\Key $sender_key, string $hex_recipient_pubkey, string $message) : string {
        return \nostriphant\NIP44\Encrypt::make($sender_key, $hex_recipient_pubkey)($message);
    }
    
    static function decrypt(NIP01\Key $recipient_key, string $hex_sender_pubkey, string $message) : string {
        return \nostriphant\NIP44\Decrypt::make($recipient_key, $hex_sender_pubkey)($message);
    }
    
}
