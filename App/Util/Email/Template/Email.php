<?php
//Definindo o namespace
namespace App\Util\Email\Template;

//Definindo as classes usadas
use raelgc\view\Template as RaelTemplate;
use App\Infraestrutura\Configuracao;
use App\Util\Util;

/**
 * Classe dos templates do email
 *
 * @final
 * @author Caroline Tacats <caroline.tacats62@gmail.com>
 * @package App\Util\Email\Template
 **/
final class Email
{

    /**
     * Template de email para o esqueci minha senha
     *
     * @access public
     * @param object $objeto Objeto do cliente
     * @uses App\Infraestrutura\Configuracao\Configuracao::get() Retornando a variável do projeto
     * @return string
     */
    public static function esqueciMinhaSenha(object $objeto, string $senha): string
    {

        //Definindo o template
        $template = new RaelTemplate(Configuracao::get('dir.html') . "/email/usuario/esqueci-minha-senha.html", false);

        //Definindo algumas variáveis
        $template->OBJETO       = $objeto;
        $template->SENHA        = $senha;
        $template->NOME_PROJETO = Configuracao::get('projeto.nome');
        $template->URL_IMG      = Configuracao::get('url.imagem');
        $template->ANO_ATUAL    = date('Y');

        //Retornando
        return $template->parse();

    }

    /**
     * Template de email para o novo usuário
     *
     * @access public
     * @param object $objeto Objeto do cliente
     * @uses App\Infraestrutura\Configuracao\Configuracao::get() Retornando a variável do projeto
     * @return string
     */
    public static function novoUsuario(object $objeto, string $senha): string
    {

        //Definindo o template
        $template = new RaelTemplate(Configuracao::get('dir.html') . "/email/usuario/novo-usuario.html", false);

        //Definindo algumas variáveis
        $template->OBJETO       = $objeto;
        $template->SENHA        = $senha;
        $template->NOME_PROJETO = Configuracao::get('projeto.nome');
        $template->URL_IMG      = Configuracao::get('url.imagem');
        $template->ANO_ATUAL    = date('Y');

        //Retornando
        return $template->parse();

    }

    /**
     * Template de email para a inscrição em evento
     *
     * @access public
     * @param object $objeto Objeto do cliente
     * @uses App\Infraestrutura\Configuracao\Configuracao::get() Retornando a variável do projeto
     * @return string
     */
    public static function inscricaoEvento(object $objeto, ?object $agenda): string
    {

        //Definindo o template
        $template = new RaelTemplate(Configuracao::get('dir.html') . "/email/evento/inscricao.html", false);

        //Definindo variáveis do usuário e do evento
        $template->NOME_USUARIO             = $objeto->usuario->nome;
        $template->EVENTO                   = $objeto->evento;
        $template->MENSAGEM_EMAIL           = $objeto->evento->mensagem_email;
        $template->CODIGO                   = $objeto->codigo;
        $template->IDENTIFICADOR_USUARIO    = Util::gerarHashParaIdentificador($objeto->id, Configuracao::get('evento.hash'));

        // Verificando se existe uma agenda associada para exibir o horário da inscrição
        if ($agenda) {
            $template->HORARIO = $agenda->dia . ' às ' . $agenda->horario;
            $template->block('AGENDA');
        }

        // Definindo variáveis do projeto
        $template->NOME_PROJETO         = Configuracao::get('projeto.nome');
        $template->URL_IMG              = Configuracao::get('url.imagem');
        $template->URL                  = Configuracao::get('url.padrao');
        $template->ANO_ATUAL            = date('Y');

        //Retornando
        return $template->parse();

    }

}