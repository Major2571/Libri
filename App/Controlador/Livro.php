<?php
namespace App\Controlador;

use App\Controlador\Principal\Controlador as ControladorPrincipal;
use App\Infraestrutura\Configuracao;
use App\Util\Util;
use App\Util\Validacao;

class Livro extends ControladorPrincipal
{
    // Definindo os campos relacionados
    protected const CAMPOS_RELACIONADOS = [
        'GenerosRelacionados' => 'generos',
        'AutoresRelacionados' => 'autores',
    ];

    /**
     * Obetendo os complementos dos modelos
     *
     * @param mixed $conteudos Conteúdos
     * @uses ControladorPrincipal::aplicarComplemento() Para aplicar os complementos
     * @return mixed
     */
    public function complemento(mixed $conteudos): mixed
    {
        return parent::aplicarComplemento($conteudos, self::CAMPOS_RELACIONADOS);
    }

    public function obterDadosConteudo(object $conteudo, ?array $campos = []): void
    {

        // Listando os campos
        foreach ($campos as $nomeControlador => $campoBanco) {

            // Definindo o controlador
            $propControlador = 'controlador' . $nomeControlador;

            // Verificando a existência do controlador para o conteúdo
            if (!property_exists($conteudo, $propControlador) || is_null($conteudo->{$propControlador})) {
                continue;
            }

            // Definindo o controlador
            $controlador = $conteudo->{$propControlador};

            // Verificando o nome do controlador para obter os dados do conteúdo
            if ($nomeControlador == 'GenerosRelacionados') {

                $conteudo->{$campoBanco} = $controlador
                    ->configurar(excecao: false, complemento: false)
                    ->livro($conteudo->id)
                    ->listarGenerosPorLivro();

                continue;

            }

            // Verificando o nome do controlador para obter os dados do conteúdo
            if ($nomeControlador == 'AutoresRelacionados') {

                $conteudo->{$campoBanco} = $controlador
                    ->configurar(excecao: false, complemento: false)
                    ->livro($conteudo->id)
                    ->listarAutoresPorLivro();

                continue;

            }


        }

    }

    public function inserir(array $campos, array $arquivo = []): int
    {

        $this->processarImagem($campos, $arquivo);

        $campos['ano_publicacao'] = Util::formatarData($campos['ano_publicacao'], 'Y-m-d');

        //Definindo o modelo
        $modelo = new $this->modelo($campos);

        //Inserindo e definindo o identificador
        $id = $this->repositorio->inserir($modelo);

        if ($campos['generos']) {
            $this->relacionarComGenero(
                livro: $id,
                generos: $campos['generos'] ?? []
            );
        }

        if ($campos['autores']) {
            $this->relacionarComAutores(
                livro: $id,
                autores: $campos['autores'] ?? []
            );
        }

        //Retornando
        return $id;

    }

    public function editar(array $campos = [], array $arquivo = []): void
    {

        $campos['ano_publicacao'] = (int) $campos['ano_publicacao'];

        $this->processarImagem($campos, $arquivo);

        if ($campos['generos']) {
            $this->relacionarComGenero(
                livro: $campos['id'],
                generos: $campos['generos'] ?? []
            );
        }

        if ($campos['autores']) {
            $this->relacionarComAutores(
                livro: $campos['id'],
                autores: $campos['autores'] ?? []
            );
        }
        
        parent::editar($campos, $arquivo);

    }


    public function relacionarComGenero(int $livro, array $generos): void
    {

        //Removendo o relacionamento
        $this->repositorio->removerRelacionamento($livro, 'livro_genero', 'livro');

        //Listando
        foreach ($generos as $genero)
            //Relacionando
            $this->repositorio->relacionar($livro, $genero, 'livro_genero', 'livro', 'genero');

    }

    public function relacionarComAutores(int $livro, array $autores): void
    {

        //Removendo o relacionamento
        $this->repositorio->removerRelacionamento($livro, 'livro_autor', 'livro');

        //Listando
        foreach ($autores as $autor)
            //Relacionando
            $this->repositorio->relacionar($livro, $autor, 'livro_autor', 'livro', 'autor');

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
