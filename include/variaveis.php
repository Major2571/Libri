<?php
/*
 * Variáveis do template
 */

//Definindo as classes usadas
use App\Util\Util;

//Incluindo os arquivos
try {

    //Definindo o diretório do HTML
    $dirHtml = $pagina->config('dir.html');

    //Incluindo o cabeçalho
    $template->addFile('INCLUDE_CABECALHO', "{$dirHtml}/include/cabecalho.html");

    //Incluindo o topo
    $template->addFile('INCLUDE_TOPO', "{$dirHtml}/include/topo.html");

    //Incluindo o rodapé
    $template->addFile('INCLUDE_RODAPE', "{$dirHtml}/include/rodape.html");

    //Incluindo o script
    $template->addFile('INCLUDE_JAVASCRIPT', "{$dirHtml}/include/javascript.html");

    //Definindo o ambiente para produção ou desenvolvimento
    Util::definirAmbiente($template, $pagina);

} catch (Exception $e) {
}

//Definindo algumas variáveis
$template->URL_VENDOR = $pagina->config('url.vendor');
$template->URL_CSS = $pagina->config('url.css');
$template->URL_JS = $pagina->config('url.js');
$template->URL = $pagina->config('url.padrao');
$template->URL_IMG = $pagina->config('url.imagem');
$template->DATA_ATUAL = date('Y');
$template->CSRF_TOKEN = Util::definirTokenCsrf();