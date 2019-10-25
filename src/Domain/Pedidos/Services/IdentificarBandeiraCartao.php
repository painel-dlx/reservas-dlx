<?php
/**
 * MIT License
 *
 * Copyright (c) 2018 PHP DLX
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in all
 * copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NON INFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE
 * SOFTWARE.
 */

namespace Reservas\Domain\Pedidos\Services;


class IdentificarBandeiraCartao
{
    private $bandeiras = [
        'amex' => [
            'nome' => 'Amex',
            'bin' => '/^(34|37)/',
            'max' => 15,
            'cvc' => [3, 4]
        ],

        'aura' => [
            'nome' => 'Aura',
            'bin' => '/^50[0-9]/',
            'max' => 16,
            'cvc' => 3
        ],

        'diners' => [
            'nome' => 'Diners',
            'bin' => '/^3(?:0[0-5]|[68][0-9])/',
            'max' => [14, 16],
            'cvc' => 3
        ],

        'discover' => [
            'nome' => 'Discover',
            'bin' => '/^6(?:011|5[0-9]{2})/',
            'max' => 16,
            'cvc' => [3, 4]
        ],

        'elo' => [
            'nome' => 'ELO',
            'bin' => '/^401178|^401179|^431274|^438935|^451416|^457393|^457631|^457632|^504175|^627780|^636297|^636368|^(506699|5067[0-6]\d|50677[0-8])|^(50900\d|5090[1-9]\d|509[1-9]\d{2})|^65003[1-3]|^(65003[5-9]|65004\d|65005[0-1])|^(65040[5-9]|6504[1-3]\d)|^(65048[5-9]|65049\d|6505[0-2]\d|65053[0-8])|^(65054[1-9]|6505[5-8]\d|65059[0-8])|^(65070\d|65071[0-8])|^65072[0-7]|^(65090[1-9]|65091\d|650920)|^(65165[2-9]|6516[6-7]\d)|^(65500\d|65501\d)|^(65502[1-9]|6550[3-4]\d|65505[0-8])/',
            'max' => 16,
            'cvc' => 3
        ],

        'hipercard' => [
            'nome' => 'Hipercard',
            'bin' => '/^(3841[046]0|60)/',
            'max' => [13, 16, 19],
            'cvc' => 3
        ],

        'mastercard' => [
            'nome' => 'MasterCard',
            'bin' => '/^5[1-5][0-9]{2}/',
            'max' => 16,
            'cvc' => 3
        ],

        'visa' => [
            'nome' => 'Visa',
            'bin' => '/^4/',
            'max' => [13, 16],
            'cvc' => 3
        ]
    ];

    /**
     * Identificar a bandeira de um cartão de crédito
     * @param string $numero_cartao
     * @return string|null
     */
    public function executar(string $numero_cartao): ?string
    {
        $numero_cartao = preg_replace('~[^0-9]~', '', $numero_cartao);

        foreach ($this->bandeiras as $bandeira) {
            $max = is_array($bandeira['max']) ? $bandeira['max'] : [$bandeira['max']];

            if (preg_match($bandeira['bin'], $numero_cartao) === 1 && in_array(strlen($numero_cartao), $max)) {
                return $bandeira['nome'];
            }
        }

        return null;
    }
}