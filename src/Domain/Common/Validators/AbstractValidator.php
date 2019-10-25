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
    private $validator_interface = ValidatorInterface::class;

    /**
     * AbstractValidator constructor.
     * @param string $validator_interface Nome da inteface de validação que poderá ser executada
     * @param array $validators
     */
    public function __construct(string $validator_interface, array $validators)
    {
        $this->validator_interface = $validator_interface;
        $this->setValidators($validators);
    }

    /**
     * @return array
     */
    public function getValidators(): array
    {
        return $this->validators;
    }

    /**
     * @param array $validators
     * @return AbstractValidator
     */
    public function setValidators(array $validators): self
    {
        foreach ($validators as $nome_validator) {
            $this->addValidator($nome_validator);
        }

        return $this;
    }

    public function addValidator(string $nome_validator, int $prioridade = 0): self
    {
        $this->validators[$prioridade][] = $nome_validator;
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
            foreach ($lista_validators as $nome_validator) {
                /** @var ValidatorInterface $validator */
                $validator = !empty($construct) ? new $nome_validator(...$construct) : new $nome_validator;

                if (!$validator instanceof $this->validator_interface) {
                    unset($validator);
                    continue;
                }

                $validator->validar($params);
            }
        }

        return true;
    }
}