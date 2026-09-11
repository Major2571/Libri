<?php
//Definindo o namespace
namespace App\Infraestrutura\Enum\Mensagem;

//Definindo as classes usadas
use App\Traits\Mensagem;

/**
 * Enum que representa as mensagens de conteúdo
 *
 * @author Caroline Tacats <caroline.tacats62@gmail.com>
 * @package App\Infraestrutura\Enum\Mensagem
 */
enum Conteudo: string
{

    //Definindo a trait das mensagens
    use Mensagem;

    /**
     * Mensagem de erro padrão
     *
     * @var string Mensagem de erro padrão
     */
    case PADRAO = 'Erro desconhecido. Por favor, entre em contato com o administrador da página.';

    /**
     * Mensagem de erro do tipo de requisição
     *
     * @var string Mensagem de erro do tipo de requisição
     */
    case TIPO_REQUISICAO = 'O método não aceita esse tipo de requisição (%s). Somente requisições do tipo (%s).';

    /**
     * Mensagem de erro do modelo sem campos
     *
     * @var string Mensagem de erro do modelo sem campos
     */
    case MODELO_SEM_CAMPOS = 'Os campos do modelo não são válidos. Por favor, entre em contato com o administrador da página.';

    /**
     * Mensagem de erro do modelo inexistente
     *
     * @var string Mensagem de erro do modelo inexistente
     */
    case MODELO_INEXISTENTE = 'O modelo("%s") passado é inválido. Por favor, entre em contato com o administrador da página.';

    /**
     * Mensagem de erro do token inválido
     *
     * @var string Mensagem de erro do token inválido
     */
    case TOKEN_INVALIDO = 'O token passado não é válido. Por favor, entre em contato com o administrador da página.';

    /**
     * Mensagem de erro da sessão inexistente
     *
     * @var string Mensagem de erro da sessão inexistente
     */
    case SESSAO_INEXISTENTE = 'A sessão do usuário é inexistente. Por favor, entre em contato com o administrador da página.';

    /**
     * Mensagem de erro caso o conteúdo já exista por login
     *
     * @var string Mensagem de erro caso o conteúdo já exista por login
     */
    case JA_EXISTE_POR_LOGIN = 'Já existe um usuário com este login.';

    /**
     * Mensagem de erro caso o conteúdo já exista por email
     *
     * @var string Mensagem de erro caso o conteúdo já exista por email
     */
    case JA_EXISTE_POR_EMAIL = 'Este e-mail já está em uso.';

    /**
     * Mensagem de erro caso o conteúdo já exista por documento
     *
     * @var string Mensagem de erro caso o conteúdo já exista por documento
     */
    case JA_EXISTE_POR_DOCUMENTO = 'Este CPF já está vinculado a outra conta.';

    /**
     * Mensagem de erro de sem autorização
     *
     * @var string Mensagem de erro de sem autorização
     */
    case SEM_AUTORIZACAO = 'Você não está autorizado a executar essa ação. Por favor, entre em contato com o administrador da página.';

    /**
     * Mensagem de erro da tradução não disponível
     *
     * @var string Mensagem de erro da tradução não disponível
     */
    case TRADUCAO_NAO_DISPONIVEL = 'A língua("%s") escolhida para tradução não está disponível. Por favor, escolha entre as opções.';

    /**
     * Mensagem de erro para nenhum conteúdo encontrado
     *
     * @var string Mensagem de erro para nenhum conteúdo encontrado
     */
    case NENHUM_CONTEUDO_ENCONTRADO = 'Nenhum conteúdo(%s) encontrado.';

    /**
     * Mensagem de erro para nenhum conteúdo encontrado
     *
     * @var string Mensagem de erro para nenhum conteúdo encontrado
     **/
    case SENHA_INCORRETA = 'Sua senha está incorreta. Por favor, tente novamente.';

    /**
     * Mensagem de erro para senha atual incorreta
     *
     * @var string Mensagem de erro para senha atual incorreta
     */
    case SENHA_ATUAL_INCORRETA = 'A senha atual informada está incorreta. Por favor, tente novamente.';

    /**
     * Mensagem de erro para email não encontrado
     *
     * @var string Mensagem de erro para email não encontrado
     */
    case EMAIL_NAO_ENCONTRADO = 'E-mail não encontrado. Por favor, tente novamente.';

    /**
     * Mensagem de erro para autenticação
     *
     * @var string Mensagem de erro para autenticação
     */
    case AUTENTICACAO = 'Usuário não encontrado com este e-mail e/ou senha. Por favor, tente novamente.';

    /**
     * Mensagem de erro para inscrição já existente por documento
     *
     * @var string Mensagem de erro para inscrição já existente por documento
     */
    case JA_EXISTE_INSCRICAO_POR_DOCUMENTO = 'Já existe uma inscrição para este evento com o mesmo CPF. Verifique a página de "Meus Eventos" para mais detalhes.';
    
    /**
     * Mensagem de erro para não há vagas disponíveis
     *
     * @var string Mensagem de erro para não há vagas disponíveis
     */
    case NAO_HA_VAGAS_DISPONIVEIS = 'Não há vagas disponíveis para este horário. Por favor, escolha outro horário.';

    /**
     * Mensagem de erro para nova senha igual à atual
     *
     * @var string Mensagem de erro para nova senha igual à atual
     */
    case SENHA_NOVA_IGUAL_ATUAL = 'A nova senha não pode ser igual à senha atual. Por favor, escolha uma senha diferente.';

    /**
     * Mensagem de erro para senha nova e confirmação de senha não coincidirem
     *
     * @var string Mensagem de erro para senha nova e confirmação de senha não coincidirem
     */
    case SENHA_NAO_COINCIDE = 'As senhas passadas não coincidem. Por favor, tente novamente.';

    /**
     * Código das opções
     *
     * @return int
     */
    public function codigo(): int
    {

        //Retornando o código
        return match ($this) {

                //Erro padrão
            self::PADRAO => 3000,
                //Erro do tipo de requisição
            self::TIPO_REQUISICAO => 3001,
                //Erro do modelo sem campos
            self::MODELO_SEM_CAMPOS => 3002,
                //Erro do modelo inexistente
            self::MODELO_INEXISTENTE => 3003,
                //Erro do token inválido
            self::TOKEN_INVALIDO => 3004,
                //Erro da sessão inexistente
            self::SESSAO_INEXISTENTE => 3005,
                //Erro caso o login já exista
            self::JA_EXISTE_POR_LOGIN => 3006,
                //Erro de sem autorização
            self::SEM_AUTORIZACAO => 3007,
                //Erro da tradução não disponível
            self::TRADUCAO_NAO_DISPONIVEL => 3008,
                //Erro de nenhum conteúdo encontrado
            self::NENHUM_CONTEUDO_ENCONTRADO => 3009,
                //Erro de inscrição já existente por documento
            self::JA_EXISTE_INSCRICAO_POR_DOCUMENTO => 3010
        };

    }

}