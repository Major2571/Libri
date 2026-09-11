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
	 * AUTENTICAÇÕES
	 */

	/**
	 * Autenticando o cliente
	 *
	 * @param null|string $login Login do cliente
	 * @param null|string $senha Senha do cliente
	 * @uses App\Controlador\Cliente::autenticar() Autenticando o cliente
	 * @return mixed
	 */
	public function autenticar(?string $email = null, ?string $senha = null): mixed
	{

		//Autenticando
		return $this->controlador
			->email($email)
			->senha($senha)
			->autenticar();

	}

	/**
	 * Iniciando a sessão do usuário
	 * @param object $objeto
	 * @return void
	 */
	public function iniciarSessao(object $objeto): void
	{

		// Iniciando a sessão
		$this->controlador->iniciarSessao($objeto);

	}

	/**
	 * Obtendo o usuário autenticado
	 *
	 * @param bool $complemento Complemento ou não
	 * @uses App\Controlador\Controlador::obterUsuarioAutenticado() Obtendo o usuário autenticado
	 * @return mixed
	 */
	public function obterUsuarioAutenticado(bool $complemento = true): mixed
	{

		//Retornando os conteúdos
		return $this->controlador
			->obterUsuarioAutenticado($complemento);

	}

	/*
	 * VERIFICAÇÕES
	 */

	/**
	 * Verificando a existência do domínio
	 *
	 * @param null|string $dominio Domínio para verificação
	 * @uses App\Controlador\Controlador::dominio() Definindo o domínio para os métodos(chamada via _call)
	 * @uses App\Controlador\Controlador::verificarDominio() Verificando a existência do domínio
	 * @return mixed
	 */
	public function verificarDominio(?string $dominio = null): mixed
	{

		//Verificando e retornando
		return $this->controlador
			->dominio($dominio)
			->verificarDominio();

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
	 * Editando o status do conteúdo
	 *
	 * @param int $id Identificador do conteúdo
	 * @param int $status Novo status do conteúdo
	 * @uses PDO::beginTransaction() Iniciando a transação
	 * @uses App\Controlador\Controlador::editarStatus() Editando o status do conteúdo
	 * @uses PDO::commit() Realizando a transação
	 * @uses PDO::rollback() Desfazendo a transação
	 * @return mixed
	 */
	public function editarStatus(int $id, int $status): mixed
	{

		try {

			//Iniciando a transação
			$this->conexao->beginTransaction();

			//Editando e retornando
			$conteudo = $this->controlador->editarStatus($id, $status);

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
	 * Redefinindo a senha do cliente
	 *
	 * @param null|string $email Email do cliente
	 * @uses App\Controlador\Controlador::email() Definindo o email para os métodos(chamada via _call)
	 * @uses App\Controlador\Cliente::esqueciMinhaSenha() Redefinindo a senha do cliente
	 * @return void
	 */
	public function esqueciMinhaSenha(?string $email = null): void
	{

		//Editando a senha e retornando o objeto
		$this->controlador
			->email($email)
			->esqueciMinhaSenha();

	}

	/**
	 * Inativando o perfil do usuário
	 *
	 * @param int $id Identificador do usuário
	 * @param null|string $senha Senha do usuário
	 * @param int $status Novo status do usuário
	 * @uses App\Controlador\Controlador::id() Definindo o identificador para os métodos(chamada via _call)
	 * @uses App\Controlador\Controlador::senha() Definindo a senha para os métodos(chamada via _call)
	 * @uses App\Controlador\Controlador::status() Definindo o status para os métodos(chamada via _call)
	 * @uses App\Controlador\Controlador::inativarPerfil() Inativando o perfil do usuário
	 * @return void
	 */
	public function inativarPerfil(int $id, ?string $senha = null, int $status = Status::INATIVO->value): void
	{
		//Editando a senha e retornando o objeto
		$this->controlador
			->id($id)
			->senha($senha)
			->status($status)
			->inativarPerfil();

	}

	/*
	 * PROCURAS
	 */

	/**
	 * Procurando o conteúdo pelo seu status e seu slug
	 *
	 * @param int $status Status do conteúdo
	 * @param null|string $slug Slug do conteúdo
	 * @uses App\Controlador\Controlador::slug() Definindo o slug para os métodos(chamada via _call)
	 * @uses App\Controlador\Controlador::status() Definindo o status para os métodos(chamada via _call)
	 * @uses App\Controlador\Controlador::procurarPorStatusPorSlug() Procurando o conteúdo pelo seu status e seu slug
	 * @return mixed
	 */
	public function procurarPorStatusPorSlug(int $status = Status::ATIVO->value, ?string $slug = null): mixed
	{

		//Retornando o conteúdo
		return $this->controlador
			->slug($slug)
			->status($status)
			->procurarPorStatusPorSlug();

	}
	/**
	 * Procurando o conteúdo pelo seu slug
	 *
	 * @param int $status Status do conteúdo
	 * @param null|string $slug Slug do conteúdo
	 * @uses App\Controlador\Controlador::slug() Definindo o slug para os métodos(chamada via _call)
	 * @uses App\Controlador\Controlador::procurarPorSlug() Procurando o conteúdo pelo seu status e seu slug
	 * @return mixed
	 */
	public function procurarPorSlug(?string $slug = null): mixed
	{

		//Retornando o conteúdo
		return $this->controlador
			->slug($slug)
			->procurarPorSlug();

	}

	public function procurarPorBeneficioEUsuario(int $beneficio, int $usuario)
	{

		return $this->controlador
			->usuario($usuario)
			->beneficio($beneficio)
			->procurarPorBeneficioEUsuario();
			
	}

	/**
	 * Procurando o conteúdo pelo seu status e seu email
	 *
	 * @param int $status Status do conteúdo
	 * @param null|string $email Email do conteúdo
	 * @uses App\Controlador\Controlador::email() Definindo o email para os métodos(chamada via _call)
	 * @uses App\Controlador\Controlador::status() Definindo o status para os métodos(chamada via _call)
	 * @uses App\Controlador\Controlador::procurarPorStatusPorEmail() Procurando o conteúdo pelo seu status e seu email
	 * @return mixed
	 */
	public function procurarPorStatusPorEmail(int $status = Status::ATIVO->value, ?string $email = null): mixed
	{

		//Retornando o conteúdo
		return $this->controlador
			->email($email)
			->status($status)
			->procurarPorStatusPorEmail();

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

	/**
	 * Procurando o conteúdo por projeto
	 *
	 * @param null|int $projeto Identificador do projeto
	 * @uses App\Controlador\Controlador::projeto() Definindo o projeto para os métodos(chamada via _call)
	 * @uses App\Infraestrutura\Configuracao\Configuracao::get() Retornando a variável do projeto
	 * @uses App\Controlador\Controlador::procurarPorProjeto() Procurando o conteúdo por projeto
	 * @return mixed
	 */
	public function procurarPorProjeto(?int $projeto = null): mixed
	{

		//Retornando o conteúdo
		return $this->controlador
			->projeto($projeto ?? Configuracao::get('projeto.codigo'))
			->procurarPorProjeto();

	}

	/*
	 * LISTAGENS
	 */

	/**
	 * Listando os conteúdos por status, de forma ordenada e por quantidade
	 *
	 * @param int $status Status
	 * @param null|string $ordem Ordem de aparição dos conteúdos
	 * @param int $quantidade Quantidade de conteúdos
	 * @uses App\Controlador\Controlador::quantidade() Definindo a quantidade para os métodos(chamada via _call)
	 * @uses App\Controlador\Controlador::ordem() Definindo a ordem para os métodos
	 * @uses App\Controlador\Controlador::status() Definindo o status para os métodos(chamada via _call)
	 * @uses App\Controlador\Controlador::listarPorStatusordemQuantidade() Listando os conteúdos por status, de forma ordenada e por quantidade
	 * @return mixed
	 */
	public function listarPorStatusOrdenadoPorQuantidade(int $status = Status::ATIVO->value, ?string $ordem = null, int $quantidade = 5): mixed
	{

		//Retornando os conteúdos
		return $this->controlador
			->quantidade($quantidade)
			->ordem($ordem)
			->status($status)
			->listarPorStatusOrdenadoPorQuantidade();

	}

	/**
	 * Listando os conteúdos por status e de forma ordenada
	 *
	 * @param int $status Status
	 * @param null|string $ordem Ordem de aparição dos conteúdos
	 * @uses App\Controlador\Controlador::ordem() Definindo a ordem para os métodos
	 * @uses App\Controlador\Controlador::status() Definindo o status para os métodos(chamada via _call)
	 * @uses App\Controlador\Controlador::listarPorStatusOrdenad() Listando os conteúdos por status e de forma ordenada
	 * @return mixed
	 */
	public function listarPorStatusOrdenado(int $status = Status::ATIVO->value, ?string $ordem = null): mixed
	{

		//Retornando os conteúdos
		return $this->controlador
			->ordem($ordem)
			->status($status)
			->listarPorStatusOrdenado();

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
	 * Listando os conteúdos por usuário
	 *
	 * @param int $usuario Identificador do usuário
	 * @uses App\Controlador\Controlador::usuario() Definindo o usuário para os métodos(chamada via _call)
	 * @uses App\Controlador\Controlador::listarPorUsuario() Listando os conteúdos por usuário
	 * @return mixed
	 */
	public function listarPorUsuario(int $usuario): mixed
	{

		//Retornando
		return $this->controlador
			->usuario($usuario)
			->listarPorUsuario();

	}

	/**
	 * Listando os conteúdos por usuário e por status
	 *
	 * @param int $usuario Identificador do usuário
	 * @param int $status Status do conteúdo
	 * @uses App\Controlador\Controlador::usuario() Definindo o usuário para os métodos(chamada via _call)
	 * @uses App\Controlador\Controlador::status() Definindo o status para os métodos(chamada via _call)
	 * @uses App\Controlador\Controlador::listarPorUsuario() Listando os conteúdos por usuário
	 * @return mixed
	 */
	public function listarPorUsuarioPorStatus(int $usuario, int $status = Status::ATIVO->value): mixed
	{

		//Retornando
		return $this->controlador
			->usuario($usuario)
			->status($status)
			->listarPorUsuarioPorStatus();

	}

	/**
	 * Listando os conteúdos por usuário, de forma ordenada e por quantidade
	 *
	 * @param int $usuario Identificador do usuário
	 * @param string $ordem Ordem de aparição dos conteúdos
	 * @param int $quantidade Quantidade de conteúdos
	 * @uses App\Controlador\Controlador::usuario() Definindo o usuário para os métodos(chamada via _call)
	 * @uses App\Controlador\Controlador::ordem() Definindo a ordem para os métodos(chamada via _call)
	 * @uses App\Controlador\Controlador::quantidade() Definindo a quantidade para os métodos(chamada via _call)
	 * @uses App\Controlador\Controlador::listarPorUsuario() Listando os conteúdos por usuário
	 * @return mixed
	 */
	public function listarPorUsuarioOrdenadoPorQuantidade(int $usuario, string $ordem, int $quantidade): mixed
	{

		//Retornando
		return $this->controlador
			->usuario($usuario)
			->ordem($ordem)
			->quantidade($quantidade)
			->listarPorUsuarioOrdenadoPorQuantidade();

	}

	/**
	 * Listando os conteúdos por usuário, por termos, de forma ordenada e por quantidade
	 *
	 * @param int $usuario Identificador do usuário
	 * @param array $termos Termos para busca
	 * @param null|string $ordem Ordem de aparição
	 * @param int $quantidade Quantidade de conteúdo
	 * @uses App\Controlador\Controlador::quantidade() Definindo a quantidade para os módulos
	 * @uses App\Controlador\Controlador::ordem() Definindo a ordenação para os métodos
	 * @uses App\Controlador\Controlador::termos() Definindo os termos para os métodos
	 * @uses App\Controlador\Controlador::status() Definindo o status para os métodos
	 * @uses App\Controlador\Controlador::listarPorTermosOrdenadoPorQuantidadePorStatus() Listando os conteúdos por status, por termos, de forma ordenada e por quantidade
	 * @return mixed
	 */
	public function listarPorUsuarioPorTermosOrdenadoPorQuantidade(int $usuario, array $termos = [], ?string $ordem = null, int $quantidade = 12): mixed
	{

		//Listando e retornando os conteúdos
		return $this->controlador
			->quantidade($quantidade)
			->ordem($ordem)
			->usuario($usuario)
			->termos($termos)
			->listarPorUsuarioPorTermosOrdenadoPorQuantidade();

	}

	/**
	 * Listando os conteúdos por evento e por usuário
	 *
	 * @param int $usuario Identificador do usuário
	 * @param int $evento Identificador do evento
	 * @param int $status Status do conteúdo
	 * @uses App\Controlador\Controlador::usuario() Definindo o usuário para os métodos(chamada via _call)
	 * @uses App\Controlador\Controlador::evento() Definindo o evento para os métodos(chamada via _call)
	 * @uses App\Controlador\Controlador::status() Definindo o status para os métodos(chamada via _call)
	 * @uses App\Controlador\Controlador::listarPorUsuarioEventoPorStatus() Listando os conteúdos por evento e por usuário
	 * @return mixed
	 */
	public function listarPorUsuarioEventoPorStatus(int $usuario, int $evento, int $status = Status::ATIVO->value): mixed
	{

		//Retornando
		return $this->controlador
			->status($status)
			->usuario($usuario)
			->evento($evento)
			->listarPorUsuarioEventoPorStatus();

	}

	/**
	 * Listando os conteúdos por status, por termos, de forma ordenada e limitada
	 *
	 * @param int $status Status do conteúdo
	 * @param array $termos Termos para busca
	 * @param null|string $ordem Ordem de aparição
	 * @param int $quantidade Quantidade de conteúdo
	 * @uses App\Controlador\Controlador::quantidade() Definindo a quantidade para os módulos
	 * @uses App\Controlador\Controlador::ordem() Definindo a ordenação para os métodos
	 * @uses App\Controlador\Controlador::termos() Definindo os termos para os métodos
	 * @uses App\Controlador\Controlador::status() Definindo o status para os métodos
	 * @uses App\Controlador\Controlador::listarPorTermosOrdenadoPorQuantidadePorStatus() Listando os conteúdos por status, por termos, de forma ordenada e limitada
	 * @return mixed
	 */
	public function listarPorTermosOrdenadoPorQuantidadePorStatus(int $status = Status::ATIVO->value, array $termos = [], ?string $ordem = null, int $quantidade = 12): mixed
	{

		//Listando e retornando os conteúdos
		return $this->controlador
			->quantidade($quantidade)
			->ordem($ordem)
			->termos($termos)
			->status($status)
			->listarPorTermosOrdenadoPorQuantidadePorStatus();

	}

	/**
	 * Listando os conteúdos por evento
	 *
	 * @param int $evento Identificador do evento
	 * @uses App\Controlador\Controlador::evento() Definindo o evento para os métodos(chamada via _call)
	 * @uses App\Controlador\Controlador::listarPorEvento() Listando os conteúdos por evento
	 * @return mixed
	 */
	public function listarPorEvento(int $evento): mixed
	{

		//Listando e retornando os conteúdos
		return $this->controlador
			->evento($evento)
			->listarPorEvento();

	}

	/**
	 * Listando os conteúdos por evento
	 *
	 * @param int $evento Identificador do evento
	 * @uses App\Controlador\Controlador::evento() Definindo o evento para os métodos(chamada via _call)
	 * @uses App\Controlador\Controlador::listarDatasFuturasPorEventoOrdenado() Listando os conteúdos por evento
	 * @return mixed
	 */
	public function listarDatasFuturasPorEventoOrdenado(int $evento, ?string $ordem = null): mixed
	{

		//Listando e retornando os conteúdos
		return $this->controlador
			->ordem($ordem)
			->evento($evento)
			->listarDatasFuturasPorEventoOrdenado();

	}

	/**
	 * Listando os conteúdos por inscrição
	 *
	 * @param int $inscricao Identificador da inscrição
	 * @uses App\Controlador\Controlador::inscricao() Definindo a inscrição para os métodos(chamada via _call)
	 * @uses App\Controlador\Controlador::listarPorInscricao() Listando os conteúdos por inscrição
	 * @return mixed
	 */
	public function listarPorInscricao(int $inscricao): mixed
	{

		//Listando e retornando os conteúdos
		return $this->controlador
			->inscricao_evento($inscricao)
			->listarPorInscricao();

	}

}