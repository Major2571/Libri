<?php
//Definindo o namespace
namespace App\Infraestrutura;

//Definindo as classes usadas
use App\Infraestrutura\Enum\Status\Status;
use App\Infraestrutura\Enum\Status\Http;
use App\Infraestrutura\Configuracao;
use App\Infraestrutura\Pagina;
use App\Util\Util;

/**
 * Classe de verificação de autenticação do usuário
 *
 * @final
 * @author Caroline Tacats <caroline.tacats62@gmail.com>
 * @package App\Infraestrutura
 */
final class Autenticacao
{

    /**
     * Constante das páginas que não precisam de autenticação
     *
     * @var array Array com as páginas não autenticadas
     */
    const PAGINAS_NAO_AUTENTICADAS = [
        'login',
        'recuperar-senha',
        'politica',
        'manutencao',
        '404',
        'sucesso',
        'cadastro',
    ];

    /**
     * Constante dos modelos que não precisam de autenticação
     * Geralmente chamado nas requisições via tratamento
     *
     * @var array Array com os modelos não autenticados
     */
    const MODELOS_NAO_AUTENTICADOS = [];

    /**
     * Constante das ações que não precisam de autenticação
     * Geralmente chamado nas requisições via tratamento
     *
     * @var array Array com as ações não autenticadas
     */
    const ACOES_NAO_AUTENTICADAS = [];

    /**
     * Constante das ações que precisam de autenticação
     * Geralmente chamado nas requisições via tratamento
     *
     * @var array Array com as ações autenticadas
     */
    const ACOES_AUTENTICADAS = [];

    /**
     * Constante dos modelos que precisam de autenticação
     * Geralmente chamado nas requisições via tratamento
     *
     * @var array Array com os modelos autenticados
     */
    const MODELOS_AUTENTICADOS = [
        'dashboard',
        'generos',
        'autores',
        'livros',
        'usuarios',
        'emprestimos'
    ];

    /**
     * Método construtor da classe
     *
     * @param Pagina $pagina Objeto da página
     * @param bool $autenticado Autenticado ou não. Usado para os métodos de requisição(tratamento)
     */
    public function __construct(Pagina $pagina, public bool $autenticado = false)
    {

        //Verificação especial para a ação "cadastrar" e com os modelos de cadastro
        if ($pagina->tipo === 'tratamento' && in_array($pagina->acao, self::ACOES_AUTENTICADAS) && in_array($pagina->modelo, self::MODELOS_AUTENTICADOS))
            //Verificando a autenticação
            $this->definirAutenticacao($pagina);
        //Verificação padrão para ações e modelos não autenticados em "tratamento"
        else if ($pagina->tipo === 'tratamento' && !in_array($pagina->acao, self::ACOES_NAO_AUTENTICADAS) && !in_array($pagina->modelo, self::MODELOS_NAO_AUTENTICADOS))
            //Verificando a autenticação
            $this->definirAutenticacao($pagina);
        //Verificação para páginas que precisam de autenticação
        else if ($pagina->tipo === 'pagina' && !in_array($pagina->diretorio, self::PAGINAS_NAO_AUTENTICADAS))
            //Verificando a autenticação
            $this->definirAutenticacao($pagina);

    }

    /**
     * Verificando a autenticação do usuário
     *
     * @param Pagina $pagina Objeto da página
     * @uses App\Util\Util::redirecionar() Redirecionando o usuário
     * @return void
     */
    private function definirAutenticacao($pagina): void
    {

        //Verificando
        if (!self::verificarAutenticacao()) {

            //Verificando se vem de um tratamento
            if ($pagina->tipo !== 'tratamento')
                //Redirecionando
                Util::redirecionar(Http::FORBIDDEN->codigo(), Configuracao::get('url.padrao'));
            else
                //Definindo
                $this->autenticado = false;

        } else {

            //Definindo se está autenticado
            $this->autenticado = true;

            if (
                $pagina->tipo === 'pagina'
            ) {
                Util::redirecionar(Http::FOUND->codigo(), Configuracao::get('url.padrao') . '/meu-perfil');
            } 

        }

    }

    /**
     * Deslogando o usuário
     *
     * @static
     * @uses App\Controlador\Token\Usuario\Token::remover() Removendo o token
     * @return void
     */
    public static function deslogar(): void
    {

        //Resetando todas as variáveis de sessão
        $_SESSION = [];
        
        //Removendo o cookie do usuário
        setcookie('usuario', '', time() - 3600, "/");

        //Destruindo a sessão
        session_destroy();

    }

    /**
     * Verificando se o usuario está autenticado
     *
     * @static
     * @return bool
     */
    public static function verificarAutenticacao(): bool
    {

        // Validação da sessão
        if (!isset($_SESSION) || !isset($_SESSION['usuario']['hash'], $_SESSION['usuario']['email'])) {
            return false;
        }

        // Validação do hash
        $agente = $_SERVER['HTTP_USER_AGENT'] ?? '';
        $hashEsperado = hash_hmac('sha256', session_id() . $_SESSION['usuario']['email'] . $agente, Configuracao::get('projeto.hash'));

        // Bloqueio imediato por hash inválido
        if (!hash_equals($_SESSION['usuario']['hash'], $hashEsperado)) {
            self::deslogar();
            return false;
        }

        // Revalida no banco para cobrir mudança de perfil após login/cookie
        try {

            // Obtendo o usuário autenticado
            $usuario = Fachada::instancia('Usuario')
                ->configurar(excecao: false, complemento: false)
                ->procurarPorStatusPorEmail(Status::ATIVO->value, $_SESSION['usuario']['email']);

            // Se o usuário não existir ou não estiver ativo, desloga imediatamente
            if (!$usuario || (int) $usuario->status !== Status::ATIVO->value) {
                self::deslogar();
                return false;
            }

        } catch (\Exception) {

            self::deslogar();
            return false;

        }

        return true;

    }

}