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
    $template->addFile('INCLUDE_CABECALHO', "{$dirHtml}/include/cabecalho.html");
    //Incluindo o topo
    $template->addFile('INCLUDE_TOPO', "{$dirHtml}/include/topo.html");

    //Incluindo o rodapé
    $template->addFile('INCLUDE_RODAPE', "{$dirHtml}/include/rodape.html");

    //Definindo o ambiente para produção ou desenvolvimento
    Util::definirAmbiente($template, $pagina);

} catch (Exception $e) {
}

try {

    //Verificando
    if (!is_null($pagina->cache))
        //Exibindo o bundle da página
        $template->BUNDLE_PAGINA = $pagina->cache;

} catch (Exception $e) {
}

//Definindo algumas variáveis
$template->URL_VENDOR   = $pagina->config('url.vendor');
$template->URL_CSS          = $pagina->config('url.css');
$template->URL_CSS_VENDOR   = $pagina->config('url.css_vendor');
$template->URL_IMG          = $pagina->config('url.imagem');

//Exibindo o template
$template->show();