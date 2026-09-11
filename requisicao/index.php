<?php
/*
 * Arquivo de requisição
 * Todas as chamadas vão passar por esse arquivo
 */

//Autoload dos arquivo
require_once( '../vendor/autoload.php' );

//Definindo as classes usadas
use App\Infraestrutura\Pagina;

//Iniciando a página
$pagina	= new Pagina( $_GET );

//Incluindo a página
require_once( $pagina->caminho );