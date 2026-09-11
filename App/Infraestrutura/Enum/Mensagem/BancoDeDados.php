<?php
//Definindo o namespace
namespace App\Infraestrutura\Enum\Mensagem;

//Definindo as classes usadas
use App\Traits\Mensagem;

/**
 * Enum que representa as mensagens de banco de dados
 *
 * @author Caroline Tacats <caroline.tacats62@gmail.com>
 * @package App\Infraestrutura\Enum\Mensagem
 */
enum BancoDeDados: string{

    //Definindo a trait das mensagens
    use Mensagem;

    /**
     * Mensagem de erro padrão
     *
     * @var string Mensagem de erro padrão
     */
	case PADRAO = 'Algum erro foi encontrado. Por favor, entre em contato com o administrador da página.';

	/**
     * Mensagem de erro de relacionamento
     *
     * @var string Mensagem de erro de relacionamento
     */
	case RELACIONAMENTO = 'Não foi possível realizar a remoção do conteúdo. Existem relacionamentos entre os seus módulos. Por favor, entre em contato com o administrador da página.';

    /**
     * Mensagem de erro de edição
     *
     * @var string Mensagem de erro de edição
     */
	case INSERCAO_EDICAO    = 'Não foi possível realizar a inserção/edição do conteúdo. Não existe o módulo pai para realizar a inserção/edição. Por favor, entre em contato com o adminitrador da página.';

    /**
     * Mensagem de erro de duplicidade
     *
     * @var string Mensagem de erro de duplicidade
     */
	case DUPLICADO  = 'Não foi possível realizar o cadastro do conteúdo. Já existe uma entrada cadastrada com o valor passado.';

    /**
     * Código das opções
     *
     * @return int
     */
    public function codigo(): int{

        //Retornando o código
        return match( $this ){

            //Erro padrão
            self::PADRAO            => 2000,
            //Erro de inserção/edição do conteúdo
            self::INSERCAO_EDICAO   => 2001,
            //Erro de duplicidade do conteúdo
            self::DUPLICADO         => 2002,
            //Erro de relacionamento do conteúdo
            self::RELACIONAMENTO    => 2003 };

    }

}