<?php
//Definindo o namespace
namespace App\Infraestrutura;

/**
 * Classe de sessão.
 * Responsável por encapsular a sessão do PHP.
 *
 * @final
 * @author Caroline Tacats <caroline.tacats62@gmail.com>
 * @package App\Infraestrutura\Sessao
 */
final class Sessao
{

    /**
     * Iniciando a sessão.
     *
     * @static
     * @param string $nome Nome da sessão.
     * @param array $opcoes Opções [opcional]. Padrão: [].
     * @return void
     */
    public static function iniciar(string $nome = 'PHPSESSID', array $opcoes = []): void
    {

        //Verificando
        if (session_status() === PHP_SESSION_NONE) {

            //Definindo o nome
            session_name($nome);

            //Definindo as opções padrões
            $opcoesPadroes = [
                'cookie_lifetime' => 0,
                'cookie_path' => '/',
                'cookie_domain' => '',      // pode definir o seu domínio
                'cookie_secure' => true,    // exige HTTPS
                'cookie_httponly' => true,    // protege contra XSS
                'cookie_samesite' => 'Lax'
            ]; // ou 'Strict' se quiser mais restrição

            //Definindo as opções
            $opcoes = array_merge($opcoesPadroes, $opcoes);

            //Iniciando a sessão
            session_start($opcoes);

        }

    }

    /**
     * Obtendo o identificador da sessão.
     *
     * @static
     * @return string
     */
    public static function id(): string
    {

        //Retornando
        return session_id();

    }

    /**
     * Definindo um item para a sessão.
     *
     * @static
     * @param string $chave Chave.
     * @param mixed $valor Valor.
     * @return void
     */
    public static function definir(string $chave, mixed $valor): void
    {

        //Definindo
        $_SESSION[$chave] = $valor;

    }

    /**
     * Obtendo um item da sessão.
     *
     * @static
     * @param string $chave Chave.
     * @param mixed $padrao Valor padrão [opcional]. Padrão: null.
     * @return mixed
     */
    public static function obter(string $chave, mixed $padrao = null): mixed
    {

        //Definindo as partes
        $partes = explode('.', $chave);

        //Definindo o valor
        $valor = $_SESSION;

        //Listando
        foreach ($partes as $parte) {

            //Verificando
            if (!is_array($valor) || !array_key_exists($parte, $valor))
                //Retornando
                return $padrao;

            //Definindo
            $valor = $valor[$parte];

        }

        //Retornando
        return $valor;

    }

    /**
     * Tem um item na sessão com essa chave?
     *
     * @static
     * @param string $chave Chave.
     * @return bool
     */
    public static function tem(string $chave): bool
    {

        //Retornando
        return isset($_SESSION[$chave]);

    }

    /**
     * Verificando se um item da sessão está vazio.
     *
     * @static
     * @param string $chave Chave.
     * @return bool
     */
    public static function vazio(string $chave): bool
    {

        //Retornando
        return empty($_SESSION[$chave]);

    }

    /**
     * Removendo um item da sessão.
     *
     * @static
     * @param string $chave Chave.
     * @return void
     */
    public static function remover(string $chave): void
    {

        //Removendo
        unset($_SESSION[$chave]);

    }

    /**
     * Limpando a sessão.
     *
     * @static
     * @return void
     */
    public static function limpar(): void
    {

        //Limpando
        session_unset();

    }

    /**
     * Destruindo a sessão.
     *
     * @static
     * @return void
     */
    public static function destruir(): void
    {

        //Destruindo
        session_unset();
        session_destroy();

        //Verificando
        if (ini_get('session.use_cookies')) {

            //Definindo os parâmetros
            $parametros = session_get_cookie_params();

            //Definindo o cookie
            setcookie(
                session_name(),
                '',
                time() - 42000,
                $parametros['path'],
                $parametros['domain'],
                $parametros['secure'],
                $parametros['httponly']
            );

        }

    }

    /**
     * Regenerando o identificador da sessão.
     *
     * @static
     * @param bool $deletarAntiga Deletar a sessão antiga [opcional]. Padrão: true.
     * @return void
     */
    public static function regenerarId(bool $deletarAntiga = true): void
    {

        //Regenerando
        session_regenerate_id($deletarAntiga);

    }

    /**
     * Obtendo toda a sessão.
     *
     * @static
     * @return array<string, mixed>
     */
    public static function obterToda(): array
    {

        //Retornando
        return $_SESSION;

    }

    /**
     * Definindo mensagem temporária.
     *
     * @static
     * @param string $chave Chave.
     * @param string $texto Texto.
     * @return void
     */
    public static function mensagem(string $chave, string $texto): void
    {

        //Verificando
        if (!isset($_SESSION['__mensagem']))
            //Definindo
            $_SESSION['__mensagem'] = [];

        //Definindo
        $_SESSION['__mensagem'][$chave] = $texto;

    }

    /**
     * Obtendo a mensagem temporária.
     *
     * @static
     * @param string $chave Chave.
     * @return null|string
     */
    public static function obterMensagem(string $chave): ?string
    {

        //Verificando
        if (isset($_SESSION['__mensagem'][$chave])) {

            //Definindo a mensagem
            $mensagem = $_SESSION['__mensagem'][$chave];

            //Removendo a mensagem da sessão
            unset($_SESSION['__mensagem'][$chave]);

            //Retornando
            return $mensagem;

        }

        //Retornando
        return null;

    }

}