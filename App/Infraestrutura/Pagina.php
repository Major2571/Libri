<?php
//Definindo o namespace
namespace App\Infraestrutura;

//Definindo as classes usadas
use App\Infraestrutura\Internacionalizacao;
use App\Infraestrutura\Configuracao;
use App\Infraestrutura\Autenticacao;
use App\Infraestrutura\Metadata;
use App\Infraestrutura\Fabrica;
use App\Infraestrutura\Base;
use App\Util\Util;

/**
 * Classe da exibição das páginas
 * Herdada da classe base para auxílio nas chamadas
 *
 * @final
 * @author Caroline Tacats <caroline.tacats62@gmail.com>
 * @package App\Infraestrutura
 */
final class Pagina extends Base{

	/**
	 * Definindo as constantes das propriedades vindo da query string
	 *
	 * @access private
	 * @var array Array com as propriedades
	 */
	private const PROPRIEDADES	= [

		'id',
		'slug',
		'pagina',
		'modelo',
		'diretorio',
		'tipo-conteudo',
		'diretorio-tratamento' ];

	/**
	 * Definindo as constantes das páginas com interna
	 *
	 * @access private
	 * @var array Array com as páginas
	 */
	public const PAGINAS_COM_INTERNA	= [
		'eventos',
		'meus-beneficios'
	];

	/**
	 * Método construtor da classe
	 *
	 * @param array $queryString Query string
	 * @uses App\Infraestrutura\Configuracao::carregar() Carregando as configurações
	 * @uses App\Infraestrutura\Configuracao::get() Retornando a variável do projeto
	 */
	public function __construct( array $queryString ){

		//Carregando as configurações
		Configuracao::carregar();

		//Verificando se é produção para mostrar os erros ou não
		if( Configuracao::get( 'projeto.producao' ) )
			//Escondendo os erros em caso de produção
			error_reporting( 0 );

		//Listando as propriedades da página
		foreach( self::PROPRIEDADES as $propriedade )
			//Verificando
			if( array_key_exists( $propriedade, $queryString ) )
				//Definindo as propriedades
				$this->$propriedade	= $this->formatarQueryString( $queryString, $propriedade );

		//Definindo a ação
		$this->acao	= $queryString[ 'acao' ] ?? 'index';
		$this->tipo	= $queryString[ 'tipo' ] ?? 'pagina';

		//Verificando se o projeto necessita de autenticação
		if( Configuracao::get( 'projeto.fechado' ) )
			//Verificando a autenticação
			$this->autenticado	= ( new Autenticacao( $this ) )->autenticado;

		//Verificando se o projeto necessita de internacionalização
		if( Configuracao::get( 'projeto.internacionalizacao' ) )
			//Iniciando a internacionalização
			new Internacionalizacao( $_SESSION[ 'lingua' ] ?? 'pt_BR' );

		//Definindo o caminho do arquivo para exibição
		$this->definirCaminho();

		//Definindo as configurações
		$this->configuracao();

	}

	/**
	 * Definindo o cache da página caso exista
	 *
	 * @access private
	 * @uses App\Util\Util::definirCache() Definindo o cache do arquivo
	 * @return void
	 */
	private function cache(): void{

		//Verificando
		if( $this->tipo !== 'tratamento' )
			//Definindo o arquivo caso exista
			$this->cache	= Util::definirCache( $this->diretorio, $this );

	}

	/**
	 * Definindo o cache da página caso exista
	 *
	 * @access private
	 * @return void
	 */
	private function metadata(): void{

		//Verificando
		if( $this->tipo !== 'tratamento' )
			//Definindo a metadata
			$this->metadata	= ( new Metadata( $this ) )->metadata;

	}

	/**
	 * Definindo as configurações da páginas
	 *
	 * @access private
	 * @return void
	 */
	private function configuracao(): void{

		//Verificando
		if( $this->tipo !== 'tratamento' )
			//Definindo as configurações
			$this->configuracao	= ( new Configuracao( $this ) )->configuracao;

	}

	/**
	 * Obtendo a configuração
	 *
	 * @param string $chave Chave.
	 * @param mixed $padrao Valor padrão caso a chave não exista (opcional default = null).
	 * @return mixed
	 */
	public function config(string $chave, mixed $padrao = null): mixed
	{

		//Retornando
		return (new Configuracao( $this ))->get($chave, $padrao);

	}

	/*
	 * FORMATAÇÕES
	 */

	/**
	 * Formatando a query string
	 *
	 * @access private
	 * @param array $queryString Query string passada
	 * @param mixed $query Tipo da querystring
	 * @return string
	 */
	private function formatarQueryString( array $queryString, string $query ): string{

		//Verificando se é do tipo módulo
		if( $query == 'modelo' )
			//Retornando
			return str_replace( '-', '\\', $queryString[ $query ] ?? '' );

		//Retornando
		return $queryString[ $query ];

	}

	/*
	 * DEFINIÇÕES
	 */

	/**
	 * Definindo o caminho para exibição
	 *
	 * @access private
	 * @uses App\Infraestrutura\Configuracao::get() Retornando a variável do projeto
	 * @uses App\Util\Util::redirecionar() Redirecionando
	 * @return void
	 */
	private function definirCaminho(): void{

		//Definindo o diretório do PHP
		$dirPhp	= Configuracao::get( 'dir.php' );

		//Definindo o caminho
		$this->caminho	= match( $this->tipo ){

			//Tipo tratamento
			'tratamento'	=> Configuracao::get( 'dir.requisicao' ) . "/{$this->diretorio}/{$this->{'diretorio-tratamento'}}/{$this->acao}.php",
			//Padrão
			default			=> ( !is_null( $this->diretorio ) && !is_null( $this->acao ) ) ? "{$dirPhp}/{$this->diretorio}/{$this->acao}.php" : "{$dirPhp}/{$this->diretorio}/index.php" };

		//Verificando
		if( !realpath( $this->caminho ) || !file_exists( $this->caminho ) )
		{
			//Carregando a página de erro internamente para preservar a URL original
			self::definir404();
		}

	}

	/**
	 * Definindo a página de erro 404
	 *
	 * @access private
	 * @uses App\Infraestrutura\Configuracao::get() Retornando a variável do projeto
	 * @return void
	 */
	public function definir404(): void
	{

		// Definindo o código de resposta HTTP para 404
		http_response_code(404);

		// Definindo as propriedades da página de erro 404
		$this->id 			= null;
		$this->slug 		= null;
		$this->pagina 		= null;
		$this->modelo 		= null;
		$this->diretorio 	= '404';
		$this->acao 		= 'index';
		$this->caminho 		= Configuracao::get('dir.php') . '/404/index.php';

	}

}