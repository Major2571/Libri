<?php
//Definindo o namespace
namespace App\Infraestrutura;

/**
 * Classe base para auxílio das outras classe
 * Classe abstrata para ser herdada
 *
 * @abstract
 * @author Caroline Tacats <caroline.tacats62@gmail.com>
 * @package App\Sistema\Infraestrutura
 */
abstract class Base{

	/**
	 * Parâmetros das junções de vários métodos
	 *
	 * @access protected
	 * @var null|array Array com os parâmetros
	 */
	protected ?array $parametros = [];

	/**
	 * Propriedades
	 *
	 * @var array Propriedades
	 */
	public array $propriedades;

	/**
	 * Instâncias para definir
	 *
	 * @access protected
	 * @var array Instâncias
	 */
	protected static array $instancia = [];

	/**
	 * Atribuições
	 *
	 * @param string $chave Chave do campo
	 * @param mixed $valor Valor do campo
	 * @return void
	 */
	public function __set( string $chave, mixed $valor ): void{

		//Definindo
        $this->propriedades[ $chave ]	= $valor;

    }

	/**
	 * Retornando as atribuições
	 *
	 * @param string $chave Chave do campo
	 * @return mixed
	 */
	public function __get( string $chave ): mixed{

		//Returning
        return $this->propriedades[ $chave ] ?? null;

    }

	/**
	 * Verificando a existência do atributo
	 *
	 * @param mixed $campo Campo para verificação
	 * @return bool
	 */
	public function __isset( $campo ): bool {

		//Retornando se o atributo existe
        return isset( $this->propriedades[ $campo ] );

    }

	 /**
	 * Método mágico para construção dos métodos
	 *
	 * @param mixed $nome Nome do método
	 * @param mixed $valor valor passado para o método
	 * @return static
	 */
	public function __call( $nome, $valor ): static{

		//Definindo o valor passado
        $this->$nome    = $valor[ 0 ] ?? null;

		//Retornando
		return $this;

    }

}