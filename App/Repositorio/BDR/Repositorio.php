<?php
//Definindo o namespace
namespace App\Repositorio\BDR;

//Definindo as classes usadas
use App\Infraestrutura\Enum\Mensagem\Repositorio as EnumRepositorio;
use App\Repositorio\Configuracao as ConfiguracaoRepositorio;
use App\Repositorio\Repositorio as RepositorioPrincipal;
use App\Excecao\BancoDeDados as BancoDeDadosException;
use App\Excecao\Conteudo as ConteudoException;
use App\Infraestrutura\Enum\Mensagem\Conteudo;
use App\Infraestrutura\Configuracao;
use App\Infraestrutura\Enum\Status\Http;
use App\Infraestrutura\Fabrica;
use App\Infraestrutura\Debug;
use App\Modelo\Modelo;
use App\Util\Util;
use DateTime;
use PDOException;
use PDO;

/**
 * Repositório abstrato para Banco de Dados
 * Todas as classes de repositório do projeto irão herdar desse repositório
 *
 * @abstract
 * @author Caroline Tacats <caroline.tacats62@gmail.com>
 * @package App\Repositorio\BDR
 * @property PDO $conexao Conexão PDO
 * @property Debug $debug Debug das queries
 */
abstract class Repositorio extends RepositorioPrincipal{

	/**
	 * Campos para debug
	 *
	 * @access private
	 * @var array Campos para debug
	 */
	private array $campos = [];

	/**
	 * Método construtor da classe
	 *
	 * @param Fabrica $fabrica Objeto da fábrica
	 * @param bool $formatar Formatar os campos ou não
	 * @param bool $remover Remover ou não os campos
	 * @param bool $excecao Exibindo ou não a exceção
	 * @uses App\Infraestrutura\Configuracao\Configuracao::get() Retornando a variável do projeto
	 */
	public function __construct(
		Fabrica $fabrica,
		public bool $formatar = true,
		protected bool $remover = true,
		public bool $excecao = true ){

		//Definindo as configurações
		$this->configuracao	= new ConfiguracaoRepositorio( $fabrica->modelo );
		$this->modelo		= $fabrica->modelo;
		$this->conexao		= $fabrica->conexao->conexao;
		$this->exibirDebug	= Configuracao::get( 'debug.exibir' );
		$this->debug		= new Debug();

	}

	/*
	 * MÉTODOS
	 */

	/**
	 * Método mágico para construção dos métodos
	 *
	 * @param mixed $nome Nome do método
	 * @param mixed $valor valor passado para o método
	 * @return static
	 */
	public function __call( $nome, $valor ): static{

		//Definindo
        $this->definirComplemento( $nome, $valor[ 0 ] );

		//Retornando
		return $this;

    }

	/**
	 * Executando as consultas
	 *
	 * @access protected
	 * @param string $consulta Consulta
	 * @return void
	 */
	protected function executar( string $consulta ): void{

		//Verificando se é para debugar
		if( $this->exibirDebug ){

			//Definindo o trace
			$this->debug->trace();

			//Iniciando o tempo de execução
			$inicio	= microtime( true );

		}

		//Preparando a consulta
		$this->pdo	= $this->conexao->prepare( $consulta );

		//Definindo os valores
		$this->definirParametros();

		//Executando
		$this->pdo->execute();

		//Verificando se é para debugar
		if( $this->exibirDebug ){

			//Definindo os valores para o debug
			$this->debug->duracao	= number_format( microtime( true ) - $inicio, 5 );
			$this->debug->string	= $this->pdo->queryString;
			$this->debug->campos	= $this->campos;

			//Debugando
			$this->debug
				->bancoDeDados()
				->renderizar();

		}

	}

	/*
     * DEFINIÇÕES
     */

