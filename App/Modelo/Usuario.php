<?php
//Definindo o namespace
namespace App\Modelo;

//Definindo as classes usadas
use App\Infraestrutura\Enum\Tipo\Campo;
use App\Modelo\Modelo;

/**
 * Classe do cliente
 * Herdada da classe pai Modelo
 *
 * @final
 * @author Caroline Tacats <caroline.tacats62@gmail.com>
 * @package App\Modelo
 */
final class Usuario extends Modelo
{

	/**
	 * Configurações
	 *
	 * @var array Array de configurações
	 */
	public array $configuracao = [

		'nome' => 'Usuário',
		'unico' => [
			'email',
			'documento'
		],
		'banco' => [
			'tabela' => [
				'nome' => 'usuario_plaza_mais'
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
			'nome' => 'Identificador do cliente',
			'tipo' => Campo::INTEIRO,
			'tamanho' => 11,
			'formatar' => false
		],

		'perfil' => [
			'nome' => 'Identificador do perfil',
			'tipo' => Campo::INTEIRO,
			'tamanho' => 11,
			'obrigatorio' => false,
			'formatar' => false
		],

		'nome' => [
			'formatar' => false,
			'obrigatorio' => false,
		],

		'email' => [
			'tipo' => Campo::EMAIL,
			'formatar' => false
		],

		'documento' => [
			'tipo' => Campo::CPF,
			'obrigatorio' => false
		],

		'telefone' => [
			'obrigatorio' => false,
			'formatar' => false,
		],

		'data_nascimento' => [
			'tipo' => Campo::DATA_CUSTOMIZADA,
			'obrigatorio' => false,
		],

		'senha' => [
			'formatar' => false
		],

		'genero' => [
			'obrigatorio' => false,
		],

		'status' => [
			'tipo' => Campo::INTEIRO,
			'formatar' => false,
		],

		'imagem' => [
			'obrigatorio' => false,
			'formatar' => false
		]

	];

}