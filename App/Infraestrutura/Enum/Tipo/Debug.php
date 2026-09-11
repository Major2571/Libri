<?php
//Definindo o namespace
namespace App\Infraestrutura\Enum\Tipo;

/**
 * Enum que representa os tipos de debug
 *
 * @author Caroline Tacats <caroline.tacats62@gmail.com>
 * @package App\Infraestrutura\Enum\Tipo
 */
enum Debug: int{

    /**
     * Banco de dados
     *
     * @var int Tipo do debug como banco de dados
     */
    case BANCO_DE_DADOS = 1;

}
