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

    $template->livros_ativo = 'active open';
    $template->novo_livro_ativo = 'active';

} catch (Exception $e) {
}

try {

    $generos = Fachada::instancia('Genero\Genero')->configurar(excecao: false)->listarOrdenado('nome');

    if ($generos) {

        foreach ($generos as $genero) {
            $template->GENERO = $genero;
            $template->block('BLOCO_GENEROS');
        }

    }

}catch (Exception $e) {
}

try {

    $autores = Fachada::instancia('Autor')->configurar(excecao: false)->listarOrdenado('nome');

    if ($autores) {

        foreach ($autores as $autor) {
            $template->AUTOR = $autor;
            $template->block('BLOCO_AUTORES');
        }

    }

}catch (Exception $e) {
}

//Exibindo o template
$template->show();