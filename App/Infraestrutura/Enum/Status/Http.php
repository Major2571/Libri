<?php
//Definindo o namespace
namespace App\Infraestrutura\Enum\Status;

//Definindo as classes usadas
use App\Traits\Mensagem;

/**
 * Enum que representa as possíveis respostas Http
 *
 * @author Caroline Tacats <caroline.tacats62@gmail.com>
 * @package App\Infraestrutura\Enum\Status
 */
enum Http: string{

    //Definindo a trait usada
    use Mensagem;

    /**
     * Resposta para solicitação correta
     *
     * @var string Status da solicitação correta
     */
    case OK = 'Sucesso';

    /**
     * Resposta para solicitação incorreta
     *
     * @var string Status da solicitação incorreta
     */
    case BAD_REQUEST  = 'Requisição inválida';

    /**
     * Resposta para solicitação não encontrada
     *
     * @var string Status da solicitação não encontrada
     */
    case NOT_FOUND = 'Não encontrado';

    /**
     * Resposta para solicitação não autorizada
     *
     * @var string Status da solicitação não autorizada
     */
    case UNAUTHORIZED = 'Não autorizado';

    /**
     * Resposta para solicitação duplicada
     *
     * @var string Status da solicitação duplicada
     */
    case CONFLICT  = 'Duplicado';

    /**
     * Resposta para solicitação proibida
     *
     * @var string Status da solicitação proibida
     */
    case FORBIDDEN   = 'Sem permissão';

    /**
     * Resposta para solicitação em manutenção
     *
     * @var string Status da solicitação em manutenção
     */
    case SERVICE_UNAVAILABLE   = 'Em manutenção';

    /**
     * Resposta para solicitação de redirecionamento temporário
     *
     * @var string Status da solicitação de redirecionamento temporário
     */
    case TEMPORARY_REDIRECT    = 'Redirecionamento temporário';

    /**
     * Resposta para solicitação de movido permanentemente
     *
     * @var string Status da solicitação de movido permanentemente
     */
    case MOVED_PERMANENTLY    = 'Movido permanentemente';

    /**
     * Resposta para solicitação de redirecionamento encontrado
     *
     * @var string Status da solicitação de redirecionamento encontrado
     */
    case FOUND    = 'Redirecionamento encontrado';

    /**
     * Resposta para solicitação de redirecionamento após método post
     *
     * @var string Status da solicitação de redirecionamento após método post
     */
    case SEE_OTHER    = 'Redirecionamento após post';

    /**
     * Código das opções
     *
     * @return int
     */
    public function codigo(): int{

        //Retornando o código
        return match( $this ){

            //Status correto
            self::OK                    => 200,
            //Movido permanentemente
            self::MOVED_PERMANENTLY     => 301,
            //Redireiconamento encontrado
            self::FOUND                 => 302,
            //Movido permanentemente
            self::SEE_OTHER             => 303,
            //Redirecionamento temporário
            self::TEMPORARY_REDIRECT    => 307,
            //Status incorreto
            self::BAD_REQUEST           => 400,
            //Status não autorizado
            self::UNAUTHORIZED          => 401,
            //Sem permissão
            self::FORBIDDEN             => 403,
            //Status não encontrado
            self::NOT_FOUND             => 404,
            //Status duplicado
            self::CONFLICT              => 409,
            //Status sem serviço
            self::SERVICE_UNAVAILABLE   => 503 };

    }

}