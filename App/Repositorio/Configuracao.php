<?php
//Definindo o namespace
namespace App\Repositorio;

//Definindo as classes usadas
use App\Infraestrutura\Base;
use App\Modelo\Modelo;

/**
 * Repositório das configurações do repositório
 *
 * @final
 * @author Caroline Tacats <caroline.tacats62@gmail.com>
 * @package App\Repositorio\BDR
 */
final class Configuracao extends Base{

    /**
	 * Método construtor da classe
	 *
     * @param Modelo $modelo Objeto do modelo
     */
    public function __construct( Modelo $modelo ){

        //Definindo o objeto da configuração
        $configuracao   = (object) $modelo->configuracao;

        //Definindo as configurações
        $this->tabela   = in_array( 'BDR', $modelo->tipoRepositorio ) ? $configuracao->banco[ 'tabela' ][ 'nome' ] ?? null : null;
        $this->nome = $configuracao->nome;
        $this->campos_busca = $configuracao->banco['busca']['campos'] ?? null;

		//Definindo os atributos
		$array			= array_map( fn( $campo ) => $campo, $modelo->atributos );

        //Definindo os campos
        $this->campos   = implode( ',', array_keys( $array ) );

    }

}