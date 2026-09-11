<?php
//Definindo o namespace
namespace App\Modelo;

//Definindo as classes usadas
use App\Modelo\Modelo;
use App\Infraestrutura\Enum\Tipo\Campo;

/**
 * Classe dos mídias
 * Herdada do modelo principal
 *
 * @final
 * @author Caroline Tacats <caroline.tacats62@gmail.com>
 * @package App\Sistema\Modelo
 */
final class Midia extends Modelo
{

	/**
	 * Configurações
	 *
	 * @var array Array de configurações
	 */
	public array $configuracao = [

		'nome' => 'Mídias',
		'banco' => [
			'tabela' => [
				'nome' => 'midia'
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
		],

		'titulo' => [
			'obrigatorio' => false
		],

		'subtitulo' => [
			'obrigatorio' => false
		],

		'texto_alternativo' => [
			'obrigatorio' => false
		],

		'nome_arquivo' => [
		]

	];

}