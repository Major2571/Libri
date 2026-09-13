<?php
//Definindo o namespace
namespace App\Util;

//Definindo as classes usadas
use App\Infraestrutura\Enum\Mensagem\BancoDeDados;
use App\Excecao\Conteudo as ConteudoException;
use App\Infraestrutura\Enum\Mensagem\Conteudo;
use App\Infraestrutura\Enum\Status\Http;
use App\Infraestrutura\Configuracao;
use App\Infraestrutura\Enum\Status\Status;
use App\Infraestrutura\Template;
use App\Infraestrutura\Pagina;
use IntlDateFormatter;
use DateTime;
use Exception;
use stdClass;

/**
 * Classe de funções úteis para o projeto
 *
 * @final
 * @author Caroline Tacats <caroline.tacats62@gmail.com>
 * @package App\Util
 */
final class Util
{

	/*
	 * VERIFICAÇÕES
	 */

	/**
	 * Verificando os erros de banco de dados
	 *
	 * @static
	 * @param null|int $tipo Tipo do erro
	 * @return string
	 */
	public static function verificarTipoErroBancoDeDados(?int $tipo = null): string
	{

		//Retornando
		return match ($tipo) {

			//Relacionamento
			1451 => BancoDeDados::RELACIONAMENTO->texto(),
			//inserção e edição
			1452 => BancoDeDados::INSERCAO_EDICAO->texto(),
			//Registros duplicados
			1062 => BancoDeDados::DUPLICADO->texto(),
			//Padrão
			default => BancoDeDados::PADRAO->texto()
		};

	}

	/**
	 * Verificando a requisição
	 *
	 * @static
	 * @param string $tipo Tipo
	 * @throws ConteudoException
	 * @return void
	 */
	public static function verificarRequisicao(string $tipo): void
	{

		//Verificando
		if ($tipo !== $_SERVER['REQUEST_METHOD'])
			//Lançando a exceção
			throw new ConteudoException(sprintf(Conteudo::TIPO_REQUISICAO->texto(), $_SERVER['REQUEST_METHOD'], $tipo), Http::BAD_REQUEST->codigo());

	}

	/**
	 * Verificando a referência
	 *
	 * @static
	 * @uses App\Infraestrutura\Configuracao\Configuracao::get() Retornando a variável do projeto
	 * @return bool
	 */
	public static function verificarReferencia(): bool
	{

		//Retornando
		return isset($_SERVER['HTTP_REFERER']) && strpos($_SERVER['HTTP_REFERER'], Configuracao::get('projeto.referencia')) !== false;

	}

	/**
	 * Verificando o token CSRF
	 *
	 * @static
	 * @param ?string $token Token
	 * @throws ConteudoException
	 * @return bool
	 */
	public static function verificarTokenCsrf(?string $token = null): bool
	{

		//Verificando e lançando a exceção
		$_SESSION ?? throw new ConteudoException(Conteudo::SESSAO_INEXISTENTE->texto(), Http::BAD_REQUEST->codigo());

		//Verificando e lançando a exceção
		$_SESSION['csrf-token'] ?? throw new ConteudoException(Conteudo::TOKEN_INVALIDO->texto(), Http::BAD_REQUEST->codigo());

		//Verificando o token junto ao hash
		if (!hash_equals($_SESSION['csrf-token'], $token ?? ''))
			//Lançando a exceção
			throw new ConteudoException(Conteudo::TOKEN_INVALIDO->texto(), Http::BAD_REQUEST->codigo());

		//Retornando
		return true;

	}

	/*
	 * FORMATAÇÕES
	 */

	/**
	 * Formatando os caracteres
	 *
	 * @static
	 * @param mixed $valor Valor
	 * @return string
	 */
	public static function formatarCaracteres(mixed $valor): mixed
	{

		//Retornando
		return !is_null($valor) ? stripslashes(htmlentities($valor, ENT_QUOTES, 'UTF-8')) : null;

	}

