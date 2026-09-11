<?php
//Definindo o namespace
namespace App\Infraestrutura\Enum;

/**
 * Enum que representa os destinos
 *
 * @author Caroline Tacats <caroline.tacats62@gmail.com>
 * @package App\Infraestrutura\Enum
 */
enum Destino: int{

    /**
     * Destino como fale conosco
     *
     * @var int Destino como fale conosco
     */
    case FALE_CONOSCO   = 1;

    /**
     * Destino como parceiro
     *
     * @var int Destino como parceiro
     */
    case PARCEIRO   = 2;

}