	/**
	 * Definindo o complemento
	 *
	 * @access protected
	 * @param mixed $campo Campo da tabela
	 * @param mixed $valor Valor
	 * @param string $tipo Tipo do complemento
	 * @uses App\Infraestrutura\Configuracao\Configuracao::get() Retornando a variável do projeto
	 * @return void
	 */
	protected function definirComplemento( mixed $campo = null, mixed $valor = null, string $tipo = 'padrão' ): void{

		//Definindo o complemento
		$this->complemento	= match( $tipo ){
			'padrão'		=> "AND {$campo} = :{$campo} {$this->complemento}",
			'ordenação'		=> "ORDER BY {$valor} {$this->complemento}",
			'agrupar' 		=> "GROUP BY {$campo} {$this->complemento}",
			'quantidade'	=> "LIMIT :quantidade {$this->complemento}",
			'período'		=> "AND ( {$campo} >= :{$campo}_inicial AND {$campo} <= :{$campo}_final ) {$this->complemento}",
			'não-nulo'		=> "AND {$campo} IS NOT NULL {$this->complemento}",
			'diferente'		=> "AND {$campo} <> :{$campo} {$this->complemento}",
			'paginação'		=> "LIMIT :{$campo}, " . Configuracao::get( 'projeto.quantidade_paginacao' ) . " {$this->complemento}",
			'in'			=> "AND {$campo} IN({$valor}) {$this->complemento}",
			'busca' 		=> ( function() use ( $campo ) {
				$condicoes	= array_map( fn( $coluna ) => "LOWER($coluna) LIKE :termo", $campo );
				return " AND (" . implode( " OR ", $condicoes ) . " ) {$this->complemento}"; } )(),
			'maior-que-agora' => " AND ( STR_TO_DATE({$campo}, '%d/%m/%Y') > CURDATE() OR  STR_TO_DATE({$campo}, '%d/%m/%Y') = CURDATE() ) {$this->complemento}",
			default			=> $this->complemento 
			
		};

		//Definindo os parâmetros
		$this->parametros	= match( $tipo ){
			'padrão',
			'paginação',
			'diferente'	=> [
				":{$campo}" => $valor,
				...$this->parametros ],
			'período'	=> [
				":{$campo}_inicial" => $valor[ 0 ],
				":{$campo}_final" => $valor[ 1 ],
				...$this->parametros ],
			'quantidade'	=> [
				':quantidade' => $valor,
				...$this->parametros ],
			'busca' => [
				':termo' => '%' . mb_strtolower( $valor ) . '%',
				...$this->parametros ],
			default	=> $this->parametros };

	}

	/**
	 * Definindo os parâmetros do PDO
	*
	* @access protected
	* @return void
	*/
	protected function definirParametros(): void{

		//Verificando
		if( count( $this->parametros ) > 0 )
        	//Listando os parâmetros
			foreach( $this->parametros as $campo => $valor ){

				//Definindo os campos e os valores
				$this->pdo->bindValue( $campo, $valor, $this->definirTipo( $valor ) );

				//Definindo o campo e seu valor para debug
				$this->campos[ $campo ]	= $valor;

			}

    }

	/**
	 * Definindo o tipo do campo para o formato PDO
	*
	* @access protected
	* @param mixed $valor Valor do campo
	* @return int
	*/
	protected function definirTipo( mixed $valor ): int{

		//Retornando o tipo do campo para o PDO
		return match( true ){

			//Inteiro
			is_int( $valor ) 	=> PDO::PARAM_INT,
			//Booleano
			is_bool( $valor ) 	=> PDO::PARAM_BOOL,
			//Nulo
			is_null( $valor )	=> PDO::PARAM_NULL,
			//String
			default 			=> PDO::PARAM_STR };

	}

	/**
	 * Definindo a quantidade de conteúdos para os métodos
	 *
	 * @param int $quantidade Quantidade de conteúdos
	 * @return static
	 */
	public function quantidade( int $quantidade ): static{

		//Definindo o tipo da variável
		$this->definirComplemento( valor: $quantidade, tipo: 'quantidade' );

		//Retornando
		return $this;

	}

	/**
	 * Definindo o limite de conteúdos para os métodos
	 *
	 * @param int $limitador Limite de conteúdos
	 * @return static
	 */
	public function limitado( int $limitador ): static{

		//Definindo o tipo da variável
		$this->definirComplemento( 'limitador', $limitador, 'paginação' );

		//Retornando
		return $this;

	}

