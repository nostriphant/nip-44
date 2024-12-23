<?php

namespace nostriphant\NIP44;
use nostriphant\NIP44\MessageKeys;

readonly class Decrypter {
    
    private ChaCha20 $chacha;
    private HMACAad $hmac;
    
    public function __construct(MessageKeys $keys, private string $salt) {
        list($chacha_key, $chacha_nonce, $hmac_key) = iterator_to_array($keys($this->salt, 32, 12, 32));
    
        $this->chacha = new ChaCha20($chacha_key, $chacha_nonce);
        $this->hmac = new HMACAad(new Hash($hmac_key), $this->salt);
    }
    
    public function __invoke(string $decoded) : string {
        $ciphertext = substr($decoded, 33, -32);
        
        if (substr($decoded, -32) !== ($this->hmac)($ciphertext)) {
            throw new \InvalidArgumentException("Unexpected ciphertext, unmatching hmac");
        }

        return ($this->chacha)($ciphertext);
    }
}
