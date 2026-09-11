<?php
//Definindo o namespace
namespace App\Infraestrutura\Enum\Tipo;

/**
 * Enum que representa os tipos das páginas de sucesso
 *
 * @author Caroline Tacats <caroline.tacats62@gmail.com>
 * @package App\Infraestrutura\Enum\Tipo
 */
enum Sucesso: int{

    /**
     * Esqueci minha senha
     *
     * @var int Tipo do sucesso como esqueci minha senha
     */
    case ESQUECI_MINHA_SENHA = 1;

    /**
     * Salvar pontuação
     *
     * @var int Tipo do sucesso como salvar pontuação
     */
    case PONTUACAO_SALVA = 2;

}
