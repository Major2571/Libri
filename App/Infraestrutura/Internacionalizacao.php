<?php
//Definindo o namespace
namespace App\Infraestrutura;

//Definindo as classes usadas
use App\Excecao\Conteudo as ConteudoException;
use App\Infraestrutura\Enum\Mensagem\Conteudo;
use App\Infraestrutura\Configuracao;
use ipinfo\ipinfo\IPinfoLite;
use App\Infraestrutura\Base;
use const LC_MESSAGES;
use App\Util\Util;

/**
 * Classe de internacionalização
 * Herdada da classe base para auxílio nas chamadas
 *
 * @final
 * @author Caroline Tacats <caroline.tacats62@gmail.com>
 * @package App\Infraestutura
 */
final class Internacionalizacao extends Base{

    /**
     * Definindo as línguas pré-definidas
     *
     * @access private
     */
    private const LINGUAS   = [

        'pt_BR' => [ 'ptBR', 'pt_BR', 'pt_BR.utf-8', 'portuguese' ],
        'pt_PT' => [ 'ptPT', 'pt_PT', 'pt_PT.utf-8', 'portuguese' ],
        'en_US' => [ 'en_US', 'en_US.utf-8', 'english' ],
        'es_ES' => [ 'es_ES', 'es_ES.utf-8', 'spanish' ] ];

    /**
     * Definindo os domínios gerais, carregados em todas as páginas
     *
     * @access private
     */
    private const DOMINIOS_GERAIS   = [
        
        'carrinho',
        'formulario',
        'rodape',
        'geral',
        'menu' ];

    /**
     * Definindo os países pré-definidos
     *
     * @access private
     */
    private const PAISES    = [
        'BR' => 'pt_BR',
        'US' => 'en_US',
        'ES' => 'es_ES',
        'PT' => 'pt_PT' ];

    /**
     * Método construtor da classe
     *
     * @param string $lingua língua passada para tradução
     * @throws ConteudoException
     */
    public function __construct( private string $lingua ){

        //Verificando a língua passada
        if( !array_key_exists( $lingua, self::LINGUAS ) )
            //Lançando a exceção
            throw new ConteudoException( sprintf( Conteudo::TRADUCAO_NAO_DISPONIVEL->texto(), $this->lingua ) );

        //Definindo a localidade para o horário/data
        setlocale( LC_TIME, 'ptBR', 'pt_BR', 'pt_BR.utf-8', 'portuguese' );

        //Definindo o timezone
        date_default_timezone_set( 'America/Recife' );

        //Verificando se a constante está definida(no windows, não existe)
        if( !defined( 'LC_MESSAGES' ) )
            //Definindo a constante
            define( 'LC_MESSAGES', 6 );

        //Definindo a localidade para as mensagens
        setlocale( LC_MESSAGES, $lingua );

        //Iniciando os domínios
        if( !empty( self::DOMINIOS_GERAIS ) )
            //Iniciando os domínios
            self::dominio( SELF::DOMINIOS_GERAIS );

        //Verificando se tem a opção de busca do IP via IPInfo
        if( Configuracao::get( 'projeto.ipinfo' ) )
            //Definindo a localização
            self::verificarPais();

    }

    /**
     * Definindo o domínio para internacionalização
     *
     * @static
     * @param mixed $dominios Domínio(s) para definição
	 * @uses App\Infraestrutura\Configuracao::get() Retornando a variável do projeto
     * @return void
     */
    public static function dominio( mixed $dominios ): void{

        //Listando o(s) domínio(s)
        foreach( (array) $dominios as $dominio ){

            //Verificando se o domínio existe e foi passado
            if( !is_null( $dominio ) ){

                //Definindo o domínio
                bindtextdomain( $dominio, Configuracao::get( 'dir.traducao' ) );

                //Definindo o codeset do domínio
                bind_textdomain_codeset( $dominio, 'UTF-8' );

            }

        }

    }

    /**
     * Traduzindo a mensagem para o idioma definido
     *
     * @static
     * @param string $mensagem Mensagem para tradução
     * @param string $dominio Domínio para tradução
	 * @uses App\Infraestrutura\Configuracao::get() Retornando a variável do projeto
     * @return string
     */
    public static function traduzir( string $mensagem, string $dominio = 'mensagem-de-erro' ): string{

        //Definindo o domínio
        self::dominio( $dominio );

        //Definindo o domínio para tradução
        textdomain( $dominio );

        //Retornando o conteúdo traduzido
        return _( $mensagem );

    }

    /**
     * Verificando o país ao acessar o site pela primeira vez
     *
     * @static
	 * @uses App\Infraestrutura\Configuracao::get() Retornando a variável do projeto
     * @return void
     */
    public static function verificarPais(): void{

        //Verificando a existência de uma localização na sessão
        if( !isset( $_SESSION[ 'ipinfo' ] ) ){

            //Definindo o cliente
            $cliente                = new IPinfoLite( Configuracao::get( 'api.ipinfo.token' ) );

            //Definindo o objeto
            $objeto    = $cliente->getDetails( Util::definirIP() );

            //Definindo a língua da tradução
            $_SESSION[ 'lingua' ]   = array_key_exists( $objeto->country_code, self::PAISES ) ? self::PAISES[ $objeto->country_code ] : 'pt_BR';

            //Definindo o país na sessão
            $_SESSION[ 'ipinfo' ]   = true;

        }

    }

}