<?php
//Definindo o namespace
namespace App\Modelo;

//Definindo as classes usadas
use App\Infraestrutura\Enum\Tipo\Campo;
use App\Modelo\Modelo;

/**
 * Classe do Interesse
 * Herdada da classe pai Modelo
 *
 * @final
 * @author Caroline Tacats <caroline.tacats62@gmail.com>
 * @package App\Modelo
 */
final class Interesse extends Modelo
{

	/**
	 * Configurações
	 *
	 * @var array Array de configurações
	 */
	public array $configuracao = [

		'nome' => 'Interesses',
		'banco' => [
			'tabela' => [
				'nome' => 'interesse'
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
			'tamanho' => 11,
			'formatar' => false
		],

		'nome' => [
		]

	];

}