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

    $template->autores_ativo = 'active open';
    $template->novo_autor_ativo = 'active';

} catch (Exception $e) {
}

try {

    $generos = Fachada::instancia('Genero')->configurar(excecao: false)->listarOrdenado('nome');

    if ($generos) {

        foreach ($generos as $genero) {
            $template->GENERO = $genero;
            $template->block('BLOCO_GENEROS');
        }

    }

}catch (Exception $e) {
}

try {

    $ultimosRegistros = Fachada::instancia('Genero')->configurar(excecao: false)->listarOrdenado('data-criacao-desc');

    if ($ultimosRegistros) {

        $ultimos = array_slice($ultimosRegistros, 0, 5);

        foreach ($ultimos as $item) {

            $template->LISTAGEM = $item;
            $template->block('BLOCO_LISTAGEM');
        }

    }

}catch (Exception $e) {
}

//Exibindo o template
$template->show();