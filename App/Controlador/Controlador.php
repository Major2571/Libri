<?php
//Definindo o namespace
namespace App\Controlador;

//Definindo as classes usadas
use App\Excecao\Conteudo as ConteudoException;
use App\Infraestrutura\Enum\Status\Http;
use App\Repositorio\Repositorio;
use App\Infraestrutura\Fabrica;
use App\Infraestrutura\Base;
use App\Modelo\Modelo;
use App\Util\Util;

/**
 * Classe do controlador
 * Toda regra de negócio do projeto passará por esta classe
 *
 * @abstract
 * @author Caroline Tacats <caroline.tacats62@gmail.com>
 * @package App\Controlador
 * @property Repositorio|\App\Repositorio\BDR\Repositorio $repositorio Repositório associado ao modelo
 */
abstract class Controlador extends Base
{

	/**
	 * Método construtor da classe
	 *
	 * @param object $repositorio repositorio
	 * @param object $configuracao Configurações
	 * @param bool $complemento Obter ou não o complemento
	 */
	public function __construct(public object $repositorio, object $configuracao, public bool $complemento = true)
	{

		//Definindo o repositório
		$this->modelo = $configuracao->nomeClasse;
		$this->nomeModelo = $configuracao->nomeModelo;
		$this->excecao = $repositorio->excecao;

	}

	/**
	 * Obtendo o controlador
	 *
	 * @static
	 * @param string $modelo Modelo
	 * @param string $implementacao Tipo de implementação
	 * @return self
	 */
	public function controlador(string $modelo, string $implementacao = 'BDR'): self
	{

		//Retornando
		return (new Fabrica($modelo, $implementacao))->controlador;

	}

	/**
	 * Obtendo o repostório
	 *
	 * @static
	 * @param string $modelo Modelo
	 * @return Repositorio
	 */
	public function repositorio(string $modelo): Repositorio
	{

		//Retornando
		return (new Fabrica($modelo))->repositorio;

	}

	/**
	 * Configurando os campos
	 *
	 * @param bool $formatar Formatar ou não
	 * @param bool $excecao Exibir exceção ou não
	 * @return self
	 */
	public function configurar(bool $formatar = true, bool $excecao = true, bool $complemento = true): self
	{

		//Formatando os campos
		$this->repositorio->formatar = $formatar;
		$this->repositorio->excecao = $excecao;
		$this->complemento = $complemento;

		//Retornando o objeto
		return $this;

	}

	/**
	 * Removendo o controlador do modelo
	 *
	 * @access protected
	 * @param array $controladores Controladores
	 * @param mixed $conteudos Conteúdo(s) para validação de listagem ou não
	 * @return void
	 */
	protected function removerControlador(array $controladores, mixed $conteudos): void
	{

		//Verificando se é um array
		if (is_array($conteudos))
			//Listando os modelos
			foreach ($conteudos as $modelo)
				//Listando os controladores fornecidos
				foreach ($controladores as $controlador)
					//Removendo o controlador
					unset($modelo->{"controlador$controlador"});
		else
			//Listando os controladores fornecidos
			foreach ($controladores as $controlador)
				//Removendo o controlador
				unset($conteudos->{"controlador$controlador"});

	}

	/**
	 * Métodos extras
	 * Geralmente é chamado por outros métodos para complementar o método
	 *
	 * @param mixed $argumentos Argumentos
	 * @return void
	 */
	public function extra(mixed $argumentos = null): void
	{
	}

	/**
	 * Obetendo os complementos dos modelos
	 *
	 * @access protected
	 * @param mixed $conteudos Conteúdos
	 * @return mixed
	 */
	protected function complemento(mixed $conteudos): mixed
	{

		//Retornando
		return $conteudos;

	}

	/*
	 * VERIFICAÇÕES E VALIDAÇÕES
	 */

	/**
	 * Verificando os campos únicos
	 *
	 * @access protected
	 * @param Modelo $modelo Modelo
	 * @param array $campos Campos
	 * @uses App\Util\Util::definirMetodoUnico() Definindo o método único
	 * @return void
	 * @throws ConteudoException
	 */
	protected function verificarCamposUnicos(Modelo $modelo, array $campos): void
	{

		//Verificando se existem campos únicos
		if (isset($modelo->configuracao['unico'])) {

			//Listando os campos únicos
			foreach ($modelo->configuracao['unico'] as $unico) {

				//Definindo o objeto
				$objeto = Util::definirMetodoUnico($unico);

				//Verificando
				if ($this->{$objeto->termo}(mb_strtolower($campos[$unico], 'UTF-8'))->{$objeto->metodo}())
					//Lançando a exceção
					throw new ConteudoException($objeto->excecao, Http::CONFLICT->codigo());

			}

		}

	}

