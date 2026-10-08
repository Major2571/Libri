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

    $template->livros_ativo = 'active open';
    $template->ver_todos_livros_ativo = 'active';
    $template->MODELO = $pagina->modelo_formatado;

} catch (Exception $e) {
}

try {

    $livros = Fachada::instancia('Livro')->configurar(excecao: false)->listarOrdenado('titulo');

    if ($livros) {

        $template->TOTAL_LIVROS = count($livros);
        $template->block('EXIBIR_TOTAL_LIVROS');

        foreach ($livros as $livro) {

            $template->LIVRO = $livro;

            $template->IMAGEM = $livro->imagem ?
                $pagina->config('url.repositorio') . '/livros/' . $livro->imagem :
                $pagina->config('url.imagem') . '/avatars/profile.png';

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
    print_r($e->getMessage());
    die;
}

//Exibindo o template
$template->show();