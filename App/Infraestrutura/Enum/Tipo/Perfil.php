<?php
//Definindo o namespace
namespace App\Infraestrutura\Enum\Tipo;

/**
 * Enum que representa os tipos dos perfis
 *
 * @author Caroline Tacats <caroline.tacats62@gmail.com>
 * @package App\Infraestrutura\Enum\Tipo
 */
enum Perfil: int{

    /**
     * Perfil comum
     *
     * @var int Tipo do perfil como comum
     */
    case COMUM = 1;

    /**
     * Lojista
     *
     * @var int Tipo do perfil como lojista
     */
    case LOJISTA   = 2;

    /**
     * Membro da equipe
     *
     * @var int Tipo do perfil como membro da equipe
     */
    case MEMBRO_EQUIPE   = 3;

}