	/**
	 * Formatando caracteres para apenas números
	 *
	 * @static
	 * @param null|string String
	 * @return string
	 */
	public static function formatarApenasNumeros(?string $string): string
	{

		//Retornando
		return preg_replace('/\D+/', '', $string ?? '');

	}

	/**
	 * Formatando a data
	 *
	 * @static
	 * @param null|string $data Data
	 * @param string $tipo Tipo
	 * @link https://unicode-org.github.io/icu/userguide/format_parse/datetime/#date-field-symbol-table Documentation
	 * @return mixed
	 */
	public static function formatarData(?string $data, string $tipo = 'padrão'): mixed
	{

		//Verificando
		if (!is_null($data)) {

			//Definindo
			$formato = match ($tipo) {

				'dia-da-semana' => "EEEE",
				//Banco de dados
				'banco' => "yyyy-MM-dd HH:mm:ss",
				//Banco de dados sem hora
				'banco-sem-hora' => "yyyy-MM-dd",
				//Dia e mês
				'dia-mês' => "dd/MM",
				//Hora
				'hora' => "H:mm",
				//Mês e ano
				'mês-ano' => 'MMM/yyyy',
				//Mês
				'mês' => 'MMMM',
				//Ano
				'ano' => 'yyyy',
				//Português sem hora
				'pt-sem-hora' => "dd/MM/yyyy",
				//Mês e ano por extenso
				'mês-ano-extenso' => "MMMM 'de' yyyy",
				//Dia e mês por extenso
				'dia-mês-ano-extenso' => "dd 'de' MMMM 'de' yyyy",
				//Dia e mês por extenso
				'dia-mês-extenso' => "dd 'de' MMMM",
				//Período do email
				'período-email' => "dd/MM/yyyy - H:mm:ss",
				//Atualização das vendas
				'atualizacao-vendas' => "dd/MM/yyyy '<span>&bull;</span>' HH:mm",
				//Padrão
				default => "dd/MM/yyyy à's' HH'h'mm"
			};

			$formatacao = new IntlDateFormatter('pt_BR', IntlDateFormatter::FULL, IntlDateFormatter::NONE, 'America/Recife', IntlDateFormatter::GREGORIAN, $formato);

			//Retornando
			return str_replace([' - 00:00:00', ' - 00:00'], '', $formatacao->format(new DateTime(str_replace('/', '-', str_replace('-', '', $data)))));

		} else
			//Retornando
			return null;

	}

	/**
	 * Formatando uma string para o formato de URL
	 * Geralmente usamos para criar um slug
	 *
	 * @static
	 * @param string $string String
	 */
	public static function formatarStringParaUrl(string $string): string
	{

		//Retornando
		return strtolower(str_replace(' ', '-', self::removerCaracterEspecial(html_entity_decode($string, ENT_QUOTES, 'UTF-8'))));

	}

	/**
	 * Formatando o nome do arquivo
	 *
	 * @static
	 * @param string $nomeArquivo Nome do arquivo
	 * @return string
	 */
	public static function formatarNomeArquivo(string $nomeArquivo): string
	{

		//Definindo a extensão
		$extensao = pathinfo($nomeArquivo, PATHINFO_EXTENSION);

		//Formatando o nome do arquivo
		return strtolower(preg_replace('/[^\w\._]+/', '', pathinfo($nomeArquivo, PATHINFO_FILENAME))) . '-' . str_replace('.', '', microtime(true)) . ".{$extensao}";

	}

	/**
	 * Definindo o cache dos arquivos
	 *
	 * @param string $caminho Caminho absoluto
	 * @param Pagina $pagina Objeto da página
	 * @return null|string
	 */
	public static function definirCache(string $arquivo, Pagina $pagina): null|string
	{


		//Verificando e definindo se é um arquivo de interna
		$complemento = ($pagina->acao === 'exibir' || $pagina->acao === 'cancelar-inscricao') && $arquivo !== 'bundle' ? '-interna' : null;

		//Definindo o caminho
		$caminho = Configuracao::get('dir.js') . "/{$arquivo}{$complemento}.js";

		//Retornando
		return file_exists($caminho) ? Configuracao::get('url.js') . "/{$arquivo}{$complemento}.js?v=" . filemtime($caminho) : null;

	}

