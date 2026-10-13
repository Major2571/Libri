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

    $nacionalidades = Fachada::instancia('Nacionalidade')->configurar(excecao: false)->listarOrdenado('gentilico_pt');

    if ($nacionalidades) {

        foreach ($nacionalidades as $nacionalidade) {
            $template->NACIONALIDADE = $nacionalidade;
            $template->block('BLOCO_NACIONALIDADES');
        }

    }

}catch (Exception $e) {
    print_r($e->getMessage());
}

//Exibindo o template
$template->show();