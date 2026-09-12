<?php
//Definindo o namespace
namespace App\Modelo;

//Definindo as classes usadas
use App\Infraestrutura\Enum\Tipo\Campo;
use App\Modelo\Modelo;

/**
 * Classe do Autor
 * Herdada da classe pai Modelo
 *
 * @final
 * @author Caroline Tacats <caroline.tacats62@gmail.com>
 * @package App\Modelo
 */
final class Autor extends Modelo
{

	/**
	 * Configurações
	 *
	 * @var array Array de configurações
	 */
	public array $configuracao = [

		'nome' => 'Autores',
		'diretorio' => 'autores',
		'banco' => [
			'tabela' => [
				'nome' => 'autor'
			]
		],
		'relacionamento' => [
			[
				'classe' => 'Autor',
				'campo' => 'Autor'
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
			'nome' => 'Nome'
		],

		'pseudonimo' => [
			'nome' => 'Pseudônimo',
			'obrigatorio' => false
		],

		'data_nascimento' => [
			'nome' => 'Data de Nascimento',
			'obrigatorio' => false,
			'tipo' => Campo::DATA,
			'formatar' => false
		],

		'data_falecimento' => [
			'nome' => 'Data de Falecimento',
			'obrigatorio' => false,
			'tipo' => Campo::DATA,
			'formatar' => false
		],

		'nacionalidade' => [
			'nome' => 'Nacionalidade',
			'obrigatorio' => false
		],

		'biografia' => [
			'nome' => 'Biografia',
			'tipo' => Campo::TEXTO,
			'formatar' => false,
			'obrigatorio' => false
		],

		'status' => [
			'nome' => 'Status',
			'tipo' => Campo::INTEIRO,
			'tamanho' => 1,
		],

		'imagem' => [
			'nome' => 'Imagem',
			'obrigatorio' => false
		]

	];

}