<?php
//Definindo o namespace
namespace App\Modelo;

//Definindo as classes usadas
use App\Infraestrutura\Enum\Tipo\Campo;
use App\Modelo\Modelo;

/**
 * Classe do projeto
 * Herdada da classe pai Modelo
 *
 * @final
 * @author Caroline Tacats <caroline.tacats62@gmail.com>
 * @package App\Modelo
 */
final class Projeto extends Modelo
{

	/**
	 * Configurações
	 *
	 * @var array Array de configurações
	 */
	public array $configuracao = [

		'nome' => 'Projeto',
		'banco' => [
			'tabela' => [
				'nome' => 'projeto'
			]
		],
		'relacionamento' => [
			[
				'classe' => 'Social\Projeto\Social',
				'campo' => 'Social'
			]
		]
	];
	/**
	 * Atributos
	 *
	 * @var array Array de atributos
	 */
	public array $atributos = [

		'id' => [
			'tipo' => Campo::INTEIRO,
			'formatar' => false,
			'tamanho' => 11
		]
	];

}