	/**
	 * Definindo o token CSRF
	 *
	 * @static
	 * @return string
	 */
	public static function definirTokenCsrf(): string
	{

		//Verificando a existência do token
		if (empty($_SESSION['csrf-token'])) {

			//Definindo
			$token = bin2hex(random_bytes(32));

			//Definindo
			$_SESSION['csrf-token'] = $token;

		} else
			//Definindo o token
			$token = $_SESSION['csrf-token'];

		//Retornando
		return $token;

	}

	/**
	 * Definindo a ordem dos registros para o banco de dados
	 *
	 * @static
	 * @param null|string $ordem Ordem
	 * @return string
	 */
	public static function definirOrdemBancoDeDados(?string $ordem = null): string
	{

		//Retornando
		return match ($ordem) {

			//Identificador
			'id' => 'id',
			'id-desc' => 'id DESC',
			//Nome
			'nome' => 'nome',
			//Sequência
			'sequência' => 'ordem',
			//Data
			'data' => 'data',
			//Dia
			'dia' => 'dia',
			//Randômico
			'randômico' => 'RAND()',
			//Data de publicação DESC
			'data-publicação-desc' => 'data_publicacao DESC',
			'data-criacao-desc' => 'data_criacao DESC',
			'data-criacao' => 'data_criacao',
			'data-expiracao' => 'data_expiracao DESC',
			//Valor
			'valor' => 'valor',
			//Padrão
			default => 'id DESC'
		};

	}

	/**
	 * Definindo o ambiente se é produção ou desenvolvimento
	 *
	 * @static
	 * @param Template $template Objeto do template
	 * @param Pagina $pagina Objeto da página
	 * @uses App\Infraestrutura\Configuracao\Configuracao::get() Retornando a variável do projeto
	 * @return void
	 */
	public static function definirAmbiente(Template $template, Pagina $pagina): void
	{

		//Definindo o complemento do css
		$complemento = ($pagina->acao === 'exibir' || $pagina->acao === 'cancelar-inscricao') && in_array($pagina->diretorio, Pagina::PAGINAS_COM_INTERNA) ? '-interna' : null;

		//Verificando se o projeto está em produção ou não
		if (Configuracao::get('projeto.producao')) {

			//Definindo o diretório do CSS
			$dirCss = Configuracao::get('dir.css');

			//Incluindo o arquivo do CSS
			$template->addFile('CSS_BUNDLE', "{$dirCss}/bundle.css");
			$template->addFile('CSS_PAGINA', "{$dirCss}/{$pagina->diretorio}{$complemento}.css");

			//Exibindo o bloco
			$template->block('ESTILO_PRODUCAO');

		} else {

			//Definindo o arquivo CSS
			$template->ARQUIVO_CSS = "{$pagina->diretorio}{$complemento}";

			//Exibindo o bloco
			$template->block('ESTILO_DESENVOLVIMENTO');

		}


	}

	/**
	 * Definindo o método único
	 *
	 * @static
	 * @param string $campo Campo
	 * @return object
	 */
	public static function definirMetodoUnico(string $campo): object
	{

		//Definindo o objeto
		$objeto = new \stdClass();

		//Retornando o objeto
		return match ($campo) {

			//Login
			'login' => (function ($objeto) {

					//Definindo os parâmetros
					$objeto->termo = 'login';
					$objeto->metodo = 'contarPorLogin';
					$objeto->excecao = Conteudo::JA_EXISTE_POR_LOGIN->texto();

					//Retornando o objeto
					return $objeto;

				})($objeto),

			//Email
			'email' => (function ($objeto) {

					//Definindo os parâmetros
					$objeto->termo = 'email';
					$objeto->metodo = 'contarPorEmail';
					$objeto->excecao = Conteudo::JA_EXISTE_POR_EMAIL->texto();

					//Retornando o objeto
					return $objeto;

				})($objeto),

			'documento' => (function ($objeto, $campo) {

					//Definindo os parâmetros
					$objeto->termo = 'documento';
					$objeto->metodo = 'contarPorDocumento';
					$objeto->excecao = Conteudo::JA_EXISTE_POR_DOCUMENTO->texto();

					//Retornando o objeto
					return $objeto;

				})($objeto, $campo),

		};

	}

