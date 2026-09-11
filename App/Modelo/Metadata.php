<?php
//Definindo o namespace
namespace App\Modelo;

//Definindo as classes usadas
use App\Modelo\Modelo;

/**
 * Classe da metadata
 * Herdada da classe pai Modelo
 *
 * @final
 * @author Caroline Tacats <caroline.tacats62@gmail.com>
 * @package App\Modelo
 */
final class Metadata extends Modelo{

	/**
	 * Configurações
	 *
	 * @var array Array de configurações
	 */
    public array $configuracao	= [

		'nome'	=> 'Metadatas',
		'banco'	=> [
			'tabela'	=> [
				'nome'	=> 'metadata' ] ] ];
	/**
	 * Atributos
	 *
	 * @var array Array de atributos
	 */
	public array $atributos	= [

		'titulo'	=> [],

		'meta_descricao'	=> [],

		'titulo_imagem'	=> [
			'obrigatorio' => false ],

		'imagem'	=> [
			'formatar' => false,
			'obrigatorio' => false ] ];

}