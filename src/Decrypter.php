<?php

namespace nostriphant\NIP44;

readonly class Decrypter {
    
    private ChaCha20 $chacha;
    private HMACAad $hmac;
    
    public function __construct(MessageKeys $keys, private string $salt) {
        list($chacha_key, $chacha_nonce, $hmac_key) = $keys($this->salt, 32, 12, 32);
    
        $this->chacha = new ChaCha20($chacha_key, $chacha_nonce);
        $this->hmac = new HMACAad(new Hash($hmac_key), $this->salt);
    }
    
    public function __invoke(string $decoded) : string {
        $ciphertext = substr($decoded, 33, -32);
        
        $hmac = substr($decoded, -32);
        $expected_hmac = ($this->hmac)($ciphertext);
        if ($hmac !== $expected_hmac) {
            throw new \InvalidArgumentException("unexpected ciphertext, unmatching hmac.");
        }

        return ($this->chacha)($ciphertext);
    }
}
