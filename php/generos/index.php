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

    $generos = Fachada::instancia('Genero\Genero')->configurar(excecao: false)->listarOrdenado();

    if ($generos) {

        $template->TOTAL = count($generos);
        $template->block('EXIBIR_TOTAL');

        foreach ($generos as $genero) {
            $template->GENERO = $genero;
            $template->GENERO_NOME_EDITAR = $genero->nome;
            $template->GENERO_DESCRICAO_EDITAR = $genero->descricao;
            $template->GENERO_PAI_EDITAR = $genero->genero_pai?->id ?? '';
            $template->block('BLOCO_GENEROS');
            
            if (!is_null($genero->genero_pai))
                continue;
            
            $template->block('BLOCO_GENEROS_MODAL');
            $template->block('BLOCO_GENEROS_EDITAR_MODAL');
        }

    }

} catch (Exception $e) {
}

//Exibindo o template
$template->show();