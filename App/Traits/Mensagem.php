<?php
//Definindo o namespace
namespace App\Traits;

//Definindo as classes usadas
use App\Infraestrutura\Internacionalizacao;
use App\Infraestrutura\Configuracao;

/**
 * Trait das mensagens de erro
 *
 * @author Caroline Tacats <caroline.tacats62@gmail.com>
 * @package App\Infraestrutura\Traits
 */
trait Mensagem{

    /**
     * Retornando o texto já traduzido caso necessário
     *
	 * @uses App\Infraestrutura\Configuracao::get() Retornando a variável do projeto
     * @uses App\Infraestrutura\Internacionalizacao::traduzir() Tradução da mensagem de acordo com a localidade
     * @return string
     */
    public function texto(): string{

        //Retornando a mensagem já traduzida
        return Configuracao::get( 'projeto.internacionalizacao' ) ? Internacionalizacao::traduzir( $this->value ) : $this->value;

    }

    /**
     * Retornando a mensagem completa e seu código em formato de array
     *
     * @return array
     */
    public function array(): array{

        //Retornando
        return [

            //Definindo o código
            'codigo'    => $this->codigo(),

            //Definindo a mensagem
            'mensagem'  => $this->texto() ];

    }

}