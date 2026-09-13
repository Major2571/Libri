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

    $generos = Fachada::instancia('Genero')->configurar(excecao: false)->listarOrdenado();

    if ($generos) {

        $template->TOTAL = count($generos);
        $template->block('EXIBIR_TOTAL');

        foreach ($generos as $genero) {
            $template->GENERO = $genero;
            $template->block('BLOCO_GENEROS');
            $template->block('BLOCO_GENEROS_MODAL');
        }

    }

} catch (Exception $e) {
}

//Exibindo o template
$template->show();