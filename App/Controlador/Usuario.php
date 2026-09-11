<?php
//Definindo o namespace
namespace App\Controlador;

//Definindo as classes usadas
use App\Controlador\Principal\Controlador as ControladorPrincipal;
use App\Util\Email\Template\Email as EmailTemplate;
use App\Excecao\Conteudo as ConteudoException;
use App\Infraestrutura\Enum\Mensagem\Conteudo;
use App\Infraestrutura\Enum\Status\Http;
use App\Infraestrutura\Configuracao;
use App\Util\Validacao;
use App\Infraestrutura\Enum\Status\Status;
use App\Infraestrutura\Enum\Tipo\Perfil;
use App\Infraestrutura\Fabrica;
use App\Modelo\Inscricao\Evento\Inscricao;
use App\Util\Email;
use App\Util\Util;

/**
 * Controlador do cliente
 * Herdada da classe pai Controlador
 *
 * @author Caroline Tacats <caroline.tacats62@gmail.com>
 * @package App\Controlador
 */
class Usuario extends ControladorPrincipal
{

	/*
	 * AUTENTICAÇÕES
	 */

	/**
	 * Autenticando
	 *
	 * @uses App\Repositorio\Repositorio::login() Definindo o login para os métodos(chamada via _call)
	 * @uses App\Repositorio\Repositorio::senha() Definindo a senha para os métodos(chamada via _call)
	 * @uses App\Repositorio\API\Usuario::autenticar() Autenticando
	 * @return object
	 */
	public function autenticar(): object
	{

		// Validando
		Validacao::validarEmail($this->email);

		// Procurando o usuário por e-mail e status ativo
		$objeto = $this->complemento(
			$this
				->configurar(
					excecao: false
				)
				->email($this->email)
				->status(Status::ATIVO->value)
				->procurarPorStatusPorEmail()
		);

		// Verificando se o usuário existe
		if (!$objeto)
			throw new ConteudoException(Conteudo::AUTENTICACAO->texto(), Http::BAD_REQUEST->codigo());

		// Verificando a senha
		Validacao::validarSenha($this->senha, $objeto->senha);
		
		// Iniciando a sessão
		$this->iniciarSessao($objeto);

		// Retornando o objeto
		return $objeto;

	}

	public function inserir(array $campos, array $arquivo = []): mixed
	{

		// Validando os campos recebidos
		Validacao::validarArray($campos ?? null, 'campos do conteúdo');
		Validacao::validarArray($campos['lojas'] ?? null, 'lojas');
		Validacao::validarArray($campos['interesses'] ?? null, 'interesses');
		Validacao::validarCPF($campos['documento'] ?? null);
		
		// Determinar/verificar o perfil a partir do documento (CPF)
		$this->verificarEDefinirPerfilPorDocumento($campos);

		// Converter data de nascimento para formato DATE
		$this->converterDataNascimento($campos);

		// Definindo o status como ativo
		$campos['status'] = Status::ATIVO->value;

		// Definindo a senha
		$senha = Util::definirSenha();
		$campos['senha'] = $senha->hash;

		// Processar upload de imagem
		$this->processarImagem($campos, $arquivo);

		// Definindo o modelo
		$modelo = new $this->modelo($campos);

		// Verificando os campos únicos
		$this->verificarCamposUnicos($modelo, $campos);

		// Inserindo o conteúdo
		$id = $this->repositorio->inserir($modelo);

		// Definindo o ID no array de campos para relacionamentos
		$campos['id'] = $id;

		// Relacionando com lojas e interesses
		$this->processarRelacionamentos($campos);

		// Verificando se há inscrições nos eventos relacionados ao documento do usuário e atualizando o usuário nessas inscrições
		$this->verificarSeHaInscricaoNosEventos($id, $campos['documento'] ?? '');

		// Procurando
		$objeto = $this
			->id($id)
			->procurarPorIdentificador();

		// Iniciando a sessão automaticamente após o cadastro
		if ($objeto) {
			$this->iniciarSessao($objeto);
		}

		// Enviando e-mail de boas-vindas
		new Email(
			$objeto->email,
			Configuracao::get('projeto.nome') . ' - Seu acesso ao Plaza Mais chegou!',
			EmailTemplate::novoUsuario($objeto, $senha->senha)
		);

		// Retornando
		return $id;

	}

