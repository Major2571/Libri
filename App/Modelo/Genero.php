<?php
//Definindo o namespace
namespace App\Modelo;

//Definindo as classes usadas
use App\Infraestrutura\Enum\Tipo\Campo;
use App\Modelo\Modelo;

/**
 * Classe do Genero
 * Herdada da classe pai Modelo
 *
 * @final
 * @author Caroline Tacats <caroline.tacats62@gmail.com>
 * @package App\Modelo
 */
final class Genero extends Modelo
{

	/**
	 * Configurações
	 *
	 * @var array Array de configurações
	 */
	public array $configuracao = [

		'nome' => 'Gêneros',
		'diretorio' => 'generos',
		'banco' => [
			'tabela' => [
				'nome' => 'genero'
			]
		],
		'relacionamento' => [
			[
				'classe' => 'Genero',
				'campo' => 'Genero'
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

		'genero_pai' => [
			'nome' => 'Genero Pai',
			'tipo' => Campo::INTEIRO,
			'tamanho' => 11,
			'formatar' => false,
			'obrigatorio' => false
		],

		'nome' => [
			'nome' => 'Nome'
		],

		'descricao' => [
			'nome' => 'Descrição',
			'tipo' => Campo::TEXTO,
			'formatar' => false,
			'obrigatorio' => false
		]

	];

}