<?php
//Definindo o namespace
namespace App\Modelo\Genero\Autor;

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

        'nome' => 'Gêneros relacionados aos autores',
        'banco' => [
            'tabela' => [
                'nome' => 'autor_genero'
            ]
        ],
        'relacionamento' => [
            [
                'classe' => 'Genero\Genero',
                'campo' => 'Genero'
            ],
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

        'genero' => [
            'tipo' => Campo::INTEIRO,
            'tamanho' => 11,
            'formatar' => false
        ],

        'autor' => [
            'tipo' => Campo::INTEIRO,
            'tamanho' => 11,
            'formatar' => false
        ],

    ];

}