	/*
	 * EDIÇÕES
	 */
	/**
	 * Editando os dados do usuário autenticado.
	 *
	 * @param array $campos Campos para edição
	 * @param array $arquivo Arquivos enviados
	 * @uses App\Util\Validacao::validarArray() Validando os campos recebidos
	 * @uses App\Util\Validacao::verificarImagem() Validando a imagem enviada
	 * @uses App\Util\Util::formatarNomeArquivo() Formatando nome do arquivo
	 * @uses App\Repositorio\BDR\Usuario::editar() Persistindo edição do usuário
	 * @return void
	 */
	public function editar(array $campos = [], array $arquivo = []): void
	{

		// Validando os campos recebidos
		Validacao::validarArray($campos ?? null, 'campos do conteúdo');

		// Validando os campos de relacionamento, se fornecidos
		if (isset($campos['lojas']) && is_array($campos['lojas']) && isset($campos['interesses']) && is_array($campos['interesses'])) {
			Validacao::validarArray($campos['lojas'] ?? null, 'lojas');
			Validacao::validarArray($campos['interesses'] ?? null, 'interesses');
		}

		// Verificando se o ID do usuário foi fornecido
		$campos['id'] ?? throw new ConteudoException(Conteudo::NENHUM_CONTEUDO_ENCONTRADO->texto(), Http::BAD_REQUEST->codigo());
		
		// Obtendo o usuário atual para comparação e verificações
		$usuarioAtual = $this->id($campos['id'])->procurarPorIdentificador($campos['id']);

		// Verificar e atualizar perfil baseado no documento
		$this->processarDocumento($campos, $usuarioAtual);

		// Converter data de nascimento para formato DATE
		$this->converterDataNascimento($campos);

		// Relacionar com lojas e interesses
		$this->processarRelacionamentos($campos);

		// Processar mudanças de senha
		$senhaAlterada = $this->processarSenha($campos, $usuarioAtual->senha);

		// Processar upload de imagem
		$this->processarImagem($campos, $arquivo);

		// Criando o modelo para edição
		$modelo = new $this->modelo($campos);

		// Editando o conteúdo
		$this->repositorio->editar($modelo);

	}

	private function compararAlteracoes(object $usuarioAtual, array $campos): array
	{
		$alteracoes = [];

		foreach ($campos as $campo => $novoValor) {

			// Ignorar campos que não representam dados do usuário
			if (
				in_array($campo, [
					'id',
					'senha',
					'senha_atual',
					'nova-senha',
					'confirmar-senha',
					'senha_alterada'
				], true)
			) {
				continue;
			}


			if (!property_exists($usuarioAtual, $campo)) {
				continue;
			}

			$valorAnterior = $usuarioAtual->{$campo};

			// Normalizando datas para comparação
			if ($campo === 'data_nascimento') {
					$valorAnterior = Util::formatarData($valorAnterior, 'banco-sem-hora');
			}

			if ((string) $valorAnterior !== (string) $novoValor) {
				$alteracoes[$campo] = [
					'anterior' => $valorAnterior,
					'novo' => $novoValor
				];
			}
		}

		return $alteracoes;

	}

