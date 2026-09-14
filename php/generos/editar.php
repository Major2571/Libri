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

    $template->generos_ativo = 'active open';
    $template->ver_todos_genero_ativo = 'active';

} catch (Exception $e) {
}

try {

    $fachadaGenero = Fachada::instancia('Genero')->configurar(complemento: false);

    $genero = $fachadaGenero->procurarPorIdentificador($pagina->id);

    $template->GENERO = $genero;

    $generos = $fachadaGenero->listarOrdenado('nome');

    if ($generos) {
        foreach ($generos as $gen) {
            
            if (!is_null($gen->genero_pai))
                continue;

            $template->GENERO_SELECT = $gen;
            $template->genero_selecionado = ($gen->id == $genero->genero_pai) ? 'selected' : '';
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