<?php
//Definindo o namespace
namespace App\Infraestrutura\Enum\Mensagem;

//Definindo as classes usadas
use App\Traits\Mensagem;

/**
 * Enum que representa as mensagens de arquivo
 *
 * @author Caroline Tacats <caroline.tacats62@gmail.com>
 * @package App\Infraestrutura\Enum\Mensagem
 */
enum Arquivo: string{

    //Definindo a trait usada
    use Mensagem;

    /**
     * Mensagem de erro padrão
     * @var string Mensagem de erro padrão
     */
	case PADRAO = 'Não foi possível realizar o upload. Erro: %s';

    /**
     * Mensagem de erro para tipo do arquivo inválido
     *
     * @var string Mensagem de erro para tipo do arquivo inválido
     */
	case TIPO_INVALIDO  = 'O arquivo passado não é válido. Apenas: %s.';

    /**
     * Mensagem de erro para tamanho do arquivo inválido
     *
     * @var string Mensagem de erro para tamanho do arquivo inválido
     */
	case TAMANHO_INVALIDO  = 'O tamanho do arquivo é muito grande. No máximo: %s.';

    /**
     * Mensagem de erro para extensão do arquivo inválida
     *
     * @var string Mensagem de erro para extensão do arquivo inválida
     */
	case EXTENSAO_INVALIDA  = 'A extensão do arquivo não é permitida. Apenas: %s.';

    /**
     * Mensagem de erro para erro de leitura das dimensões
     *
     * @var string Mensagem de erro para erro de leitura das dimensões
     */
	case DIMENSAO   = 'Não foi possível ler as dimensões do arquivo.';

    /**
     * Mensagem de erro para as dimensões mínimas
     *
     * @var string Mensagem de erro para as dimensões mínimas
     */
	case DIMENSAO_MINIMA_INVALIDA   = 'Dimensão mínima inválida.';

    /**
     * Mensagem de erro para as dimensões máximas
     *
     * @var string Mensagem de erro para as dimensões máximas
     */
	case DIMENSAO_MAXIMA_INVALIDA   = 'Dimensão máxima inválida.';

    /**
     * Código das opções
     *
     * @return int
     */
    public function codigo(): int{

        //Retornando o código
        return match( $this ){

            //Erro padrão
            self::PADRAO                    => 6000,
            //Erro de tipo de arquivo inválido
            self::TIPO_INVALIDO             => 6001,
            //Erro de tamanho inválido
            self::TAMANHO_INVALIDO          => 6002,
            //Erro de extensão inválida
            self::EXTENSAO_INVALIDA         => 6003,
            //Erro de leitura das dimensões
            self::DIMENSAO                  => 6004,
            //Erro de dimensão mínima
            self::DIMENSAO_MINIMA_INVALIDA  => 6005,
            //Erro de dimensão máxima
            self::DIMENSAO_MAXIMA_INVALIDA  => 6006 };

    }

}