	/**
	 * Definindo a ordem dos conteúdos para os métodos
	 *
	 * @param null|string $ordem Ordem dos conteúdos
	 * @return static
	 */
	public function ordem( ?string $ordem ): static{

		// Definindo a ordem para o formato do banco de dados
		$ordem = Util::definirOrdemBancoDeDados( $ordem );

		//Definindo o tipo da variável
		$this->definirComplemento( valor: $ordem, tipo: 'ordenação' );

		//Retornando
		return $this;

	}

	public function agrupar( ?array $campos = []): static
	{

		// Definindo os campos
		$campos = !is_null($campos) ? 
				  implode(', ', $campos) : 
				  $this->propriedades['configuracao']->propriedades['campos'];

		// Definindo o tipo da variável
		$this->definirComplemento(campo: $campos, tipo: 'agrupar');

		// Retornando
		return $this;

	}

	public function dataEhFutura(): static
	{
		// Definindo o horário de agora
		$agora = (
			new DateTime(
				'now',
				new \DateTimeZone('America/Recife')
			))->format('d/m/Y');

		// Definindo o complemento
		$this->definirComplemento('dia', $agora, 'maior-que-agora');

		// Retornando
		return $this;
	}

	/*
	 * INSERÇÕES
	 */

	/**
	 * Inserindo o conteúdo
	 *
	 * @param Modelo $modelo Modelo
	 * @throws BancoDeDadosException
	 * @return int
	 */
	public function inserir( Modelo $modelo ): int{

		try{

			//Preparando a consulta
			$this->pdo	= $this->conexao->prepare( "INSERT INTO {$this->configuracao->tabela}({$modelo->camposSeparados}) VALUES({$modelo->camposSeparadosParaInsercao})" );

			//Listando
			foreach( $modelo->campos as $campo )
				//Definindo os valores
				$this->pdo->bindValue( ":{$campo}", $modelo->$campo, $this->definirTipo( $modelo->$campo ) );

			//Executando
			$this->pdo->execute();

			//Retornando
			return (int) $this->conexao->lastInsertId();

		}catch( PDOException $e ){

			//Lançando a exceção
			throw new BancoDeDadosException( EnumRepositorio::INSERIR->texto(), $e, resposta: Http::BAD_REQUEST->codigo() );

		}

	}

	public function editar(Modelo $modelo): void
	{

		try {

			//Formatando os campos para a consulta SQL
			$camposFormatados = array_map(fn($campo) => "{$campo} = :{$campo}", $modelo->campos);
			$camposImplode = implode(', ', $camposFormatados);

			//Preparando a consulta
			$pdo = $this->conexao->prepare("UPDATE {$this->configuracao->tabela} SET {$camposImplode} WHERE id = :id");

			//Listando os campos
			foreach ($modelo->campos as $campo)
				//Definindo os valores
				$pdo->bindValue(":{$campo}", $modelo->$campo, $this->definirTipo($modelo->$campo));

			//Definindo os valores
			$pdo->bindValue(':id', $modelo->id, PDO::PARAM_INT);

			//Executando a consulta
			$pdo->execute();

		} catch (PDOException $e) {

			//Lançando a exceção
			throw new BancoDeDadosException( EnumRepositorio::EDITAR->texto(), $e, resposta: Http::BAD_REQUEST->codigo());

		}

	}


	/**
	 * Editando o status
	 *
	 * @param int $id Identificador do conteúdo
	 * @param int $status Status
	 * @throws BancoDeDadosException
	 * @return void
	 */
	public function editarStatus(int $id, int $status): void
	{

		try {

			//Preparando a consulta
			$pdo = $this->conexao->prepare("UPDATE {$this->configuracao->tabela} SET status = :status WHERE id = :id");

			//Definindo os valores
			$pdo->bindValue(':id', $id, PDO::PARAM_INT);
			$pdo->bindValue(':status', $status, PDO::PARAM_INT);

			//Executando a consulta
			$pdo->execute();

		} catch (PDOException $e) {

			//Lançando a exceção
			throw new BancoDeDadosException(EnumRepositorio::EDITAR->texto(), $e, resposta: Http::BAD_REQUEST->codigo());

		}

	}