	/**
	 * Verificando a existência do domínio
	 *
	 * @uses App\Repositorio\Repositorio::dominio() Definindo o domínio para os métodos(chamada via _call)
	 * @uses App\Repositorio\API\Dominio::verificar() Verificando a existência do domínio
	 * @return mixed
	 */
	public function verificarDominio(): mixed
	{

		//Verificando e retornando a verificação do conteúdo
		return $this->repositorio
			->dominio($this->dominio)
			->verificar();

	}

	/*
	 * INSERÇÕES
	 */

	/**
	 * Inserindo os campos
	 *
	 * @param array $campos Campos para inserção
	 * @uses App\Repositorio\BDR\Repositorio::inserir() Inserindo o conteúdo
	 * @return mixed
	 */
	public function inserir(array $campos): mixed
	{

		//Definindo o identificador
		$id = $this->repositorio->inserir(new $this->modelo($campos));

		//Extras
		$this->extra($id);

		//Retornando o identificador
		return $id;

	}

	/*
	 * EDIÇÕES
	 */

	/**
	 * Inserindo os campos
	 *
	 * @param array $campos Campos para inserção
	 * @uses App\Repositorio\BDR\Repositorio::inserir() Inserindo o conteúdo
	 * @return mixed
	 */
	public function editar(array $campos = [], array $arquivo = []): void
	{

		// Criando o modelo para edição
		$modelo = new $this->modelo($campos);

		// Editando o conteúdo
		$this->repositorio->editar($modelo);

	}

	public function remover(int $id): void
	{
		//Removendo o conteúdo
		$this->repositorio->remover($id);

	}

	/*
	 * CONTAGEM
	 */

	/**
	 * Contando a quantidade de conteúdos por email
	 *
	 * @uses App\Repositorio\BDR\Repositorio::email() Definindo o email para os métodos(chamada via _call)
	 * @uses App\Repositorio\BDR\Repositorio::contar() Contando a quantidade de conteúdos
	 * @return int
	 */
	public function contarPorEmail(): int
	{

		//Retornando a quantidade conteúdos
		return $this->repositorio
			->email($this->email)
			->contar();

	}

	/**
	 * Contando a quantidade de conteúdos por documento
	 *
	 * @uses App\Repositorio\BDR\Repositorio::documento() Definindo o documento para os métodos(chamada via _call)
	 * @uses App\Repositorio\BDR\Repositorio::contar() Contando a quantidade de conteúdos
	 * @return int
	 */
	public function contarPorDocumento(): int
	{

		//Retornando a quantidade conteúdos
		return $this->repositorio
			->documento($this->documento)
			->contar();

	}

	/*
	 * PROCURAS
	 */

	/**
	 * Procurando o conteúdo pelo seu identificador
	 *
	 * @uses App\Repositorio\Repositorio::id() Definindo o identificador para os métodos(chamada via _call)
	 * @uses App\Repositorio\BDR\Repositorio::procurar() Procurando o conteúdo
	 * @return mixed
	 */
	public function procurarPorIdentificador(): mixed
	{

		//Retornando o conteúdo
		return $this->complemento(
			//Procurando o conteúdo
			$this->repositorio
				->id($this->id)
				->procurar(),
			$this->complemento
		);

	}

	/**
	 * Procurando o conteúdo por projeto
	 *
	 * @uses App\Repositorio\Repositorio::projeto() Definindo o projeto para os métodos(chamada via _call)
	 * @uses App\Repositorio\BDR\Repositorio::procurar() Procurando o conteúdo
	 * @return mixed
	 */
	public function procurarPorProjeto(): mixed
	{

		//Retornando o conteúdo
		return $this->complemento(
			//Procurando o conteúdo
			$this->repositorio
				->projeto($this->projeto)
				->procurar(),
			$this->complemento
		);

	}

	/**
	 * Procurando o conteúdo por slug e status
	 *
	 * @uses App\Repositorio\Repositorio::projeto() Definindo o projeto para os métodos(chamada via _call)
	 * @uses App\Repositorio\BDR\Repositorio::procurar() Procurando o conteúdo
	 * @return mixed
	 */
	public function procurarPorStatusPorSlug(): mixed
	{

		//Retornando o conteúdo
		return $this->complemento(
			//Procurando o conteúdo
			$this->repositorio
				->slug($this->slug)
				->status($this->status)
				->procurar(),
			$this->complemento
		);

	}

