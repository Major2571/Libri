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
final class Nacionalidade extends Modelo
{

    /**
     * Configurações
     *
     * @var array Array de configurações
     */
    public array $configuracao = [


        'nome' => 'Nacionalidades',
        'diretorio' => 'nacionalidades',
        'banco' => [
            'tabela' => [
                'nome' => 'nacionalidade'
            ]
        ]

    ];

    /**
     * Atributos
     *
     * @var array Array de atributos
     */
    public array $atributos = [

        'codigo_iso2' => [
            'nome' => 'Código ISO2',
            'tamanho' => 2,
            'formatar' => false
        ],

        'codigo_iso3' => [
            'nome' => 'Código ISO3',
            'tamanho' => 3,
            'formatar' => false
        ],

        'pais_pt' => [
            'nome' => 'Nome do País (PT)',
            'tamanho' => 80
        ],

        'gentilico_pt' => [
            'nome' => 'Gentílico (PT)',
            'tamanho' => 80,
            'obrigatorio' => false,
            'formatar' => false
        ]

    ];

}