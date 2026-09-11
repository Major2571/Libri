<?php
//Definindo the namespace
namespace App\Util;

//Definindo as classes usadas
use App\Infraestrutura\Configuracao;
use App\Infraestrutura\Base;

/**
 * Classe de paginação
 * Herdada da classe base para auxílio nas chamadas
 *
 * @author Rafael Cardoso <rafus0@hotmail.com>
 * @author Caroline Tacats <caroline.tacats62@gmail.com>
 * @package App\Util
 */
class Paginacao extends Base{

	/**
	 * Método construtor da classe
	 *
	 * @param int $offset Offset da paginação
	 * @param null|int $conteudosPorPagina Conteúdos por página
	 * @param int $quantidade Quantidade de conteúdos
	 * @param null|string $diretorio
	 * @param null|string $termo
	 * @uses App\Infraestrutura\Configuracao::get() Retornando a variável do projeto
	 */
	public function __construct(
		int $offset 				= 0,
		?int $conteudosPorPagina	= null,
		int $quantidade				= 0,
		?string $diretorio 			= null,
		?string $termo 				= null ){

		//Definindo a página atual
		$this->paginaAtual       	= max( 1, (int) ceil( $offset / $conteudosPorPagina ) + 1 );

		//Definindo a quantidade de conteúdos por página
		$this->conteudosPorPagina	= $conteudosPorPagina ?? Configuracao::get( 'projeto.quantidade_paginacao' );

		//Definindo a quantidade total
		$this->quantidadeTotal     	= $quantidade;

		//Definindo o diretório
		$this->diretorio           	= $diretorio ?? '';

		//Definindo o termo de busca
		$this->termoBusca          	= $termo ? "?{$termo}" : null;

		//Definindo a quantidade total de páginas
		$this->quantidadePaginas	= (int) ceil( $this->quantidadeTotal / $this->conteudosPorPagina );

		//Definindo a página anterior
		$this->paginaAnterior     	= max( 1, $this->paginaAtual - 1 );

		//Definindo a próxima páginas
		$this->proximaPagina      	= min( $this->quantidadePaginas, $this->paginaAtual + 1 );

		//Definindo a primeira página
		$this->primeiraPagina		= ( $this->paginaAtual > 4 && $this->quantidadePaginas > 9 ) ? min( $this->paginaAtual - 4, $this->quantidadePaginas - 8 ) : 1;

		//Criando a paginação
		$this->criarPaginacao();

	}

	/**
	 * Criando a paginação
	 *
	 * @access private
	 * @return void
	 */
	private function criarPaginacao(): void{

		//Definindo a última página
		$ultimaPagina	= min( $this->primeiraPagina + 8, $this->quantidadePaginas );

		//Definindo a URL da imagem
		$imagem	= Configuracao::get( 'url.imagem' );

		//Definindo o HTML
		$html	= '';

		//Verificando a página
		if( $this->paginaAtual > 1 )
			//Criando o link da página anterior
			$html	.= $this->criarLink( $this->paginaAnterior, "{$imagem}/svg/left-arrow-page.svg" );

		//Definindo a página atual
		$html	.= '<div class="pagination-namber"><span class="active">' . $this->paginaAtual . '</span><span>/' . $this->quantidadePaginas . '</span></div>';

		//Verificando
		if( $this->paginaAtual < $ultimaPagina )
			//Criando o link da próxima página
			$html	.= $this->criarLink( $this->proximaPagina, "{$imagem}/svg/right-arrow-page.svg" );

		//Definindo o HTML
		$this->html	= $html;

	}

	/**
	 * Criando o link junto com o HTML
	 *
	 * @access private
	 * @param int $pagina Página
	 * @param string $imagem URL da imagem
	 * @return string
	 */
	private function criarLink( int $pagina, string $imagem ): string{

		//Definindo a URL
		$url= $this->definirURL( $pagina );

		//Retornando o HTML
		return "<a href='{$url}'>
					<img src='{$imagem}'>
				</a>";

	}

	/**
	 * Definindo a URL
	 *
	 * @access private
	 * @param int $pagina Página
	 * @return string
	 */
	private function definirURL( int $pagina ): string{

		//Definindo e retornando a página
		return Configuracao::get( 'url.padrao' ) . "/{$this->diretorio}/pagina/{$pagina}{$this->termoBusca}";

	}

}