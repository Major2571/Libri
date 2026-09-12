<?php
//Definindo o namespace
namespace App\Infraestrutura;

//Definindo as classes usadas
use App\Infraestrutura\Enum\Status\Status;
use App\Infraestrutura\Fabrica;
use App\Infraestrutura\Base;
use Exception;

/**
 * Fachada do projeto
 * Todas as chamadas irão passar por esta classe
 * Herdada da classe base para auxílio nas chamadas
 *
 * @final
 * @author Caroline Tacats <caroline.tacats62@gmail.com>
 * @package App\Infraestrutura
 * @property \App\Controlador\Controlador $controlador Controlador
 * @property \PDO $conexao Conexão
 */
final class Fachada extends Base
{

	/**
	 * Método construtor da classe
	 *
	 * @param Fabrica $fabrica Objeto da fábrica
	 */
	public function __construct(Fabrica $fabrica)
	{

		//Definindo a conexão
		$this->conexao = $fabrica->conexao->conexao ?? null;

		//Definindo o controlador
		$this->controlador = $fabrica->controlador;

		//Definindo o repositório
		$this->repositorio = $fabrica->repositorio;

	}

	/**
	 * Obtendo a instância da fachada
	 *
	 * @static
	 * @param string $modelo Modelo
	 * @param string $implementacao Tipo da implementação
	 * @return self
	 */
	public static function instancia(string $modelo, string $implementacao = 'BDR'): self
	{

		//Definindo o nome da instância
		$instancia = $modelo . $implementacao;

		//Verificando
		if (!array_key_exists($instancia, self::$instancia))
			//Definindo
			self::$instancia[$instancia] = new self(new Fabrica($modelo, $implementacao));

		//Retornando
		return self::$instancia[$instancia]->configurar();

	}

	/**
	 * Configurando os campos
	 *
	 * @param bool $formatar Formatar ou não
	 * @param bool $excecao Exibir exceção ou não
	 * @param bool $complemento Exibir ou não o complemento
	 * @return self
	 */
	public function configurar(bool $formatar = true, bool $excecao = true, bool $complemento = true): self
	{

		//Formatando os campos
		$this->repositorio->formatar = $formatar;
		$this->repositorio->excecao = $excecao;
		$this->controlador->complemento = $complemento;

		//Retornando o objeto
		return $this;

	}

	/*
	 * INSERÇÕES
	 */

	/**
	 * Inserindo o conteúdo
	 *
	 * @param array $campos Campos
	 * @param null|array $arquivo Arquivo para upload
	 * @uses PDO::beginTransaction() Iniciando a transação
	 * @uses App\Controlador\Controlador::inserir() Inserindo o conteúdo
	 * @uses PDO::commit() Realizando a transação
	 * @uses PDO::rollback() Desfazendo a transação
	 * @return mixed
	 */
	public function inserir(array $campos, ?array $arquivo = null): mixed
	{

		try {

			//Iniciando a transação
			$this->conexao->beginTransaction();

			//Inserindo e retornando
			$conteudo = $this->controlador->inserir($campos, $arquivo);

			//Comitando
			$this->conexao->commit();

			//Retornando o conteúdo
			return $conteudo;

		} catch (Exception $e) {

			//Desfazendo a transação
			$this->conexao->rollback();

			//Lançando a exceção
			throw $e;

		}

	}

	/**
	 * EDIÇÃO
	 */
	/**
	 * Editando o conteúdo
	 *
	 * @param array $campos Campos
	 * @param null|array $arquivo Arquivo para upload
	 * @uses App\Controlador\Controlador::editar() Editando o conteúdo
	 * @uses PDO::beginTransaction() Iniciando a transação
	 * @uses PDO::commit() Realizando a transação
	 * @uses PDO::rollback() Desfazendo a transação
	 * @return mixed
	 */
	public function editar(array $campos, ?array $arquivo = null): mixed
	{

		try {

			//Iniciando a transação
			$this->conexao->beginTransaction();

			//Editando e retornando
			$conteudo = $this->controlador->editar($campos, $arquivo);

			//Comitando
			$this->conexao->commit();

			//Retornando o conteúdo
			return $conteudo;

		} catch (Exception $e) {

			//Desfazendo a transação
			$this->conexao->rollback();

			//Lançando a exceção
			throw $e;

		}

	}


	/**
	 * REMOVER
	 */
	public function remover(int $id): void
	{

		try {

			//Iniciando a transação
			$this->conexao->beginTransaction();

			//Editando e retornando
			$this->controlador->remover($id);

			//Comitando
			$this->conexao->commit();

		} catch (Exception $e) {

			//Desfazendo a transação
			$this->conexao->rollback();

			//Lançando a exceção
			throw $e;

		}

	}

	/**
	 * Listando os conteúdos de forma ordenada
	 *
	 * @param null|string $ordem Ordem de aparição dos conteúdos
	 * @uses App\Controlador\Controlador::ordem() Definindo a ordem para os métodos
	 * @uses App\Controlador\Controlador::listarOrdenado() Listando os conteúdos de forma ordenada
	 * @return mixed
	 */
	public function listarOrdenado(?string $ordem = null): mixed
	{

		//Retornando os conteúdos
		return $this->controlador
			->ordem($ordem)
			->listarOrdenado();

	}

	/**
	 * Procurando o conteúdo pelo seu identificador
	 *
	 * @param int $id Identificador do conteúdo
	 * @uses App\Controlador\Controlador::id() Definindo o identificador para os métodos(chamada via _call)
	 * @uses App\Controlador\Controlador::procurarPorIdentificador() Procurando o conteúdo pelo seu identificador
	 * @return mixed
	 */
	public function procurarPorIdentificador(int $id = 1): mixed
	{

		//Retornando o conteúdo
		return $this->controlador
			->id($id)
			->procurarPorIdentificador();

	}


}