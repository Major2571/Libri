<?php
//Definindo o namespace
namespace App\Infraestrutura\Enum\Mensagem;

//Definindo as classes usadas
use App\Traits\Mensagem;

/**
 * Enum que representa as mensagens de repositório
 *
 * @author Caroline Tacats <caroline.tacats62@gmail.com>
 * @package App\Infraestrutura\Enum\Mensagem
 */
enum Repositorio: string{

    //Definindo a trait usada
    use Mensagem;

    /**
     * Mensagem de erro para inserção
     *
     * @var string Mensagem de erro para inserção
     */
	case INSERIR    = 'Não foi possível realizar a inserção do conteúdo. Por favor, tente novamente mais tarde.';

    /**
     * Mensagem de erro para procura
     *
     * @var string Mensagem de erro para procura
     */
	case PROCURAR   = 'Não foi possível realizar a procura do conteúdo. Por favor, tente novamente mais tarde.';

    /**
     * Mensagem de erro para listagem
     *
     * @var string Mensagem de erro para listagem
     */
	case LISTAR = 'Não foi possível realizar a listagem dos conteúdos. Por favor, tente novamente mais tarde.';

    /**
     * Mensagem de erro para contagem
     *
     * @var string Mensagem de erro para contagem
     */
	case CONTAR =  'Não foi possível realizar a contagem dos conteúdos. Por favor, tente novamente mais tarde.';

    /**
    * Mensagem de erro para edição
    *
    * @var string Mensagem de erro para edição
    */
    case EDITAR = 'Não foi possível realizar a edição do conteúdo. Por favor, tente novamente mais tarde.';

    /**
     * Mensagem de erro para relacionamento
     *
     * @var string Mensagem de erro para relacionamento
     */
    case RELACIONAR = 'Não foi possível realizar o relacionamento do conteúdo. Por favor, tente novamente mais tarde.';

    case REMOVER = 'Não foi possível realizar a exclusão do conteúdo. Por favor, tente novamente mais tarde.';
    /**
     * Código das opções
     *
     * @return int
     */
    public function codigo(): int{

        //Retornando o código
        return match( $this ){

            //Erro de inserção
            self::INSERIR   => 4000,
            //Erro de procura
            self::PROCURAR  => 4001,
            //Erro de listagem
            self::LISTAR    => 4002,
            //Erro de de contagem
            self::CONTAR    => 4003,
            //Erro de edição
            self::EDITAR    => 4004,
            //Erro de relacionamento
            self::RELACIONAR => 4005 };

    }

}