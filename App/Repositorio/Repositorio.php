<?php
//Definindo o namespace
namespace App\Repositorio;

//Definindo as classes usadas
use App\Infraestrutura\Fabrica;
use App\Infraestrutura\Base;

/**
 * Repositório abstrato para o projeto
 * Todas as classes de repositório irão herdar desta classe
 * Herdada da classe base para auxílio nas chamadas
 *
 * @abstract
 * @author Caroline Tacats <caroline.tacats62@gmail.com>
 * @package App\Repositorio
 */
abstract class Repositorio extends Base{

	/**
	 * Obtendo a instância
	 *
	 * @static
	 * @param Fabrica $fabrica Objeto da fábrica
	 * @return static
	 */
	public static function instancia( Fabrica $fabrica ): static{

		//Definindo o nome da classe do repositório
		$instancia	= $fabrica->configuracao->nomeClasse . $fabrica->implementacao;

		//Verificando
		if( !array_key_exists( $instancia, self::$instancia ) )
			//Definindo
			self::$instancia[ $instancia ]	= new $fabrica->propriedades[ 'repositorio' ]( $fabrica );

		//Retornando
		return self::$instancia[ $instancia ];

	}

	/*
	 * ÚTIL
	 */

	/**
	 * Limpando os complementos e os parâmetros
	 *
	 * @access protected
	 * @return void
	 */
	protected function limparComplementosParametros(): void{

		//Limpando os complementos e parâmetros
		$this->complemento	= null;
		$this->parametros	= [];

	}

	/**
	 * Definindo os termos para os métodos
	 *
	 * @param array $termos Termos
	 * @return static
	 */
	public function termos(array $termos): static
	{

		//Definindo os filtros para busca
		$filtros = [];

		//Listando os filtros
		foreach ($filtros as $campo)
			//Verificando se o campo foi passado
			if (!empty($termos[$campo]))
				//Definindo o tipo da variável
				$this->definirComplemento($campo, implode(',', (array) $termos[$campo]), 'in');

		//Termo
		if (!empty($termos['termo']))
			//Definindo o tipo da variável
			$this->definirComplemento($this->configuracao->campos_busca, $termos['termo'], 'busca');

		//Retornando
		return $this;

	}

}