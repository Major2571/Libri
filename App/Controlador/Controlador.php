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

	/*
	 * LISTAGENS
	 */
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

}