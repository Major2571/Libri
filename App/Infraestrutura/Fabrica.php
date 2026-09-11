<?php
//Definindo o namespace
namespace App\Infraestrutura;

//Definindo as classes usadas
use App\Infraestrutura\Conexao\BDR\Conexao as ConexaoBDR;
use App\Excecao\Conteudo as ConteudoException;
use App\Infraestrutura\Enum\Mensagem\Conteudo;
use App\Infraestrutura\Internacionalizacao;
use App\Infraestrutura\Enum\Status\Http;
use App\Infraestrutura\Configuracao;
use App\Infraestrutura\Base;
use stdClass;

/**
 * Classe da fábrica.
 * Todas as instâncias dos controladores e repositórios são passadas por aqui
 * Herdada da classe base para auxílio nas chamadas
 *
 * @final
 * @author Lucas Dantas <lucas@mvoeon.dev>
 * @package App\Infraestrutura
 * @property \App\Modelo\Modelo $modelo Classe base do modelo
 */
final class Fabrica extends Base{

	/**
	 * Método construtor da classe
	 *
	 * @param object|string $modelo Modelo
	 * @param string implementacao Tipo de implementação
	 * @uses App\Infraestrutura\Configuracao::get() Retornando a variável do projeto
	 * @uses App\Infraestutura\Internacionalizacao\Internacionalizacao::dominio() Definindo o domínio
	 * @return void
	 * @throws ConteudoException
	 */
	public function __construct(
		public object|string $modelo,
		public string $implementacao = 'BDR' ){

		//Definindo os nomes das classes
        $repositorio	= $this->formatarRepositorio();
        $controlador	= "App\Controlador\\{$this->modelo}";
        $modelo			= "App\Modelo\\{$this->modelo}";

		//Verificando
        if( !class_exists( $modelo ) )
            //Lançando a exceção
            throw new ConteudoException( sprintf( Conteudo::MODELO_INEXISTENTE->texto(), $modelo ), Http::BAD_REQUEST->codigo() );

		$this->modelo		= $modelo;

		//Definindo o controlador
		$this->controlador	= !class_exists( $controlador ) ? 'App\Controlador\Principal\Controlador' : $controlador;

		//Definindo o repositório
		$this->repositorio	= !class_exists( $repositorio ) ? "App\Repositorio\\{$this->implementacao}\Principal\Repositorio" : $repositorio;

		//Definindo os atributos
		$this->definirAtributos();

		//Verificando
		if( Configuracao::get( 'projeto.internacionalizacao' ) )
			//Definindo o domínio
			Internacionalizacao::dominio( $this->modelo->configuracao[ 'dominio' ] ?? null );

	}

	/**
	 * Formatando o repositório
	 *
	 * @access private
	 * @return string
	 */
	private function formatarRepositorio(): string{

		//Retornando o repositório
		return match( $this->implementacao ){

			//Padrão
			default	=> "App\Repositorio\\{$this->implementacao}\\{$this->modelo}" };

	}

	/**
	 * Definindo os atributos da fábrica
	 *
	 * @access private
	 * @uses App\Repositorio\Repositorio::instancia() Definindo a instância
	 * @return void
	 */
	private function definirAtributos(): void{

		//Definindo o nome do modelo
		$nomeClasse					= $this->modelo;

		//Definindo o model
		$this->modelo				= new $this->modelo();

		//Criando o objeto
		$objeto						= new stdClass();

		//Definindo as configurações
		$objeto->tipoRepositorio	= $this->modelo->tipoRepositorio;
		$objeto->nomeModelo			= $this->modelo->configuracao[ 'nome' ];
		$objeto->nomeClasse			= $nomeClasse;

		//Definindo as configurações
		$this->configuracao			= $objeto;

		//Definindo a conexão
		$this->conexao				= $this->definirConexao();

		//Definindo o repositório
		$this->repositorio			= $this->repositorio::instancia( $this );

		//Definindo o controlador
		$this->controlador			= new $this->controlador( $this->repositorio, $objeto );

	}


	/**
	 * Definindo a conexão de acordo com a implementação
	 *
	 * @access private
	 * @uses App\Infraestrutura\Conexao\BDR\Conexao::instancia() Definindo a instância
	 * @return object
	 */
	private function definirConexao(): object{

		//Retornando a conexão
		return match( $this->implementacao ){

			//Padrão
			default	=> ConexaoBDR::instancia() };

	}

}