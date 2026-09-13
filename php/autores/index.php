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
    $template->ver_todos_autores_ativo = 'active';

} catch (Exception $e) {
}

try {

    $autores = Fachada::instancia('Autor')->configurar(excecao: false)->listarOrdenado();

    if ($autores) {

        $template->TOTAL = count($autores);
        $template->block('EXIBIR_TOTAL');

        foreach ($autores as $autor) {
            $template->AUTOR = $autor;
            $template->block('BLOCO_AUTORES');
            $template->block('BLOCO_AUTORES_MODAL');
        }

    }

} catch (Exception $e) {
}

//Exibindo o template
$template->show();