	public static function definirLimitador(?int $pagina): int
	{

		//Retornando
		return $pagina > 0 ? ($pagina - 1) * Configuracao::get('projeto.quantidade_paginacao') : 0;

	}

	/**
	 * Sanitiza valores recursivamente (string ou array).
	 *
	 * @static
	 * @param mixed $valor Valor
	 * @return mixed
	 */
	public static function sanitizar(mixed $valor): mixed
	{

		//Verificando
		if (is_array($valor))
			//Retornando
			return array_map([self::class, 'sanitizar'], $valor);

		//Verificando
		if (is_string($valor))
			//Retornando
			return htmlspecialchars(trim($valor), ENT_QUOTES, 'UTF-8');

		//Retornando
		return $valor;

	}

	/*
	 * REMOÇÕES
	 */

	/**
	 * Removendo os caracteres especiais
	 *
	 * @static
	 * @param string $string String
	 * @return string
	 */
	public static function removerCaracterEspecial(string $string): string
	{

		//Minimizando
		$string = strtolower($string);

		//Removendo os acentos e caracteres
		$string = preg_replace('/[áàãâä]/u', 'a', $string);
		$string = preg_replace('/[éèêë]/u', 'e', $string);
		$string = preg_replace('/[íìîï]/u', 'i', $string);
		$string = preg_replace('/[óòõôö]/u', 'o', $string);
		$string = preg_replace('/[úùûü]/u', 'u', $string);
		$string = preg_replace('/[ç]/u', 'c', $string);
		$string = preg_replace('/[ñ]/u', 'n', $string);
		$string = preg_replace('/[^a-z0-9\s-]/', '', $string);
		$string = preg_replace('/[\s-]+/', '-', $string);

		//Retornando
		return trim($string);

	}

	/*
	 * REDIRECIONAMENTOS
	 */

	/**
	 * Redirecionando
	 *
	 * @param int $tipo Tipo de redirecionamento
	 * @param string $pagina Página para redirecionamento
	 * @return void
	 */
	public static function redirecionar(int $tipo, string $pagina): void
	{

		//Definindo o cabeçalho
		$cabecalho = match ($tipo) {

			301 => 'HTTP/1.1 301 Moved Permanently',
			302 => 'HTTP/1.1 302 Found',
			303 => 'HTTP/1.1 303 See Other',
			401 => 'HTTP/1.1 401 Unauthorized',
			403 => 'HTTP/1.1 403 Forbidden',
			404 => 'HTTP/1.1 404 Not Found',
			503 => 'HTTP/1.1 503 Service Unavailable'
		};

		//Definindo o cabeçalho e redirecionando
		header($cabecalho);
		header("Location: {$pagina}");

		//Finalizando o processo
		die();

	}

	/**
	 * Definindo o sufixo e a moeda da língua para os campos de tradução não automática
	 *
	 * @static
	 * @return array
	 */
	public static function definirOpcaoLingua(): array
	{

		//Retornando a língua
		return match ($_SESSION['lingua'] ?? null) {

			//Inglês
			'en_US' => ['sufixo' => '_ing', 'moeda' => '$ ', 'api' => 'EN', 'js' => 'en-US'],
			//Espanhol
			'es_ES' => ['sufixo' => '_esp', 'moeda' => '€ ', 'api' => 'ES', 'js' => 'es-ES'],
			//Português de Portugal
			'pt_PT' => ['sufixo' => '_pt', 'moeda' => '€ ', 'api' => 'PTPT', 'js' => 'pt-PT'],
			//Padrão
			default => ['sufixo' => null, 'moeda' => 'R$ ', 'api' => 'PTBR', 'js' => 'pt-BR']
		};

	}

