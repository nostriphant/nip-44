<?php

use nostriphant\NIP44\Padding;
use function Pest\vectors_nip44;

it('calcs padded length correctly', function (int $unpadded_length, int $expected_length) {
    expect(Padding::calculateLength($unpadded_length))->toBe($expected_length);
})->with(vectors_nip44()->v2->valid->calc_padded_len);
