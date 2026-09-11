<?php
//Definindo o namespace
namespace App\Infraestrutura\Enum\Mensagem;

//Definindo as classes usadas
use App\Traits\Mensagem;

/**
 * Enum que representa as mensagens de validação
 *
 * @author Caroline Tacats <caroline.tacats62@gmail.com>
 * @package App\Infraestrutura\Enum\Mensagem
 */
enum Validacao: string{

    //Definindo a trait usada
    use Mensagem;

    /**
     * Mensagem de erro para o campo vazio
     *
     * @var string Mensagem de erro para o campo vazio
     */
	case VAZIO  = 'O campo("%s") está vazio. Por favor, preencha novamente.';

    /**
     * Mensagem de erro o tamanho inválido
     *
     * @var string Mensagem de erro o tamanho inválido
     */
	case TAMANHO_INVALIDO   = 'O campo("%s") está com o tamanho inválido. Não pode ultrapassar("%s") caractere(s).';

    /**
     * Mensagem de erro para o email inválido
     *
     * @var string Mensagem de erro para o email inválido
     */
	case EMAIL_INVALIDO = 'O e-mail("%s") está inválido.';

    /**
     * Mensagem de erro para o campo não inteiro
     *
     * @var string Mensagem de erro para o campo não inteiro
     */
	case NAO_INTEIRO    = 'O campo("%s") não é numérico.';

    /**
     * Mensagem de erro para o array inválido
     *
     * @var string Mensagem de erro para o array inválido
     */
	case ARRAY_INVALIDO = 'O campo("%s") não é um array válido.';

    /**
     * Mensagem de erro para a latitude inválida
     *
     * @var string Mensagem de erro para a latitude inválida
     */
	case LATITUDE_INVALIDA  = 'A latitude("%s") está inválida.';

    /**
     * Mensagem de erro para a longitude inválida
     *
     * @var string Mensagem de erro para a longitude inválida
     */
	case LONGITUDE_INVALIDA = 'A longitude("%s") está inválida.';

    /**
     * Mensagem de erro para URL inválida
     *
     * @var string Mensagem de erro para URL inválida
     */
	case URL_INVALIDA   = 'A URL("%s") está inválida.';

    /**
     * Mensagem de erro para data inválida
     *
     * @var string Mensagem de erro para data inválida
     */
	case DATA_INVALIDA  = 'A data("%s") está inválida.';

    /**
     * Mensagem de erro para a senha sem um mínimo de caracteres
     *
     * @var string Mensagem de erro para a senha sem um mínimo de caracteres
     */
    case SENHA_MINIMO_CARACTER  = 'A senha deve ter no mínimo 8 caracteres.';

    /**
     * Mensagem de erro para a senha sem número
     *
     * @var string Mensagem de erro para a senha sem número
     */
    case SENHA_SEM_NUMERICO = 'A senha deve conter pelo menos um número.';

    /**
     * Mensagem de erro para a senha sem caracter especial
     *
     * @var string Mensagem de erro para a senha sem caracter especial
     */
    case SENHA_SEM_CARACTERE_ESPECIAL   = 'A senha deve conter pelo menos um caractere especial.';

    /**
     * Mensagem de erro para a senha sem letra
     *
     * @var string Mensagem de erro para a senha sem letra
     */
    case SENHA_SEM_LETRA = 'A senha deve conter pelo menos uma letra.';

    /**
     * Mensagem de erro para as senhas que não conferem
     *
     * @var string Mensagem de erro para as senhas que não conferem
     */
    case SENHA_NAO_CONFERE  = 'As senhas passadas não conferem.';

    /**
     * Mensagem de erro para a senha inválida
     *
     * @var string Mensagem de erro para a senha inválida
     */
    case SENHA_INVALIDA  = 'A senha atual não está correta. Por favor, tente novamente.';

    /**
     * Mensagem de erro para CPF inválido
     *
     * @var string Mensagem de erro para CPF inválido
     */
	case CPF    = 'O CPF("%s") está inválido.';

    /**
     * Mensagem de erro para CNPJ inválido
     *
     * @var string Mensagem de erro para CNPJ inválido
     */
	case CNPJ   = 'O CNPJ("%s") está inválido.';

    /**
     * Código das opções
     *
     * @return int
     */
    public function codigo(): int{

        //Retornando o código
        return match( $this ){

            //Erro de campo vazio
            self::VAZIO                         => 5000,
            //Erro de campo com tamanho inválido
            self::TAMANHO_INVALIDO              => 5001,
            //Erro de email inválido
            self::EMAIL_INVALIDO                => 5002,
            //Erro de campo não inteiro
            self::NAO_INTEIRO                   => 5003,
            //Erro de array inválido
            self::ARRAY_INVALIDO                => 5004,
            //Erro de latitude inválida
            self::LATITUDE_INVALIDA             => 5005,
            //Erro de longitude inválida
            self::LONGITUDE_INVALIDA            => 5006,
            //Erro de URL inválida
            self::URL_INVALIDA                  => 5007,
            //Erro de data inválida
            self::DATA_INVALIDA                 => 5008,
            //Erro da senha sem o mínimo de caracteres
            self::SENHA_MINIMO_CARACTER         => 5009,
            //Erro da senha sem sem número
            self::SENHA_SEM_NUMERICO            => 5010,
            //Erro da senha sem caractere especial
            self::SENHA_SEM_CARACTERE_ESPECIAL  => 5011,
            //Erro da senha sem letra
            self::SENHA_SEM_LETRA               => 5012,
            //Erro da senha que não confere
            self::SENHA_NAO_CONFERE             => 5013,
            //Erro da senha inválida
            self::SENHA_INVALIDA                => 5014,
            //Erro de CPF
            self::CPF                           => 5015,
            //Erro de CNPJ
            self::CNPJ                          => 5016, };

    }

}