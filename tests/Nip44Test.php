<?php

use nostriphant\NIP01\Key;
use nostriphant\NIP44\Functions;
use nostriphant\NIP44\Hash;
use nostriphant\NIP44\MessageKeys;
use nostriphant\NIP44\ConversationKey;
use function \Pest\vectors;

describe('NIP-44 v2', function () {

    describe('valid', function () {
        it('is sane', function () {
            $key = hex2bin(
                    '000102030405060708090a0b0c0d0e0f' .
                    '101112131415161718191a1b1c1d1e1f' .
                    '202122232425262728292a2b2c2d2e2f' .
                    '303132333435363738393a3b3c3d3e3f' .
                    '404142434445464748494a4b4c4d4e4f'
            );
            $salt = hex2bin(
                    '606162636465666768696a6b6c6d6e6f' .
                    '707172737475767778797a7b7c7d7e7f' .
                    '808182838485868788898a8b8c8d8e8f' .
                    '909192939495969798999a9b9c9d9e9f' .
                    'a0a1a2a3a4a5a6a7a8a9aaabacadaeaf'
            );
            expect(bin2hex((new Hash($salt))($key)))->toBe('06a6b88c5853361a06104c9ceb35b45cef760014904671014a193f40c15fc244');
        });

        it('get_conversation_key', function ($vector) {
            //https://github.com/paulmillr/nip44/blob/main/javascript/test/nip44.vectors.json
            $privkey = Key::fromHex($vector->sec1);
            $key = new ConversationKey($privkey, hex2bin($vector->pub2));
            expect('' . $key)->not()->toBeFalse();
            expect(bin2hex($key))->toBe($vector->conversation_key, $vector->note ?? '');
        })->with(vectors('nip44')->v2->valid->get_conversation_key);

        $message_keys = vectors('nip44')->v2->valid->get_message_keys;
        $conversation_key = hex2bin($message_keys->conversation_key);
        expect($conversation_key)->not()->toBeEmpty();
        $message_key_maker = new MessageKeys($conversation_key);
        it('gets message keys', function ($vector) use ($message_key_maker) {
            $expected = [
                $vector->chacha_key,
                $vector->chacha_nonce,
                $vector->hmac_key
            ];
            foreach ($message_key_maker(hex2bin($vector->nonce), 32, 12, 32) as $key) {
                expect(bin2hex($key))->toBe(array_shift($expected));
            }
        })->with(vectors('nip44')->v2->valid->get_message_keys->keys);

        it('can encrypt & decrypt', function ($vector) {
            $key = Key::fromHex($vector->sec2);
            $pub2 = $key(Key::public());

            $privkey = Key::fromHex($vector->sec1);
            $conversation_key = new ConversationKey($privkey, hex2bin($pub2));
            expect(bin2hex('' . $conversation_key))->toBe($vector->conversation_key);
            $keys = $conversation_key();
            
            expect(Functions::decrypt($vector->payload, $keys))->toBe($vector->plaintext, 'Unable to properly decrypt vector payload');

            $payload = Functions::encrypt($vector->plaintext, $keys, hex2bin($vector->nonce));
            expect(Functions::decrypt($payload, $keys))->toBe($vector->plaintext, 'Unable to properly decrypt self encrypted payload');

            expect($payload)->toBe($vector->payload, 'Unable to properly encrypt vector message');
        })->with(vectors('nip44')->v2->valid->encrypt_decrypt);

        it('can encrypt & decrypt long messages', function ($vector) {
            $plaintext = str_repeat($vector->pattern, $vector->repeat);
            expect(hash('sha256', $plaintext))->toBe($vector->plaintext_sha256);
            $keys = new MessageKeys(hex2bin($vector->conversation_key));

            $payload = Functions::encrypt($plaintext, $keys, hex2bin($vector->nonce));
            expect(hash('sha256', $payload))->toBe($vector->payload_sha256, 'Unable to properly encrypt long text');

            expect(Functions::decrypt($payload, $keys))->toBe($plaintext, 'Unable to properly decrypt self encrypted payload');
        })->with(vectors('nip44')->v2->valid->encrypt_decrypt_long_msg);
    });
    
    
  describe('invalid', function() {
    it('encrypt_msg_lengths', function ($vector) {
            expect(fn() => Functions::encrypt(str_repeat('a', $vector), new MessageKeys(random_bytes(32)), ''))->toThrow(\InvalidArgumentException::class, message: $vector);
        })->with(vectors('nip44')->v2->invalid->encrypt_msg_lengths);

        it('decrypt', function ($vector) {
            expect(fn() => Functions::decrypt($vector->payload, new MessageKeys(hex2bin($vector->conversation_key))))->toThrow(\InvalidArgumentException::class, message: $vector->note);
        })->with(vectors('nip44')->v2->invalid->decrypt);

        it('get_conversation_key', function ($vector) {
            $privkey = Key::fromHex($vector->sec1);
            expect(fn() => $privkey(Key::sharedSecret($vector->pub2)))->toThrow(\InvalidArgumentException::class, message: $vector->note);
        })->with(vectors('nip44')->v2->invalid->get_conversation_key);
    });

    it('shared_secret', function () {
        $public_key_bech32 = 'npub1efz8l77esdtpw6l359sjvakm7azvyv6mkuxphjdk3vfzkgxkatrqlpf9s4';
        $public_key_hex = 'ca447ffbd98356176bf1a1612676dbf744c2335bb70c1bc9b68b122b20d6eac6';
        $private_key = Key::fromHex('56350645f60b55570901937c9196e618a5b87a2b64968b09c0b404efef74d2b5');

        $key = new ConversationKey($private_key, hex2bin($public_key_hex));
        expect('' . $key)->not()->toBeEmpty();
    });
});

