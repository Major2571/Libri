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
final class Livro extends Modelo
{

	/**
	 * Configurações
	 *
	 * @var array Array de configurações
	 */
	public array $configuracao = [

		'nome' => 'Livros',
		'diretorio' => 'livros',
		'banco' => [
			'tabela' => [
				'nome' => 'livro'
			]
		],
		'relacionamento' => [
			[
				'classe' => 'Genero\Autor\Genero',
				'campo' => 'GenerosRelacionados'
			],
			[
				'classe' => 'Autor',
				'campo' => 'AutoresRelacionados'
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

		'titulo' => [
			'nome' => 'Título'
		],

		'isbn' => [
			'nome' => 'ISBN',
			'tamanho' => 20,
			'obrigatorio' => false
		],

		'ano_publicacao' => [
			'nome' => 'Ano de Publicação',
			'obrigatorio' => false,
			'tipo' => Campo::DATA,
			'formatar' => false
		],

		'quantidade_disponivel' => [
			'nome' => 'Quantidade Disponível',
			'tipo' => Campo::INTEIRO,
			'tamanho' => 1,
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