	/**
	 * Definindo a senha aleatória e o hash para o banco de dados
	 *
	 * @static
	 * @param int $quantidade Quantidade de caracteres da senha
	 * @return object Objeto com a senha e o hash
	 */
	public static function definirSenha(int $quantidade = 15): object
	{

		//Definindo o objeto
		$objeto = new \stdClass();

		//Definindo os caracteres para compor a senha
		$caracteres = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789!#$%*()_=+[]{}|;:<>?';

		//Iniciando a criação
		$senha = '';

		//Definindo o tamanho
		$tamanho = mb_strlen($caracteres, '8bit') - 1;

		//Listando o tamanho para geração da senha
		foreach (range(1, $quantidade) as $i)
			//Concatenando a senha e criando
			$senha .= $caracteres[random_int(0, $tamanho)];

		//Definindo a senha
		$objeto->senha = $senha;

		//Definindo o hash da senha
		$objeto->hash = password_hash($senha, PASSWORD_BCRYPT, ['cost' => 12]);

		//Retornando
		return $objeto;

	}

	/**
	 * Gerando um hash seguro para o identificador
	 *
	 * @static
	 * @param int $id Identificador numérico
	 * @param string|null $hashKey Chave secreta para gerar o hash
	 * @return string Hash seguro concatenado com o ID codificado em base64
	 */
	public static function gerarHashParaIdentificador(int $id, ?string $hashKey)
	{

		$hash = hash_hmac('sha256', $id, $hashKey);
		$baseIdEncode = base64_encode($id);

		return $hash . $baseIdEncode;
	}

	/**
	 * Validando e decodificando o identificador seguro
	 *
	 * @static
	 * @param string|null $secureId Identificador seguro (hash + ID codificado)
	 * @param string|null $hashKey Chave secreta para validar o hash
	 * @return int ID decodificado se válido, ou lança exceção se inválido
	 * @throws Exception Se o identificador for inválido
	 */
	public static function validarEDecodificarIdentificador(?string $secureId, ?string $hashKey)
	{
		$hashLength = 64;
		$hash = substr($secureId, 0, $hashLength);
		$baseIdEncode = substr($secureId, $hashLength);
		$id = base64_decode($baseIdEncode);
		$validHash = hash_hmac('sha256', $id, $hashKey);

		if (hash_equals($validHash, $hash)) {
			return $id;
		} else {
			throw new Exception('Identificador inválido!');
		}

	}

	public static function definirAtalho(object $pagina): object
	{

		//Definindo o objeto
		$objeto = new stdClass();

		//Definindo o diretório
		$objeto->diretorio = $pagina->diretorio;

		//Definindo a ação
		$pagina->acao = str_contains($pagina->acao, 'editar') ? 'editar' : $pagina->acao;

		//Verificando a ação
		switch ($pagina->acao) {

			//Página de listagem
			case 'index':

				//Definindo o objeto
				$objeto->titulo = 'Ver todos';
				$objeto->atalho = $pagina->nome_modelo;

				break;

			//Página de inserção
			case 'novo':

				//Definindo o objeto
				$objeto->titulo = 'Novo';
				$objeto->atalho = $pagina->nome_modelo;

				break;

			//Página de edição
			case 'editar':

				//Definindo o objeto
				$objeto->titulo = 'Editar';
				$objeto->atalho = $pagina->nome_modelo;

				break;

			//Página de exibição
			case 'exibir':

				//Definindo o objeto
				$objeto->titulo = 'Exibir';
				$objeto->atalho = $pagina->nome_modelo;

				break;
		}

		//Retornando
		return $objeto;

	}
}