	/**
	 * Processar o documento (CPF) para verificar e definir o perfil do usuário, e verificar se há inscrições nos eventos relacionados a esse documento para atualizar o usuário nessas inscrições
	 *
	 * @param array $campos Campos de edição, passado por referência para ser modificado
	 * @param object $usuarioAtual Objeto do usuário atual antes da edição, para comparação
	 * @return void
	 */
	private function processarDocumento(array & $campos, object $usuarioAtual): void
	{
		// Definindo o documento
		$documento = !is_null($usuarioAtual->documento) ? $usuarioAtual->documento : $campos['documento'] ?? null;
		
		// Se o documento não estiver definido, não há como verificar o perfil ou as inscrições relacionadas, então retorna imediatamente
		if (!isset($documento)) {
			return;
		}

		// Validando o documento (CPF)
		Validacao::validarCPF($documento);

		// Verificando e definindo o perfil a partir do documento
		$this->verificarEDefinirPerfilPorDocumento($campos);

		// Verificando se há inscrições nos eventos relacionados ao documento do usuário e atualizando o usuário nessas inscrições
		$this->verificarSeHaInscricaoNosEventos($campos['id'], $documento);

	}

	/**
	 * Converter a data de nascimento do formato brasileiro (DD/MM/YYYY) para o formato DATE (YYYY-MM-DD) aceito pelo banco de dados
	 *
	 * @param array $campos Campos de edição, passado por referência para ser modificado
	 * @return void
	 */
	private function converterDataNascimento(array & $campos): void
	{

		// Se a data de nascimento não estiver definida ou estiver vazia, não há como converter, então retorna imediatamente
		if (!isset($campos['data_nascimento']) || empty($campos['data_nascimento'])) {
			return;
		}

		// Convertendo a data de nascimento para o formato DATE (YYYY-MM-DD)
		$data = str_replace('/', '-', $campos['data_nascimento']);
		$timestamp = strtotime($data);

		// Se a conversão falhar, lança uma exceção indicando que o formato da data de nascimento é inválido
		if ($timestamp === false) {
			throw new ConteudoException('Data de nascimento em formato inválido.', Http::BAD_REQUEST->codigo());
		}

		// Definindo o campo de data de nascimento para edição no formato DATE
		$campos['data_nascimento'] = date('Y-m-d', $timestamp);
	}

	/**
	 * Processar os relacionamentos do usuário com lojas e interesses
	 *
	 * @param array $campos Campos de edição, passado por referência para ser modificado
	 * @return void
	 */
	private function processarRelacionamentos(array & $campos): void
	{
		// Verificando e processando os relacionamentos com as lojas
		if (isset($campos['lojas']) && is_array($campos['lojas'])) {
			$this->relacionarComLojas($campos['id'], $campos['lojas']);
		}

		// Verificando e processando os relacionamentos com os interesses
		if (isset($campos['interesses']) && is_array($campos['interesses'])) {
			$this->relacionarComInteresses($campos['id'], $campos['interesses']);
		}

	}

	/**
	 * Processar a mudança de senha, verificando a senha atual e validando a nova senha
	 *
	 * @param array $campos Campos de edição, passado por referência para ser modificado
	 * @return bool
	 */
	private function processarSenha(array & $campos, ?string $senhaSalva): bool
	{
		// Verificando se a senha atual foi fornecida para validação
		if (!isset($campos['senha_atual']) || $campos['senha_atual'] === '') {
			return false;
		}

		// Senha atual informada pelo usuário
		$senhaAtualInformada = (string) $campos['senha_atual'];

		// Verificando a senha atual informada com a senha salva
		$senhaAtualValida 	= password_verify($senhaAtualInformada, $senhaSalva);

		// Verificação de compatibilidade com senhas salvas em MD5
		if (!$senhaAtualValida && $senhaSalva !== '') {
			$senhaAtualValida = hash_equals($senhaSalva, md5($senhaAtualInformada));
		}

		// Se a senha atual não for válida, lança uma exceção indicando que a senha atual está incorreta
		if (!$senhaAtualValida) {
			throw new ConteudoException(Conteudo::SENHA_ATUAL_INCORRETA->texto(), Http::BAD_REQUEST->codigo());
		}

		// Verificando se há nova senha para atualizar
		if (isset($campos['nova-senha']) && $campos['nova-senha'] !== '') {

			if ($campos['senha_atual'] === $campos['nova-senha']) {
				throw new ConteudoException(Conteudo::SENHA_NOVA_IGUAL_ATUAL->texto(), Http::BAD_REQUEST->codigo());
			}

			if ($campos['nova-senha'] !== ($campos['confirmar-senha'] ?? '')) {
				throw new ConteudoException(Conteudo::SENHA_NAO_COINCIDE->texto(), Http::BAD_REQUEST->codigo());
			}

			$campos['senha'] = password_hash($campos['nova-senha'], PASSWORD_BCRYPT, ['cost' => 12]);

			// Marca que a senha foi alterada
			return true;

		}

		return false;

	}

