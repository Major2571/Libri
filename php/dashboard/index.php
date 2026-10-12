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

} catch (Exception $e) {
}

try{

    $template->SECTION_CARDS = '';
    $template->TOTAL_LIVROS = Fachada::instancia('Livro')->contar();
    $template->TOTAL_AUTORES = Fachada::instancia('Autor')->contar();
    $template->TOTAL_GENEROS = Fachada::instancia('Genero\Genero')->contar();


} catch (Exception $e) {

    $template->SECTION_CARDS = 'd-none';

    $template->addFile('INCLUDE_MANUTENCAO', "{$dirHtml}/include/sistema/manutencao.html");

}

//Exibindo o template
$template->show();