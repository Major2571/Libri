<?php
//Definindo o namespace
namespace App\Infraestrutura\Enum\Tipo;

/**
 * Enum que representa os tipos dos campos
 *
 * @author Caroline Tacats <caroline.tacats62@gmail.com>
 * @package App\Infraestrutura\Enum\Tipo
 */
enum Campo: int{

    /**
     * Campo inteiro
     *
     * @var int Tipo do campo como inteiro
     */
    case INTEIRO    = 1;

    /**
     * Campo string
     *
     * @var int Tipo do campo como string
     */
    case STRING = 2;

    /**
     * Campo email
     *
     * @var int Tipo do campo como email
     */
    case EMAIL  = 3;

    /**
     * Campo booleano
     *
     * @var int Tipo do campo como booleano
     */
    case BOOLEANO   = 4;

    /**
     * Campo data
     *
     * @var int Tipo do campo como data
     */
    case DATA   = 5;

    /**
     * Campo URL
     *
     * @var int Tipo do campo como URL
     */
    case URL  = 6;

    /**
     * Campo longitude
     *
     * @var int Tipo do campo como longitude
     */
    case LONGITUDE  = 7;

    /**
     * Campo latitude
     *
     * @var int Tipo do campo como latitude
     */
    case LATITUDE   = 8;

    /**
     * Campo array
     *
     * @var int Tipo do campo como array
     */
    case ARRAY  = 9;

    /**
     * Campo json
     *
     * @var int Tipo do campo como json
     */
    case JSON  = 10;

    /**
     * Campo objeto
     *
     * @var int Tipo do campo como objeto
     */
    case OBJETO  = 11;

    /**
     * Campo texto
     *
     * @var int Tipo do campo como texto
     */
    case TEXTO  = 12;

    /**
     * Campo CNPJ
     *
     * @var int Tipo do campo como CNPJ
     */
    case CNPJ   = 13;

    /**
     * Campo CPF
     *
     * @var int Tipo do campo como CPF
     */
    case CPF    = 14;

    /**
     * Campo data customizada
     *
     * @var int Tipo do campo como data customizada
     */
    case DATA_CUSTOMIZADA   = 15;

    /**
     * Campo decimal
     *
     * @var int Tipo do campo como decimal
     */
    case DECIMAL    = 16;

}
