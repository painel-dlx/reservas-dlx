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

namespace Reservas\UseCases\Quartos\CriarNovoQuarto;


use Reservas\Domain\Quartos\Entities\Quarto;
use Reservas\Domain\Quartos\Repositories\QuartoRepositoryInterface;
use Reservas\Domain\Quartos\Validators\QuartoValidator;
use Reservas\Domain\Quartos\Validators\QuartoValidatorEnum;
use SechianeX\Contracts\SessionInterface;

/**
 * Class CriarNovoQuartoCommandHandler
 * @package Reservas\UseCases\Quartos\CriarNovoQuarto
 * @covers CriarNovoQuartoCommandHandlerTest
 */
class CriarNovoQuartoCommandHandler
{
    /**
     * @var QuartoRepositoryInterface
     */
    private $quarto_repository;
    /**
     * @var QuartoValidator
     */
    private $validator;
    /**
     * @var SessionInterface
     */
    private $session;

    /**
     * CriarNovoQuartoCommandHandler constructor.
     * @param QuartoRepositoryInterface $quarto_repository
     * @param SessionInterface $session
     * @param QuartoValidator $validator
     */
    public function __construct(
        QuartoRepositoryInterface $quarto_repository,
        SessionInterface $session,
        QuartoValidator $validator
    ) {
        $this->quarto_repository = $quarto_repository;
        $this->session = $session;
        $this->validator = $validator;
    }

    /**
     * @param CriarNovoQuartoCommand $command
     * @return Quarto
     */
    public function handle(CriarNovoQuartoCommand $command): Quarto
    {
        /** @var Quarto $quarto */
        $quarto = $this->session->get('editando:quarto');

        $quarto->setNome($command->getNome());
        $quarto->setQtde($command->getQtde());
        $quarto->setValorMin($command->getValorMin());
        $quarto->setDescricao($command->getDescricao());
        $quarto->setMaxHospedes($command->getMaxHospedes());
        $quarto->setTamanhoM2($command->getTamanhoM2());
        $quarto->setLink($command->getLink());

        $this->validator->setValidators(QuartoValidatorEnum::CADASTRAR);
        $this->validator->validar($quarto, $this->quarto_repository);

        $this->quarto_repository->create($quarto);

        return $quarto;
    }
}