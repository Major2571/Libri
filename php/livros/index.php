<?php
/**
 * Exibindo a página de cadastro de usuário
 */

//Definindo as classes
use App\Infraestrutura\Template;
use App\Infraestrutura\Configuracao;
use App\Infraestrutura\Enum\Status\Status;
use App\Infraestrutura\Fachada;
use App\Util\Util;

// Definindo a página
try {

    // Definindo o objeto
    $template = new Template($pagina);

    // Incluindo as variáveis
    require_once(Configuracao::get('dir.include') . '/variaveis.php');

} catch (Exception $e) {
}

try {

    $livros = Fachada::instancia('Livro')->configurar(excecao: false)->listarOrdenado('titulo');

    if ($livros) {

        $template->TOTAL = count($livros);
        $template->block('EXIBIR_TOTAL');

        foreach ($livros as $livro) {

            $template->LIVRO = $livro;

            $template->IMAGEM = $livro->imagem ?
                $pagina->config('url.repositorio') . '/livros/' . $livro->imagem :
                $pagina->config('url.imagem') . '/books/book.png';

            $template->STATUS = Util::formatarLabel($livro->status, 'status');
            

            if ((int) $livro->status === Status::ATIVO->value) {
                $template->block('BLOCO_BTN_INATIVAR');
            } else {
                $template->block('BLOCO_BTN_ATIVAR');
            }

            $template->block('BLOCO_LIVROS');
        }

    }

} catch (Exception $e) {
}

//Exibindo o template
$template->show();