	/**
	 * Processar o upload de imagem, validando a imagem e movendo para o repositório, e definindo o campo de imagem para edição
	 *
	 * @param array $campos Campos de edição, passado por referência para ser modificado
	 * @param array $arquivo Arquivos enviados
	 * @uses App\Util\Validacao::verificarImagem() Validando a imagem enviada
	 * @uses App\Util\Util::formatarNomeArquivo() Formatando nome do arquivo
	 * @return void
	 */
	private function processarImagem(array & $campos, array $arquivo): void
	{
		// Verificando se um arquivo de imagem foi enviado
		if (!isset($arquivo['imagem']['name']) || $arquivo['imagem']['name'] === '') {
			return;
		}

		// Formatando o nome do arquivo
		$nomeArquivo = Util::formatarNomeArquivo($arquivo['imagem']['name']);
		Validacao::verificarImagem($arquivo['imagem']);

		// Definindo o caminho do arquivo e movendo para o repositório
		$caminhoArquivo = Configuracao::get('dir.repositorio') . "/usuario/{$nomeArquivo}";
		move_uploaded_file($arquivo['imagem']['tmp_name'], $caminhoArquivo);

		// Definindo o campo de imagem para edição
		$campos['imagem'] = $nomeArquivo;

	}
	
	/**
	 * Verificar e definir o perfil do usuário a partir do documento (CPF)
	 *
	 * @param array $campos Array de campos, passado por referência para ser modificado
	 * @return void
	 */
	private function verificarEDefinirPerfilPorDocumento(array & $campos): void
	{
		$documento = (string) ($campos['documento'] ?? '');

		// Procurar em Lojista
		if ($documento !== '' && $this->procurarCPFNoModelo('Lojista', $documento)) {
			$campos['perfil'] = Perfil::LOJISTA->value;
			return;
		}

		// Procurar em Membro
		if ($documento !== '' && $this->procurarCPFNoModelo('Membro', $documento)) {
			$campos['perfil'] = Perfil::MEMBRO_EQUIPE->value;
			return;
		}

		// Padrão: comum
		if (empty($campos['perfil'])) {
			$campos['perfil'] = Perfil::COMUM->value;
		}

	}

	/**
	 * Verificar se há inscrições nos eventos relacionados ao documento do usuário e atualizar o usuário nessas inscrições
	 *
	 * @param int $usuarioId ID do usuário recém-criado
	 * @param string $documento Documento (CPF) do usuário
	 * @uses App\Repositorio\Repositorio::documento() Definindo o documento para a busca
	 * @uses App\Repositorio\BDR\Repositorio::listar() Listando as inscrições relacionadas ao documento
	 * @uses App\Repositorio\BDR\Repositorio::editar() Editando as inscrições para relacionar com o novo usuário
	 * @return void
	 */
	private function verificarSeHaInscricaoNosEventos(int $usuarioId, string $documento): void
	{

		// Se o documento estiver vazio, não há como relacionar as inscrições, então retorna imediatamente
		if (empty($documento)) {
			return;
		}

		$inscricoesEventosFabrica 	= new Fabrica('Inscricao\Evento\Inscricao');

		$repositorio 				= $inscricoesEventosFabrica->repositorio;
		$repositorio->excecao 		= false;
		
		$inscricoesEventos 			= $repositorio->documento($documento)->listar();


		if (!empty($inscricoesEventos)) {

			$repositorioInscricao = $inscricoesEventosFabrica->repositorio;

			foreach ($inscricoesEventos as $evento) {

				$inscricaoAtualizada = new Inscricao([
					'id' => $evento->id,
					'usuario' => $usuarioId
				]);


				$repositorioInscricao->editar($inscricaoAtualizada);
			}

		}

	}

