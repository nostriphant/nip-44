<?php

namespace nostriphant\NIP44;

readonly class MessageKeys {
    
    
    public function __construct(#[\SensitiveParameter] private string $conversation_key) {
    }

    /**
     * Based on https://github.com/mgp25/libsignal-php/blob/master/src/kdf/HKDF.php
     * @param string $prk
     * @param string $info
     * @param int $length
     * @return string
     * 
     */
    private function hkdf_expand(string $info, int $length): string {
        $iterations = (int) ceil($length / Hash::OUTPUT_SIZE);
        $stepResult = '';
        $result = '';
        for ($i = 0; $i < $iterations; $i++) {
            $stepResult = (string)(new Hash($this->conversation_key))($stepResult)($info)(chr(($i + 1) % 256));
            $stepSize = min($length, strlen($stepResult));
            $result .= substr($stepResult, 0, $stepSize);
            $length -= $stepSize;
        }
        return $result;
    }
    
    public function __invoke(string $salt, int ...$lengths) : array {
        $keys_raw = $this->hkdf_expand($salt, array_sum($lengths));
        $offset = 0;
        $keys = [];
        foreach ($lengths as $length) {
            $keys[] = substr($keys_raw, $offset, $length);
            $offset += $length;
        }
        return $keys;
    }
    
}
