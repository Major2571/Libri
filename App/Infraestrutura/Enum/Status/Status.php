<?php
//Definindo o namespace
namespace App\Infraestrutura\Enum\Status;

/**
 * Enum que representa os possíveis status
 *
 * @author Caroline Tacats <caroline.tacats62@gmail.com>
 * @package App\Infraestrutura\Enum\Status
 */
enum Status: int{

    /**
     * Status ativo
     *
     * @var int Valor do status ativo
     */
    case ATIVO  = 1;

    /**
     * Status inativo
     *
     * @var int Valor do status inativo
     */
    case INATIVO    = 0;

    /**
     * Status retirado
     *
     * @var int Valor do status retirado
     */
    case RETIRADO   = 2;
    
    /**
     * Status indisponível
     *
     * @var int Valor do status indisponível
     */
    case INDISPONIVEL = 3;

}