	/*
	 * PROCURAS
	 */

	/**
	 * Procurando o conteúdo
	 *
	 * @throws ConteudoException
	 * @throws BancoDeDadosException
	 * @return mixed
	 */
	public function procurar(): mixed{

		try{

			//Executando a consulta
			$this->executar( "SELECT {$this->configuracao->campos} FROM {$this->configuracao->tabela} WHERE id IS NOT NULL {$this->complemento}" );

			//Limpando os parâmetros e complemento
			$this->limparComplementosParametros();

			//Definindo o conteúdo
			$conteudo	= $this->pdo->fetch();

			//Verificando
			if( $conteudo )
				//Retornando
				return (object) ( new $this->modelo( $conteudo, $this->formatar, $this->remover ) )->propriedades;

			//Verificando se é para exibir a exceção
			if( $this->excecao )
				//Lançando a exceção
				throw new ConteudoException( sprintf( Conteudo::NENHUM_CONTEUDO_ENCONTRADO->texto(), $this->configuracao->nome ) );
			else
				//Retornando
				return null;

		}catch( PDOException $e ){

			//Lançando a exceção
			throw new BancoDeDadosException( EnumRepositorio::PROCURAR->texto(), $e, resposta: Http::BAD_REQUEST->codigo() );

		}

	}

	/*
	 * LISTAGENS
	 */

	/**
	 * Listando os conteúdos
	 *
	 * @throws ConteudoException
	 * @throws BancoDeDadosException
	 * @return mixed
	 */
	public function listar(): mixed{

		try{

			//Executando a consulta
			$this->executar( "SELECT {$this->configuracao->campos} FROM {$this->configuracao->tabela} WHERE id IS NOT NULL {$this->complemento}" );

			//Limpando os parâmetros e complemento
			$this->limparComplementosParametros();

			//Definindo os conteúdos
			$conteudos	= $this->pdo->fetchAll();

			//Verificando
			if( count( $conteudos ) > 0 )
				//Retornando os conteúdos
				return array_map( fn( $conteudo ) => (object) ( new $this->modelo( $conteudo, $this->formatar, $this->remover ) )->propriedades, $conteudos );

			//Exibindo ou não a exceção
			if( $this->excecao )
				//Lançando a exceção
				throw new ConteudoException( sprintf( Conteudo::NENHUM_CONTEUDO_ENCONTRADO->texto(), $this->configuracao->nome ) );
			else
				//Retornando
				return null;

		}catch( PDOException $e ){

			//Lançando a exceção
			throw new BancoDeDadosException( EnumRepositorio::LISTAR->texto(), $e, resposta: Http::BAD_REQUEST->codigo() );

		}

	}

	/*
	 * CONTAGENS
	 */

	/**
	 * Contando a quantidade de conteúdos
	 *
	 * @throws BancoDeDadosException
	 * @return int
	 */
	public function contar(): int{

		try{

			//Executando a consulta
			$this->executar( "SELECT COUNT(*) FROM {$this->configuracao->tabela} WHERE id IS NOT NULL {$this->complemento}" );

			//Limpando os parâmetros e complemento
			$this->limparComplementosParametros();

			//Retornando a quantidade
			return (int) $this->pdo->fetchColumn();

		}catch( PDOException $e ){

			//Lançando a exceção
			throw new BancoDeDadosException( EnumRepositorio::CONTAR->texto(), $e, resposta: Http::BAD_REQUEST->codigo() );

		}

	}

	public function remover(int $id): void
	{

		try {

			//Preparando a consulta
			$pdo = $this->conexao->prepare("DELETE FROM {$this->configuracao->tabela} WHERE id = :id");

			//Definindo os valores
			$pdo->bindValue(':id', $id, PDO::PARAM_INT);

			//Executando a consulta
			$pdo->execute();

		} catch (PDOException $e) {

			//Lançando a exceção
			throw new BancoDeDadosException(EnumRepositorio::REMOVER->texto(), $e, resposta: Http::BAD_REQUEST->codigo());

		}

	}

}