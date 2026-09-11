<?php
//Definindo o namespace
namespace App\Excecao;

//Definindo as classes usadas
use App\Infraestrutura\Enum\Status\Http;
use App\Infraestrutura\Configuracao;
use App\Util\Util;
use PDOException;
use Exception;

/**
 * Classe de exceção para o banco de dados
 * Herdada da classe pai Exception
 *
 * @final
 * @author Caroline Tacats <caroline.tacats62@gmail.com>
 * @package App\Excecao
 */
final class BancoDeDados extends Exception{

	/**
	 * Método construtor da classe
	 *
	 * @param string $mensagem Mensagem
	 * @param PDOException $excecao Exceção do repositório
	 * @param null|string $complemento Complemento da mensagem
	 * @param null|int $resposta Código da resposta
	 * @uses App\Util\Util::verificarTipoErroBandoDeDados() Verificando o tipo de erro do banco de dados
	 * @uses App\Infraestrutura\Configuracao\Configuracao::get() Retornando a variável do projeto
	 */
	public function __construct(
		string $mensagem,
		PDOException $excecao,
		?string $complemento = null,
		public ?int $resposta = null ){

		//Definindo a resposta
		$resposta	= $resposta ?? Http::OK->codigo();

		//Definindo o código de resposta
		http_response_code( $resposta );

		//Definindo o complemento da mensagem
		$complemento = Util::verificarTipoErroBandoDeDados( $excecao->errorInfo[ 1 ] );

		//Definindo a mensagem
		$mensagem    = ( !Configuracao::get( 'projeto.producao' ) ) ? "{$mensagem} {$complemento}({$excecao->getMessage()})" : $mensagem . $complemento;

        //Construtor da classe pai
		parent::__construct( $mensagem, $resposta );

	}

}