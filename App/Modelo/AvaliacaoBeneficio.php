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
final class AvaliacaoBeneficio extends Modelo
{

	/**
	 * Configurações
	 *
	 * @var array Array de configurações
	 */
	public array $configuracao = [

		'nome' => 'AvaliacaoBeneficio',
		'banco' => [
			'tabela' => [
				'nome' => 'avaliacao_beneficio'
			]
		]

	];

	/**
	 * Atributos
	 *
	 * @var array Array de atributos
	 */
	public array $atributos = [

		'usuario' => [
			'tipo' => Campo::INTEIRO,
			'formatar' => false,
			'tamanho' => 11
		],

		'beneficio' => [
			'tipo' => Campo::INTEIRO,
			'formatar' => false,
			'tamanho' => 11
		],

		'nota' => [
			'tipo' => Campo::INTEIRO,
			'formatar' => false
		],

		'mensagem' => [
			'tipo' => Campo::TEXTO,
			'obrigatorio' => false,
			'formatar' => false
		]

	];

}