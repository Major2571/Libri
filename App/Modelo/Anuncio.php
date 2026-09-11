<?php
//Definindo o namespace
namespace App\Modelo;

//Definindo as classes usadas
use App\Infraestrutura\Enum\Tipo\Campo;
use App\Modelo\Modelo;

/**
 * Classe do Anuncio
 * Herdada da classe pai Modelo
 *
 * @final
 * @author Caroline Tacats <caroline.tacats62@gmail.com>
 * @package App\Modelo
 */
final class Anuncio extends Modelo
{

	/**
	 * Configurações
	 *
	 * @var array Array de configurações
	 */
	public array $configuracao = [

		'nome' => 'Anuncio',
		'banco' => [
			'tabela' => [
				'nome' => 'imagem_anuncio'
			]
		],
		'relacionamento' => [
			[
				'classe' => 'Midia',
				'campo' => 'Imagem'
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

		'imagem' => [
			'tipo' => Campo::INTEIRO,
			'formatar' => false,
			'tamanho' => 11
		],

		'titulo' => [
			'formatar' => false
		],

		'link' => [
			'formatar' => false
		],

		'status' => [
			'tipo' => Campo::INTEIRO,
			'formatar' => false,
		],

		'data_publicacao' => [
			'tipo' => Campo::DATA_CUSTOMIZADA,
			'formatar' => false,
			'obrigatorio' => false
		],

		'data_inativacao' => [
			'tipo' => Campo::DATA_CUSTOMIZADA,
			'formatar' => false,
			'obrigatorio' => false
		]


	];

}