	// RELACIONAMENTOS
	/**
	 * Relacionar o usuário com as lojas selecionadas
	 *
	 * @param int $id ID do usuário
	 * @param array $lojas Array de IDs das lojas para relacionar
	 * @uses App\Repositorio\BDR\Repositorio::removerRelacionamento() Removendo os relacionamentos anteriores
	 * @uses App\Repositorio\BDR\Repositorio::relacionar() Criando os novos relacionamentos
	 * @return void
	 */
	public function relacionarComLojas(int $id, array $lojas): void
	{

		//Removendo o relacionamento
		$this->repositorio->removerRelacionamento($id, 'usuario_plaza_mais_loja', 'usuario');

		//Listando
		foreach ($lojas as $loja)
			//Relacionando
			$this->repositorio->relacionar($id, $loja, 'usuario_plaza_mais_loja', 'usuario', 'loja');

	}

	/**
	 * Relacionar o usuário com os interesses selecionados
	 *
	 * @param int $id ID do usuário
	 * @param array $interesses Array de IDs dos interesses para relacionar
	 * @uses App\Repositorio\BDR\Repositorio::removerRelacionamento() Removendo os relacionamentos anteriores
	 * @uses App\Repositorio\BDR\Repositorio::relacionar() Criando os novos relacionamentos
	 * @return void
	 */
	public function relacionarComInteresses(int $id, array $interesses): void
	{

		//Removendo o relacionamento
		$this->repositorio->removerRelacionamento($id, 'usuario_plaza_mais_interesse', 'usuario');

		//Listando
		foreach ($interesses as $interesse)
			//Relacionando
			$this->repositorio->relacionar($id, $interesse, 'usuario_plaza_mais_interesse', 'usuario', 'interesse');

	}

	/*
	 * UTILITÁRIOS
	 */

	/**
	 * Iniciando a sessão do cliente
	 *
	 * @param object $objeto Objeto do cliente
	 * @uses App\Infraestrutura\Configuracao\Configuracao::get() Retornando a variável do projeto
	 * @return void
	 */
	public function iniciarSessao(object $objeto): void
	{

		// Definindo o agente do usuário
		$agente = $_SERVER['HTTP_USER_AGENT'] ?? '';

		// Definindo os dados da sessão
		$dadosSessao = [
			'hash' 				=> hash_hmac('sha256', session_id() . $objeto->email . $agente, Configuracao::get('projeto.hash')),
			'id' 				=> $objeto->id,
			'nome' 				=> $objeto->nome,
			'perfil' 			=> $objeto->perfil,
			'email' 			=> $objeto->email,
			'perfil_completo' 	=> Util::verificarPerfilCompleto($objeto)
		];

		// Definindo o cookie do usuário para manter a autenticação por 30 dias
		setcookie('usuario', json_encode(['email' => $objeto->email]), time() + (86400 * 30), "/");

		//Mantém compatibilidade com o restante do sistema.
		$_SESSION['usuario'] = $dadosSessao;

	}