	/**
	 * Procurando o conteúdo por slug e status
	 *
	 * @uses App\Repositorio\Repositorio::projeto() Definindo o projeto para os métodos(chamada via _call)
	 * @uses App\Repositorio\BDR\Repositorio::procurar() Procurando o conteúdo
	 * @return mixed
	 */
	public function procurarPorSlug(): mixed
	{

		//Retornando o conteúdo
		return $this->complemento(
			//Procurando o conteúdo
			$this->repositorio
				->slug($this->slug)
				->procurar(),
			$this->complemento
		);

	}


	public function procurarPorBeneficioEUsuario(): mixed
	{

		return $this->complemento(
			$this->repositorio
				->usuario($this->usuario)
				->beneficio($this->beneficio)
				->procurar(),
			$this->complemento
		);

	}

	/*
	 * LISTAGENS
	 */
	/**
	 * Listando os conteúdos por status, de forma ordenada e por quantidade
	 *
	 * @uses App\Repositorio\BDR\Repositorio::quantidade() Definindo a ordem para os métodos
	 * @uses App\Repositorio\BDR\Repositorio::ordem() Definindo a ordem para os métodos
	 * @uses App\Repositorio\Repositorio::status() Definindo o status para os métodos(chamada via _call)
	 * @uses App\Repositorio\BDR\Repositorio::listar() Listando os conteúdos
	 * @return mixed
	 */
	public function listarPorStatusOrdenadoPorQuantidade(): mixed
	{

		//Retornando os conteúdos
		return $this->complemento(
			//Listando os conteúdos
			$this->repositorio
				->quantidade($this->quantidade)
				->ordem($this->ordem)
				->status($this->status)
				->listar(),
			$this->complemento
		);

	}

	/**
	 * Listando os conteúdos por status e de forma ordenada
	 *
	 * @uses App\Repositorio\BDR\Repositorio::ordem() Definindo a ordem para os métodos
	 * @uses App\Repositorio\Repositorio::status() Definindo o status para os métodos(chamada via _call)
	 * @uses App\Repositorio\BDR\Repositorio::listar() Listando os conteúdos
	 * @return mixed
	 */
	public function listarPorStatusOrdenado(): mixed
	{

		//Retornando os conteúdos
		return $this->complemento(
			//Listando os conteúdos
			$this->repositorio
				->ordem($this->ordem)
				->status($this->status)
				->listar(),
			$this->complemento
		);

	}

	/**
	 * Listando os conteúdos de forma ordenada
	 *
	 * @uses App\Repositorio\BDR\Repositorio::ordem() Definindo a ordem para os métodos
	 * @uses App\Repositorio\BDR\Repositorio::listar() Listando os conteúdos
	 * @return mixed
	 */
	public function listarOrdenado(): mixed
	{

		//Retornando os conteúdos
		return $this->complemento(
			//Listando os conteúdos
			$this->repositorio
				->ordem($this->ordem)
				->listar(),
			$this->complemento
		);

	}

	/**
	 * Listando os conteúdos pelo seu identificador
	 *
	 * @uses App\Repositorio\Repositorio::id() Definindo o identificador para os métodos(chamada via _call)
	 * @uses App\Repositorio\BDR\Repositorio::listar() Listando os conteúdos de acordo com os parâmetros passados
	 * @return mixed
	 */
	public function listarPorIdentificador(): mixed
	{

		//Listando e retornando os conteúdos
		return $this->complemento(
			//Listando os conteúdos
			$this->repositorio
				->id($this->id)
				->listar(),
			$this->complemento
		);

	}

	/**
	 * Listando os conteúdos por status, por termos, de forma ordenada e limitada
	 *
	 * @uses App\Repositorio\Repositorio::limitado() Definindo o limiteador para os módulos
	 * @uses App\Repositorio\Repositorio::ordenadoPor() Definindo a ordenação para os métodos
	 * @uses App\Repositorio\Repositorio::termos() Definindo os termos para os métodos
	 * @uses App\Repositorio\Repositorio::status() Definindo o status para os métodos
	 * @uses App\Repositorio\BDR\Repositorio::listar() Listando os conteúdos por status, por termos, de forma ordenada e limitada
	 * @return mixed
	 */
	public function listarPorTermosOrdenadoPorQuantidadePorStatus(): mixed
	{

		//Listando e retornando os conteúdos
		return $this->complemento(
			//Listando os conteúdos
			$this->repositorio
				->quantidade($this->quantidade)
				->ordem($this->ordem)
				->termos($this->termos)
				->status($this->status)
				->listar(),
			$this->complemento
		);

	}

}