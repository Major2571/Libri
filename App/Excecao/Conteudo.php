<?php
//Definindo o namespace
namespace App\Excecao;

//Definindo as classes usadas
use App\Infraestrutura\Enum\Status\Http;
use Exception;

/**
 * Exceção dos conteúdos
 * Herdada da classe pai Exception
 *
 * @final
 * @author Caroline Tacats <caroline.tacats62@gmail.com>
 * @package App\Excecao
 */
final class Conteudo extends Exception{

	/**
	 * Construtor da classe
	 *
	 * @param null|string $mensagem Mensagem
	 * @param null|int $resposta Código da resposta
	 * @param null|callable $callback Callback
	 */
	public function __construct(
		?string $mensagem 		= null,
		public ?int $resposta	= null ){

		//Definindo a resposta
		$resposta	= $resposta ?? Http::OK->codigo();

		//Definindo a resposta
		http_response_code( $resposta );

		//Construtor da classe pai
		parent::__construct( $mensagem, $resposta );

	}

}