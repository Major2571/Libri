<?php
/**
 * Exibindo a página 404
 */

//Definindo as classes
use App\Infraestrutura\Template;
use App\Util\Util;

// Definindo a página
try {

    // Definindo o objeto
    $template = new Template($pagina);

    //Definindo o diretório do HTML
    $dirHtml = $pagina->config('dir.html');

    //Incluindo o cabeçalho
    $template->addFile('INCLUDE_CABECALHO', "{$dirHtml}/include/sistema/cabecalho.html");

    //Incluindo o topo
    $template->addFile('INCLUDE_MENU', "{$dirHtml}/include/institucional/menu.html");

    //Incluindo o rodapé
    $template->addFile('INCLUDE_RODAPE', "{$dirHtml}/include/institucional/rodape.html");

    // Modais
    $template->addFile('INCLUDE_MODAL_LOGIN', "{$dirHtml}/include/modais/login.html");
    $template->addFile('INCLUDE_MODAL_CADASTRO', "{$dirHtml}/include/modais/cadastro.html");

    //Definindo o ambiente para produção ou desenvolvimento
    Util::definirAmbiente($template, $pagina);

} catch (Exception $e) {
}

//Definindo algumas variáveis
$template->URL_VENDOR       = $pagina->config('url.vendor');
$template->URL_IMG          = $pagina->config('url.imagem');
$template->URL              = $pagina->config('url.padrao');
$template->DATA_ATUAL       = date('Y');

//Exibindo o template
$template->show();