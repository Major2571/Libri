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

    $template->autores_ativo = 'active open';
    $template->ver_todos_autores_ativo = 'active';
    $template->MODELO = $pagina->modelo_formatado;

} catch (Exception $e) {
}

try {

    $fachadaAutor = Fachada::instancia('Autor')->configurar();

    $autor = $fachadaAutor->procurarPorIdentificador($pagina->id);

    $generosSelecionados = array_map('intval', $autor->generos ?? []);

    $template->AUTOR = $autor;

    $template->ATIVO = ((int) $autor->status === Status::ATIVO->value) ? 'selected' : '';
    $template->INATIVO = ((int) $autor->status === Status::INATIVO->value) ? 'selected' : '';

    $template->IMAGEM_AUTOR = $autor->imagem ?
        $pagina->config('url.repositorio') . '/autores/' . $autor->imagem :
        $pagina->config('url.imagem') . '/avatars/profile.png';

    $generos = Fachada::instancia('Genero\Genero')->listarOrdenado('nome');

    if ($generos) {
        foreach ($generos as $gen) {
            $template->GENERO = $gen;
            $template->genero_selecionado = in_array((int) $gen->id, $generosSelecionados, true) ? 'selected' : '';
            $template->block('BLOCO_GENEROS');
        }
    }

} catch (Exception $e) {

    $pagina->definir404();
    require_once $pagina->caminho;
    exit;
    
}

//Exibindo o template
$template->show();