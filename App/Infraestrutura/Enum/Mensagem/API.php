<?php
//Definindo o namespace
namespace App\Infraestrutura\Enum\Mensagem;

//Definindo as classes usadas
use App\Traits\Mensagem;

/**
 * Enum que representa as mensagens de API
 *
 * @author Caroline Tacats <caroline.tacats62@gmail.com>
 * @package App\Infraestrutura\Enum\Mensagem
 */
enum API: string{

    //Definindo a trait usada
    use Mensagem;

	/**
     * Mensagem de erro padrão
     *
     * @var string Mensagem de erro padrão
     */
	case PADRAO = 'Algum erro foi encontrado. Por favor, entre em contato com o administrador do sistema.';

    /**
     * Mensagem de erro para a verificação do domínio
     *
     * @var string Mensagem de erro para a verificação do domínio
     */
	case VERIFICAR_DOMINIO  = 'Não foi possível verificar o registro do domínio. Por favor, tente novamente mais tarde.';

    /**
     * Mensagem de erro de autenticação
     *
     * @var string Mensagem de erro de autenticação
     */
	case AUTENTICACAO   = 'Não foi possível realizar a autenticação do usuário. Por favor, tente novamente mais tarde.';

    /**
     * Mensagem de erro para o esqueci minha senha
     *
     * @var string Mensagem de erro para o esqueci minha senha
     */
	case ESQUECI_MINHA_SENHA    = 'Não foi possível realizar a redefinição da senha do usuário. Por favor, tente novamente mais tarde.';

    /**
     * Mensagem de erro para a verificação do login
     *
     * @var string Mensagem de erro para a verificação do login
     */
	case VERIFICAR_LOGIN  = 'Não foi possível realizar a verificação do login do usuário. Por favor, tente novamente mais tarde.';

    /**
     * Mensagem de erro para o cadastro
     *
     * @var string Mensagem de erro para o cadastro
     */
	case CADASTRO   = 'Não foi possível realizar o cadastro do cliente. Por favor, tente novamente mais tarde.';

    /**
     * Código das opções
     *
     * @return int
     */
    public function codigo(): int{

        //Retornando o código
        return match( $this ){

            //Erro padrão
            self::PADRAO                => 1000,
            //Erro de verificação do domínio
            self::VERIFICAR_DOMINIO     => 1001,
            //Erro de autenticação
            self::AUTENTICACAO          => 1002,
            //Erro de esqueci minha senha
            self::ESQUECI_MINHA_SENHA   => 1003,
            //Erro da verificação do login
            self::VERIFICAR_LOGIN       => 1004,
            //Erro do cadastro
            self::CADASTRO              => 1005 };

    }

}