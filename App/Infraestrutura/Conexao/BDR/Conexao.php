<?php
//Defining the namespace
namespace App\Infraestrutura\Conexao\BDR;

//Definindo as classes usadas
use App\Infraestrutura\Configuracao;
use App\Infraestrutura\Enum\Status\Http;
use App\Util\Util;
use PDOException;
use PDO;

/**
 * Classe de conexão
 * Conexão para Banco de Dados Relacional
 *
 * @author Caroline Tacats <caroline.tacats62@gmail.com>
 * @package App\Infraestrutura\Conexao\BDR
 */
class Conexao{

	/**
	 * Instância
	 *
	 * @access private
	 * @var self
	 */
	private static $instancia;

	/**
	 * Definindo a constante das opções de conexão
	 *
	 * @access private
	 * @var array
	 */
	private const OPCOES	= [

		PDO::MYSQL_ATTR_INIT_COMMAND 		=> 'SET NAMES utf8',
		PDO::ATTR_ERRMODE 					=> PDO::ERRMODE_EXCEPTION,
		PDO::MYSQL_ATTR_USE_BUFFERED_QUERY	=> true,
		PDO::ATTR_DEFAULT_FETCH_MODE		=> PDO::FETCH_ASSOC ];

	/**
	 * Método construtor da classe
	 *
	 * @param null|object $conexao Conexão
	 * @uses App\Infraestrutura\Configuracao::get() Retornando a variável do projeto
	 * @uses App\Util\Util::redirecionar() Redirecionando o usuário
	 */
	public function __construct( public ?object $conexao = null ){

		try{

			//Definindo a string de conexão
			$conexao	= sprintf(
				'mysql:host=%s;port=%s;dbname=%s;charset=utf8',
				Configuracao::get( 'banco.endereco' ),
				Configuracao::get( 'banco.porta' ),
				Configuracao::get( 'banco.base' ) );

			//Definindo a conexão
			$this->conexao	= new PDO( $conexao, Configuracao::get( 'banco.usuario' ), Configuracao::get( 'banco.senha' ), self::OPCOES );

		}catch( PDOException ){

			//Redirecionando
			Util::redirecionar( Http::TEMPORARY_REDIRECT->codigo(), Configuracao::get( 'url.padrao' ) . '/manutencao' );

		}

	}

	/**
	 * Obtendo a instância
	 *
	 * @static
	 * @return self
	 */
	public static function instancia(): self{

		//Verificando a existência da instância
		if( is_null( self::$instancia ) )
			//Definindo a instância
			self::$instancia	= new self();

		//Retornando
		return self::$instancia;

	}

}