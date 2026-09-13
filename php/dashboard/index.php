<?php
/**
 * Exibindo a página de cadastro de usuário
 */

//Definindo as classes
use App\Infraestrutura\Template;
use App\Infraestrutura\Configuracao;

// Definindo a página
try {

    // Definindo o objeto
    $template = new Template($pagina);

    // Incluindo as variáveis
    require_once(Configuracao::get('dir.include') . '/variaveis.php');

    //Incluindo a barra de navegação
    $template->addFile('INCLUDE_MANUTENCAO', "{$dirHtml}/include/sistema/manutencao.html");

    $template->dashboard_ativo = 'active';

} catch (Exception $e) {
}

//Exibindo o template
$template->show();