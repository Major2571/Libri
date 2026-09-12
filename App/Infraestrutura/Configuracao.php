<?php
//Definindo o namespace
namespace App\Infraestrutura;

//Definindo as classes usadas
use App\Infraestrutura\Pagina;
use App\Infraestrutura\Base;
use App\Infraestrutura\Sessao;
use App\Util\Util;
use Dotenv\Dotenv;

/**
 * Configurações do projeto
 * Herdada da classe base para auxílio nas chamadas
 *
 * @final
 * @author Caroline Tacats <caroline.tacats62@gmail.com>
 * @package App\Infraestrutura
 */
final class Configuracao extends Base
{

    /**
     * Configurações
     *
     * @access private
     * @var array Array de configurações
     */
    private static array $configuracoes = [];

    public function __construct(private Pagina $pagina)
    {
    }

    /**
     * Carregando as configurações
     *
     * @static
     * @uses Dotenv\Dotenv::createImmutable() Criando a instância do .env
     * @return void
     */
    public static function carregar(): void
    {

        //Iniciando a sessão

        //Iniciando a sessão
        Sessao::iniciar('SESPLAZAMAISID');

        //Definindo o fuso horário
        date_default_timezone_set('America/Recife');

        //Definindo a localidade
        setlocale(LC_TIME, 'ptBR', 'pt_BR', 'pt_BR.utf-8', 'portuguese');

        //Definindo as variáveis de ambiente
        Dotenv::createImmutable('../')->load();

        //Definindo as principais variáveis
        $dir = $_ENV['DIR'] ?? null;
        $dirRepositorio = $_ENV['DIR_REPOSITORIO'] ?? null;
        $url = $_ENV['URL'] ?? null;

        //Definindo as configurações
        self::$configuracoes = [

            //Projeto
            'projeto' => [

                //Em produção ou não
                'producao' => (bool) $_ENV['PRODUCAO'] ?? false,
                //Ambiente fechado ou não
                'fechado' => (bool) $_ENV['AMBIENTE_FECHADO'] ?? false,
                //Nome do projeto
                'nome' => $_ENV['NOME_PROJETO'] ?? null,
                //HASH do projeto
                'hash' => $_ENV['HASH'] ?? null,
                //Referência do projeto
                'referencia' => $_ENV['REFERENCIA'] ?? '',
                //Registros por página
                'quantidade_paginacao' => 12,
            ],

            //Debug
            'debug' => [

                //Exibir ou não o debug
                'exibir' => (bool) $_ENV['DEBUG'] ?? false,
                //Exibir ou não o trace
                'trace' => (bool) $_ENV['DEBUG_EXIBIR_TRACE'] ?? false
            ],

            //Diretórios
            'dir' => [

                //Diretório padrão
                'padrao' => $dir,
                //Diretório de repositório
                'repositorio' => $dirRepositorio,
                //Diretório de include
                'include' => "{$dir}/include",
                //Diretório de requisição
                'requisicao' => "{$dir}/submit",
                //Diretório do PHP
                'php' => "{$dir}/php",
                //Diretório do CSS
                'css' => "{$dir}/css",
                //Diretório do HTML
                'html' => "{$dir}/html",
                //Diretório do JS
                'js' => "{$dir}/js"
            ],

            //URLs
            'url' => [

                //URL padrão
                'padrao' => $url,
                'vendor' => "{$url}/assets/vendor",
                //URL do CSS
                'css' => "{$url}/assets/css",
                'css_vendor' => "{$url}/assets/vendor/css",
                //URL do JS
                'js' => "{$url}/assets/js",
                'js_vendor' => "{$url}/assets/vendor/js",
                //URL de imagens
                'imagem' => "{$url}/assets/img",
                //Repositório
                'repositorio' => $_ENV['URL_REPOSITORIO'],
            ],

            //Banco de dados
            'banco' => [

                //Endereço
                'endereco' => $_ENV['BD_ENDERECO'] ?? 'localhost',
                //Base
                'base' => $_ENV['BD_BASE'] ?? null,
                //Porta
                'porta' => $_ENV['BD_PORTA'] ?? 3306,
                //Usuário
                'usuario' => $_ENV['BD_USUARIO'] ?? 'root',
                //Senha
                'senha' => $_ENV['BD_SENHA'] ?? null
            ]

        ];

    }

    /**
     * Retornando o valor da configuração escolhida
     *
     * @static
     * @param string $chave Chave
     * @param mixed $padrao Valor padrão
     * @return mixed
     */
    public static function get(string $chave, $padrao = null): mixed
    {

        //Separando o valor da chave
        $chaves = explode('.', $chave);

        //Definindo as configurações
        $configuracoes = self::$configuracoes;

        //Listando as chaves
        foreach ($chaves as $chave) {

            //Verificando se existe aquela configuração
            if (!is_array($configuracoes) || !array_key_exists($chave, $configuracoes))
                //Retornando o valor padrão passado
                return $padrao;

            //Definindo a configuração
            $configuracoes = $configuracoes[$chave];

        }

        //Retornando a configuração
        return $configuracoes;

    }

}