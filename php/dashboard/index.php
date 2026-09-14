<?php
/**
 * Exibindo a página de cadastro de usuário
 */

//Definindo as classes
use App\Infraestrutura\Template;
use App\Infraestrutura\Configuracao;
use App\Infraestrutura\Fachada;

// Definindo a página
try {

    // Definindo o objeto
    $template = new Template($pagina);

    // Incluindo as variáveis
    require_once(Configuracao::get('dir.include') . '/variaveis.php');

    //Incluindo a barra de navegação
    // $template->addFile('INCLUDE_MANUTENCAO', "{$dirHtml}/include/sistema/manutencao.html");

    $template->dashboard_ativo = 'active';

} catch (Exception $e) {
}

try{

    // $template->TOTAL_LIVROS = Fachada::instancia('Livro')->contar();
    $template->TOTAL_AUTORES = Fachada::instancia('Autor')->contar();
    $template->TOTAL_GENEROS = Fachada::instancia('Genero')->contar();


} catch (Exception $e) {
    $template->addFile('INCLUDE_MANUTENCAO', "{$dirHtml}/include/sistema/manutencao.html");
}

//Exibindo o template
$template->show();