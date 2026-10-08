<?php
namespace App\Controlador;

use App\Controlador\Principal\Controlador as ControladorPrincipal;
use App\Infraestrutura\Configuracao;
use App\Util\Util;
use App\Util\Validacao;

class Livro extends ControladorPrincipal
{
    public function inserir(array $campos, array $arquivo = []): int
    {
        $this->processarImagem($campos, $arquivo);
        return parent::inserir($campos);
    }

    public function editar(array $campos = [], array $arquivo = []): void
    {
        $this->processarImagem($campos, $arquivo);
        parent::editar($campos, $arquivo);
    }

    private function processarImagem(array &$campos, array $arquivo): void
    {
        if (!isset($arquivo['imagem']['name']) || $arquivo['imagem']['name'] === '') {
            return;
        }

        $nomeArquivo = Util::formatarNomeArquivo($arquivo['imagem']['name']);
        Validacao::verificarImagem($arquivo['imagem']);
        $caminhoArquivo = Configuracao::get('dir.repositorio') . "/livros/{$nomeArquivo}";
        move_uploaded_file($arquivo['imagem']['tmp_name'], $caminhoArquivo);
        $campos['imagem'] = $nomeArquivo;
    }
}
