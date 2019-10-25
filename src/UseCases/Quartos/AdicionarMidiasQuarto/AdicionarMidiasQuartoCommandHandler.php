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

namespace Reservas\UseCases\Quartos\AdicionarMidiasQuarto;


use Psr\Http\Message\UploadedFileInterface;
use Reservas\Domain\Quartos\Entities\Quarto;
use Reservas\Domain\Quartos\Repositories\QuartoRepositoryInterface;

class AdicionarMidiasQuartoCommandHandler
{
    const DIR_MIDIA = 'public/uploads/quartos/%d/';

    /**
     * @var QuartoRepositoryInterface
     */
    private $quarto_repository;

    /**
     * AdicionarMidiasQuartoCommandHandler constructor.
     * @param QuartoRepositoryInterface $quarto_repository
     */
    public function __construct(QuartoRepositoryInterface $quarto_repository)
    {
        $this->quarto_repository = $quarto_repository;
    }

    /**
     * @param AdicionarMidiasQuartoCommand $command
     * @return Quarto
     */
    public function handle(AdicionarMidiasQuartoCommand $command): Quarto
    {
        $quarto = $command->getQuarto();
        $midias = current($command->getMidias());

        if (count($midias) !== count($midias, COUNT_RECURSIVE)) {
            $midias = array_column($midias, 0);
        }

        $dir_midias = sprintf(self::DIR_MIDIA, $quarto->getId());

        if (!file_exists($dir_midias)) {
            $this->criarDiretorios($dir_midias);
        }

        /** @var UploadedFileInterface $midia */
        foreach ($midias as $midia) {
            $nome_arquivo =  basename($midia->getClientFilename());
            $arquivo_original =  "{$dir_midias}original/{$nome_arquivo}";

            $midia->moveTo($arquivo_original);
            $quarto->addMidia($arquivo_original);
        }

        return $quarto;
    }

    /**
     * Criar diretórios
     * @param string $dir_midias
     */
    public function criarDiretorios(string $dir_midias): void
    {
        mkdir($dir_midias, 0755, true);
        mkdir($dir_midias . 'original/', 0755, true);
        mkdir($dir_midias . 'mini/', 0755, true);
    }
}