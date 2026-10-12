<?php
/**
 * Exibindo a página de cadastro de usuário
 */

//Definindo as classes
use App\Infraestrutura\Template;
use App\Infraestrutura\Configuracao;
use App\Infraestrutura\Enum\Status\Status;
use App\Infraestrutura\Fachada;

// Definindo a página
try {

    // Definindo o objeto
    $template = new Template($pagina);

    // Incluindo as variáveis
    require_once(Configuracao::get('dir.include') . '/variaveis.php');

} catch (Exception $e) {
}

try {

    $fachadaLivro = Fachada::instancia('Livro')->configurar();

    $livro = $fachadaLivro->procurarPorIdentificador($pagina->id);

    $generosSelecionados = array_map('intval', $livro->generos ?? []);
    $autoresSelecionados = array_map('intval', $livro->autores ?? []);

    $template->LIVRO = $livro;

    $template->ATIVO = ((int) $livro->status === Status::ATIVO->value) ? 'selected' : '';
    $template->INATIVO = ((int) $livro->status === Status::INATIVO->value) ? 'selected' : '';

    $template->IMAGEM_LIVRO = $livro->imagem ?
        $pagina->config('url.repositorio') . '/livros/' . $livro->imagem :
        $pagina->config('url.imagem') . '/books/book.png';

    $generos = Fachada::instancia('Genero\Genero')->listarOrdenado('nome');

    if ($generos) {
        foreach ($generos as $gen) {
            $template->GENERO = $gen;
            $template->genero_selecionado = in_array((int) $gen->id, $generosSelecionados, true) ? 'selected' : '';
            $template->block('BLOCO_GENEROS');
        }
    }

    $autores = Fachada::instancia('Autor')->configurar(excecao: false)->listarOrdenado('nome');

    if ($autores) {
        foreach ($autores as $autor) {
            $template->AUTOR = $autor;
            $template->autor_selecionado = in_array((int) $autor->id, $autoresSelecionados, true) ? 'selected' : '';
            $template->block('BLOCO_AUTORES');
        }
    }

} catch (Exception $e) {

    $pagina->definir404();
    require_once $pagina->caminho;
    exit;
    
}

//Exibindo o template
$template->show();