	/**
	 * Obtendo o usuário autenticado
	 *
	 * @param bool $complemento Definindo se o complemento deve ser obtido
	 * @uses App\Repositorio\Repositorio::id() Definindo o ID para os métodos(chamada via _call)
	 * @uses App\Repositorio\BDR\Repositorio::procurarPorIdentificador() Procurando o usuário por identificador
	 * @uses App\Controlador\Principal\Controlador::complemento() Obtendo o complemento do usuário
	 * @return object|null Retorna o objeto do usuário autenticado ou null se não houver usuário autenticado
	 */
	public function obterUsuarioAutenticado(bool $complemento = true): ?object
	{

		// Verificando se há um usuário autenticado na sessão
		if (!isset($_SESSION['usuario']['id'])) {
			return null;
		}

		// Obtendo o usuário autenticado
		$usuario = $this->complemento(
			$this
				->configurar(
					excecao: false,
					complemento: $complemento
				)
				->id($_SESSION['usuario']['id'])
				->procurarPorIdentificador()
		);

		// Retornando o usuário autenticado ou null se não encontrado
		return $usuario ?? null;

	}

	/**
	 * Redefinindo a senha do cliente
	 *
	 * @uses App\Repositorio\Repositorio::login() Definindo o login para os métodos(chamada via _call)
	 * @uses App\Repositorio\API\Usuario::esqueciMinhaSenha() Redefinindo a senha do cliente
	 * @uses App\Infraestrutura\Configuracao\Configuracao::get() Retornando a variável do projeto
	 * @uses App\Util\Email\Template\Email::esqueciMinhaSenha() Template do esqueci minha senha
	 * @return void
	 */
	public function esqueciMinhaSenha(): void
	{

		// Validando o e-mail
		Validacao::validarEmail($this->email);

		// Buscando o usuário por e-mail
		$objeto = $this->repositorio
			->email($this->email)
			->procurar();

		// Verificando se o usuário existe
		if (!$objeto)
			throw new ConteudoException(Conteudo::EMAIL_NAO_ENCONTRADO->texto(), Http::BAD_REQUEST->codigo());

		// Gerando nova senha
		$novaSenha = Util::definirSenha();

		// Definindo os campos para edição
		$campos = [
			'id' => $objeto->id,
			'senha' => $novaSenha->hash,
			'documento' => $objeto->documento ?? null
		];

		// Salvando a nova senha
		$this->editar($campos);

		// Enviando a nova senha por e-mail
		new Email(
			$objeto->email,
			'Sua nova senha - ' . Configuracao::get('projeto.nome'),
			EmailTemplate::esqueciMinhaSenha($objeto, $novaSenha->senha)
		);

	}

	/**
	 * PROCURAS
	 */
	/**
	 * Procurar CPF em um modelo específico
	 *
	 * @param string $modelo Nome do modelo (e.g., 'Lojista', 'Membro')
	 * @param string $cpf CPF normalizado (apenas dígitos)
	 * @return bool True se encontrado, false caso contrário
	 */
	private function procurarCPFNoModelo(string $modelo, string $cpf): bool
	{
		// Criando uma fábrica para o modelo especificado
		$fabrica = new Fabrica($modelo);

		// Verificando se há um registro com o CPF fornecido
		$existe = (int) ($fabrica->repositorio->cpf($cpf)->contar() ?? 0) > 0;

		// Retornando true se encontrado, false caso contrário
		return $existe ? true : false;

	}

	/**
	 * Procurando usuário por status e e-mail.
	 *
	 * @uses App\Repositorio\Repositorio::status() Definindo status para a busca
	 * @uses App\Repositorio\Repositorio::email() Definindo e-mail para a busca
	 * @uses App\Repositorio\BDR\Repositorio::procurar() Procurando usuário
	 * @return mixed
	 */
	public function procurarPorStatusPorEmail(): mixed
	{

		//Retornando
		return $this->repositorio
			->status($this->status)
			->email($this->email)
			->procurar();

	}

	/**
	 * Inativando o perfil do usuário autenticado.
	 * 
	 * @return void
	 */
	public function inativarPerfil(): void
	{

		$objeto = $this->complemento(
			$this
				->configurar( excecao: false )
				->id($this->id)
				->procurarPorIdentificador()
		);

		// Verificando a senha
		Validacao::validarSenha($this->senha, $objeto->senha);
		
		// Editando o status do usuário
		$this->repositorio->editarStatus($this->id, $this->status);

	}

}