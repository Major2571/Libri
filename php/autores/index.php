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

    $autores = Fachada::instancia('Autor')->configurar(excecao: false)->listarOrdenado();

    if ($autores) {

        $template->TOTAL = count($autores);
        $template->block('EXIBIR_TOTAL');

        foreach ($autores as $autor) {

            $template->AUTOR = $autor;

            $template->IMAGEM = $autor->imagem ?
                $pagina->config('url.repositorio') . '/autores/' . $autor->imagem :
                $pagina->config('url.imagem') . '/avatars/profile.png';

            $template->STATUS = Util::formatarLabel($autor->status, 'status');

            if ((int) $autor->status === Status::ATIVO->value) {
                $template->block('BLOCO_BTN_INATIVAR');
            } else {
                $template->block('BLOCO_BTN_ATIVAR');
            }

            $template->block('BLOCO_AUTORES');
            $template->block('BLOCO_AUTORES_MODAL');
        }

    }

} catch (Exception $e) {
    print_r($e->getMessage());
    die;
}

//Exibindo o template
$template->show();