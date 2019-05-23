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

namespace Reservas\Domain\Common\Validators;


abstract class AbstractValidator
{
    /**
     * @var array
     */
    private $validators = [];
    /**
     * @var string
     */
    private $validatorInterface = ValidatorInterface::class;

    /**
     * AbstractValidator constructor.
     * @param string $validatorInterface Nome da inteface de validação que poderá ser executada
     * @param array $validators
     */
    public function __construct(string $validatorInterface, array $validators)
    {
        $this->validatorInterface = $validatorInterface;

        foreach ($validators as $nomeValidator) {
            $this->addValidator($nomeValidator);
        }
    }

    /**
     * @return array
     */
    public function getValidators(): array
    {
        return $this->validators;
    }

    public function addValidator(string $nomeValidator, int $prioridade = 0): self
    {
        $this->validators[$prioridade][] = $nomeValidator;
        return $this;
    }

    /**
     * Executar todos os validadores
     * @param mixed|null $params
     * @param mixed|null $construct
     * @return bool
     */
    public function validar($params = null, ... $construct): bool
    {
        foreach ($this->validators as $lista_validators) {
            foreach ($lista_validators as $nomeValidator) {
                /** @var ValidatorInterface $validator */
                $validator = !empty($construct) ? new $nomeValidator(...$construct) : new $nomeValidator;

                if (!$validator instanceof $this->validatorInterface) {
                    unset($validator);
                    continue;
                }

                $validator->validar($params);
            }
        }

        return true;
    }
}