<?php
//Definindo o namespace
namespace App\Modelo;

//Definindo as classes usadas
use App\Infraestrutura\Enum\Tipo\Campo;
use App\Modelo\Modelo;

/**
 * Classe de configuração
 * Herdada da classe pai Modelo
 *
 * @final
 * @author Caroline Tacats <caroline.tacats62@gmail.com>
 * @package App\Modelo
 */
final class Configuracao extends Modelo{

	/**
	 * Configurações
	 *
	 * @var array Array de configurações
	 */
    public array $configuracao	= [

		'nome'		=> 'Configuração',
		'banco'		=> [
			'tabela'	=> [
				'nome'	=> 'configuracao_projeto' ] ] ];
	/**
	 * Atributos
	 *
	 * @var array Array de atributos
	 */
	public array $atributos	= [

		'codigo_cabecalho'	=> [
			'tipo' => Campo::TEXTO,
			'formatar' => false,
			'obrigatorio' => false ],

        'codigo_topo'	=> [
			'tipo' => Campo::TEXTO,
			'formatar' => false,
			'obrigatorio' => false ],

        'codigo_rodape'	=> [
			'tipo' => Campo::TEXTO,
			'formatar' => false,
			'obrigatorio' => false ],

        'codigo_sucesso'	=> [
			'tipo' => Campo::TEXTO,
			'formatar' => false,
			'obrigatorio' => false ],

        'manutencao'	=> [
			'tipo' => Campo::INTEIRO,
			'tamanho'	=> 1,
			'formatar' => false ] ];

}