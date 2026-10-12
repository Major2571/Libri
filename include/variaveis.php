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
    $template->addFile('INCLUDE_CABECALHO', "{$dirHtml}/include/sistema/cabecalho.html");

    //Incluindo o topo
    $template->addFile('INCLUDE_TOPO', "{$dirHtml}/include/sistema/topo.html");

    //Incluindo a barra de navegação
    $template->addFile('INCLUDE_NAVBAR', "{$dirHtml}/include/sistema/navbar.html");

    //Incluindo o rodapé
    $template->addFile('INCLUDE_RODAPE', "{$dirHtml}/include/sistema/rodape.html");

    //Incluindo o script
    $template->addFile('INCLUDE_JAVASCRIPT', "{$dirHtml}/include/sistema/javascript.html");

    //Definindo o ambiente para produção ou desenvolvimento
    Util::definirAmbiente($template, $pagina);

} catch (Exception $e) {
}

//Atalhos
try {

    //Verificando se não está no painel
    if ($pagina->diretorio) {

        //Definindo os atalhos
        $atalho = Util::definirAtalho($pagina);

        $template->TITULO_DA_PAGINA = ($pagina->diretorio == 'dashboard') ? ' Dashboard ' : $atalho->atalho . ' - ' . $atalho->titulo;

    }

} catch (Exception $e) {
}

//Atalhos
try {

    //Verificando se não está no painel
    if ($pagina->diretorio != 'dahsboard') {

        //Incluindo o script
        $template->addFile('INCLUDE_ATALHO', "{$dirHtml}/include/sistema/atalho.html");

        //Definindo os atalhos
        $atalho = Util::definirAtalho($pagina);

        $template->TITULO_DO_ATALHO = $atalho->titulo;
        $template->DIRETORIO = $atalho->diretorio;
        $template->ATALHO = $atalho->atalho;
        $template->MODELO = $pagina->modelo_formatado;
    }

} catch (Exception $e) {
}

// Menu
try {

    $diretorio = $pagina->diretorio;
    $acao = $pagina->acao;

    $itensMenu = [
        [
            'diretorio' => 'autores',
            'titulo'    => 'Autores',
            'icone'     => 'bx-user'
        ],
        [
            'diretorio' => 'generos',
            'titulo'    => 'Gêneros',
            'icone'     => 'bx-bookmark-alt'
        ],
        [
            'diretorio' => 'livros',
            'titulo'    => 'Livros',
            'icone' => 'bx-book'
        ],
    ];

    $template->dashboard_ativo = ($diretorio == 'dashboard') ? 'active' : '';

    foreach ($itensMenu as $item) {

        $atual = ($item['diretorio'] == $diretorio);

        $menu               = new stdClass();
        $menu->diretorio    = $item['diretorio'];
        $menu->titulo       = $item['titulo'];
        $menu->icone        = $item['icone'];
        $menu->ativo        = ($atual && in_array($acao, ['index', 'novo', 'editar'])) ? 'active open' : '';
        $menu->novo_ativo   = ($atual && $acao == 'novo') ? 'active' : '';
        $menu->todos_ativo  = ($atual && in_array($acao, ['index', 'editar'])) ? 'active' : '';

        $template->MENU = $menu;
        $template->block("BLOCO_MENU");

    }

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