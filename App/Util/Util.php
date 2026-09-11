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
use DateTimeImmutable;
use Exception;

/**
 * Classe de funções úteis para o projeto
 *
 * @final
 * @author Caroline Tacats <caroline.tacats62@gmail.com>
 * @package App\Util
 */
final class Util{

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
	public static function verificarTipoErroBandoDeDados( ?int $tipo = null ): string{

		//Retornando
		return match( $tipo ){

			//Relacionamento
			1451 	=> BancoDeDados::RELACIONAMENTO->texto(),
			//inserção e edição
			1452	=> BancoDeDados::INSERCAO_EDICAO->texto(),
			//Registros duplicados
			1062	=> BancoDeDados::DUPLICADO->texto(),
			//Padrão
			default	=> BancoDeDados::PADRAO->texto() };

	}

	/**
	 * Verificando a requisição
	 *
	 * @static
	 * @param string $tipo Tipo
	 * @throws ConteudoException
	 * @return void
	 */
	public static function verificarRequisicao( string $tipo ): void{

		//Verificando
		if( $tipo !== $_SERVER[ 'REQUEST_METHOD' ] )
			//Lançando a exceção
			throw new ConteudoException( sprintf( Conteudo::TIPO_REQUISICAO->texto(), $_SERVER[ 'REQUEST_METHOD' ], $tipo ), Http::BAD_REQUEST->codigo() );

	}

	/**
	 * Verificando a referência
	 *
	 * @static
	 * @uses App\Infraestrutura\Configuracao\Configuracao::get() Retornando a variável do projeto
	 * @return bool
	 */
	public static function verificarReferencia(): bool{

		//Retornando
		return isset( $_SERVER[ 'HTTP_REFERER' ] ) && strpos( $_SERVER[ 'HTTP_REFERER' ], Configuracao::get( 'projeto.referencia' ) ) !== false;

	}

	/**
	 * Verificando se o perfil do usuário está completo
	 *
	 * @static
	 * @param object $usuario Objeto do usuário
	 * @return bool
	 */
	public static function verificarPerfilCompleto( object $usuario ): bool{

		$perfil = (int) ($usuario->perfil ?? 0);

		if ($perfil <= 0) {
			return false;
		}

		$camposObrigatorios = [
			['nome'],
			['documento', 'cpf'],
			['telefone', 'celular'],
			['data_nascimento', 'dataNascimento']
		];

		foreach ($camposObrigatorios as $campos) {
			if (!self::possuiValorPreenchido($usuario, $campos)) {
				return false;
			}
		}

		return true;

	}

	/**
	 * Verificando se existe valor preenchido em um dos campos informados
	 *
	 * @static
	 * @param object $usuario Objeto do usuário
	 * @param array $campos Campos a verificar
	 * @return bool
	 */
	private static function possuiValorPreenchido( object $usuario, array $campos ): bool{

		foreach ($campos as $campo) {
			$valor = $usuario->{$campo} ?? null;

			if (is_string($valor)) {
				$valor = trim($valor);
			}

			if ($valor !== null && $valor !== '' && $valor !== '0' && $valor !== 0) {
				return true;
			}
		}

		return false;

	}

	/**
	 * Verificando o token CSRF
	 *
	 * @static
	 * @param ?string $token Token
	 * @throws ConteudoException
	 * @return bool
	 */
	public static function verificarTokenCsrf( ?string $token = null ): bool{

		//Verificando e lançando a exceção
		$_SESSION ?? throw new ConteudoException( Conteudo::SESSAO_INEXISTENTE->texto(), Http::BAD_REQUEST->codigo() );

		//Verificando e lançando a exceção
		$_SESSION[ 'csrf-token' ] ?? throw new ConteudoException( Conteudo::TOKEN_INVALIDO->texto(), Http::BAD_REQUEST->codigo() );

		//Verificando o token junto ao hash
		if( !hash_equals( $_SESSION[ 'csrf-token' ], $token ?? '' ) )
			//Lançando a exceção
			throw new ConteudoException( Conteudo::TOKEN_INVALIDO->texto(), Http::BAD_REQUEST->codigo() );

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
	public static function formatarCaracteres( mixed $valor ): mixed{

		//Retornando
		return !is_null( $valor ) ? stripslashes( htmlentities( $valor, ENT_QUOTES, 'UTF-8' ) ) : null;

	}

	/**
	 * Gerando as opções de lojas agrupadas por categoria
	 *
	 * @static
	 * @param ?iterable $lojas Lojas
	 * @param array<int, bool> $idsSelecionados IDs selecionados
	 * @param string $mensagemVazia Mensagem exibida quando não houver lojas
	 * @return string
	 */
	public static function gerarOpcoesLojas( ?iterable $lojas, array $idsSelecionados = [], string $mensagemVazia = 'Nenhuma loja encontrada.' ): string{

		// Iniciando os grupos de lojas por categoria
		$grupos = [];

		// Agrupando as lojas por categoria
		foreach ($lojas as $loja) {
			$categoria 			  = $loja->categoria->nome ?? 'Sem categoria';
			$grupos[$categoria][] = $loja;
		}

		// Verificando se existem lojas para exibir
		if (empty($grupos)) {
			return '<option value="">' . $mensagemVazia . '</option>';
		}

		// Ordenando os grupos por categoria
		ksort($grupos);

		// Gerando o HTML das opções de lojas agrupadas por categoria
		$html = '';

		// Iterando sobre os grupos para gerar as opções
		foreach ($grupos as $categoria => $itens) {

			// Gerando o grupo de opções para a categoria
			$html .= '<optgroup label="' . $categoria . '">';

			// Iterando sobre as lojas do grupo para gerar as opções
			foreach ($itens as $loja) {

				$id 	  = (int) ($loja->id ?? 0);
				$nome 	  = (string) ($loja->nome ?? '');
				$selected = isset($idsSelecionados[$id]) ? ' selected' : '';

				$html .= '<option value="' . $id . '"' . $selected . '>'
					. $nome
					. '</option>';
			}

			// Fechando o grupo de opções
			$html .= '</optgroup>';
		}

		// Retornando o HTML
		return $html;

	}

	/**
	 * Formatando caracteres para apenas números
	 *
	 * @static
	 * @param null|string String
	 * @return string
	 */
	public static function formatarApenasNumeros( ?string $string ): string{

		//Retornando
		return preg_replace( '/\D+/', '', $string ?? '' );

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
	public static function formatarData( ?string $data, string $tipo = 'padrão' ): mixed{

		//Verificando
		if( !is_null( $data ) ){

			//Definindo
			$formato	= match( $tipo ){

				'dia-da-semana'			=> "EEEE",
				//Banco de dados
				'banco'					=> "yyyy-MM-dd HH:mm:ss",
				//Banco de dados sem hora
				'banco-sem-hora'		=> "yyyy-MM-dd",
				//Dia e mês
				'dia-mês'				=> "dd/MM",
				//Hora
				'hora'					=> "H:mm",
				//Mês e ano
				'mês-ano'				=> 'MMM/yyyy',
				//Mês
				'mês'					=> 'MMMM',
				//Ano
				'ano'					=> 'yyyy',
				//Português sem hora
				'pt-sem-hora'			=> "dd/MM/yyyy",
				//Mês e ano por extenso
				'mês-ano-extenso'	    => "MMMM 'de' yyyy",
				//Dia e mês por extenso
				'dia-mês-ano-extenso'	=> "dd 'de' MMMM 'de' yyyy",
				//Dia e mês por extenso
				'dia-mês-extenso'		=> "dd 'de' MMMM",
				//Período do email
				'período-email'			=> "dd/MM/yyyy - H:mm:ss",
				//Atualização das vendas
				'atualizacao-vendas'	=> "dd/MM/yyyy '<span>&bull;</span>' HH:mm",
				//Padrão
				default					=> "dd/MM/yyyy à's' HH'h'mm" };

			$formatacao	= new IntlDateFormatter( 'pt_BR', IntlDateFormatter::FULL, IntlDateFormatter::NONE, 'America/Recife', IntlDateFormatter::GREGORIAN, $formato );

			//Retornando
			return str_replace( [ ' - 00:00:00', ' - 00:00' ], '', $formatacao->format( new DateTime( str_replace( '/', '-', str_replace( '-', '', $data ) ) ) ) );

		}else
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
	public static function formatarStringParaUrl( string $string ): string{

		//Retornando
		return strtolower( str_replace( ' ', '-', self::removerCaracterEspecial( html_entity_decode( $string, ENT_QUOTES, 'UTF-8' ) ) ) );

	}

	/**
	 * Formatando o nome do arquivo
	 *
	 * @static
	 * @param string $nomeArquivo Nome do arquivo
	 * @return string
	 */
	public static function formatarNomeArquivo( string $nomeArquivo ): string{

		//Definindo a extensão
		$extensao	= pathinfo( $nomeArquivo, PATHINFO_EXTENSION );

		//Formatando o nome do arquivo
		return strtolower( preg_replace( '/[^\w\._]+/', '', pathinfo( $nomeArquivo, PATHINFO_FILENAME ) ) ) . '-' . str_replace( '.', '', microtime( true ) ) . ".{$extensao}";

	}

	/*
	 * DEFINIÇÕES
	 */

	/**
	 * Definindo a descrição para a metatag
	 *
	 * @static
	 * @param ?string $texto Texto
	 * @param bool $tag Remover as tags ou não
	 * @return string
	 */
	public static function definirDescricao( ?string $texto, bool $tag = true ): string{

		//Definindo o texto limpo
		$textoLimpo	= preg_replace( "/\r\n|\r|\n|\t/", '', strip_tags( $texto ?? '' ) );

		//Verifica se o texto possui mais de 160 caracteres
		if( strlen( $textoLimpo ) > 160 )
			//Trunca o texto no limite de 157 caracteres e adiciona reticências
			$textoLimpo	= substr($textoLimpo, 0, 157) . '...';

		//Retorna a descrição com ou sem tags, conforme a opção
		return $tag ? htmlspecialchars( trim( $textoLimpo ), ENT_QUOTES, 'UTF-8' ) : $textoLimpo;

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
		$complemento = ( $pagina->acao === 'exibir' || $pagina->acao === 'cancelar-inscricao' ) && $arquivo !== 'bundle' ? '-interna' : null;
		
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
	public static function definirTokenCsrf(): string{

		//Verificando a existência do token
		if( empty( $_SESSION[ 'csrf-token' ] ) ){

			//Definindo
			$token						= bin2hex( random_bytes( 32 ) );

			//Definindo
			$_SESSION[ 'csrf-token' ]	= $token;

		}else
			//Definindo o token
			$token	= $_SESSION[ 'csrf-token' ];

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
	public static function definirOrdemBancoDeDados( ?string $ordem = null ): string{

		//Retornando
		return match( $ordem ){

			//Identificador
			'id'					=> 'id',
			//Nome
			'nome'					=> 'nome',
			//Sequência
			'sequência'				=> 'ordem',
			//Data
			'data'					=> 'data',
			//Dia
			'dia'					=> 'dia',
			//Randômico
			'randômico'				=> 'RAND()',
			//Data de publicação DESC
			'data-publicação-desc'	=> 'data_publicacao DESC',
			'data-criacao'	=> 'data_criacao DESC',
			'data-expiracao'	=> 'data_expiracao DESC',
			//Valor
			'valor'					=> 'valor',
			//Padrão
			default					=> 'id DESC' };

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
	public static function definirAmbiente( Template $template, Pagina $pagina ): void{

		//Definindo o complemento do css
		$complemento = ( $pagina->acao === 'exibir' || $pagina->acao === 'cancelar-inscricao' ) && in_array($pagina->diretorio, Pagina::PAGINAS_COM_INTERNA) ? '-interna' : null;

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
	 * Definindo o nome da constante
	 *
	 * @static
	 * @param string $nome Nome
	 * @return string
	 */
	public static function definirNomeConstante( string $nome ): string{

		//Retornando
		return str_replace( [ '---', '-' ], '_', strtoupper( self::formatarStringParaUrl( $nome ) ) );

	}

	/**
	 * Definindo o método único
	 *
	 * @static
	 * @param string $campo Campo
	 * @return object
	 */
	public static function definirMetodoUnico( string $campo ): object{

		//Definindo o objeto
		$objeto	= new \stdClass();

		//Retornando o objeto
		return match( $campo ){

			//Login
			'login'	=> ( function( $objeto ){

				//Definindo os parâmetros
				$objeto->termo		= 'login';
				$objeto->metodo		= 'contarPorLogin';
				$objeto->excecao	= Conteudo::JA_EXISTE_POR_LOGIN->texto();

				//Retornando o objeto
				return $objeto;

			} )( $objeto ),

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

	/**
	 * Definindo o IP do usuário
	 *
	 * @static
	 * @return string
	 */
	public static function definirIP(): string{

		//Retornando
	    return getenv( 'HTTP_CLIENT_IP' ) ?: getenv( 'HTTP_X_FORWARDED_FOR' ) ?: getenv( 'HTTP_X_FORWARDED' ) ?: getenv( 'HTTP_FORWARDED_FOR' ) ?: getenv( 'HTTP_FORWARDED' ) ?: getenv( 'REMOTE_ADDR' );

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
	public static function removerCaracterEspecial( string $string ): string{

		//Minimizando
		$string	= strtolower( $string );

		//Removendo os acentos e caracteres
		$string	= preg_replace( '/[áàãâä]/u', 'a', $string );
		$string = preg_replace( '/[éèêë]/u', 'e', $string );
		$string = preg_replace( '/[íìîï]/u', 'i', $string );
		$string = preg_replace( '/[óòõôö]/u', 'o', $string );
		$string = preg_replace( '/[úùûü]/u', 'u', $string );
		$string = preg_replace( '/[ç]/u', 'c', $string );
		$string = preg_replace( '/[ñ]/u', 'n', $string );
		$string = preg_replace( '/[^a-z0-9\s-]/', '', $string );
		$string = preg_replace( '/[\s-]+/', '-', $string );

		//Retornando
		return trim( $string );

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
	public static function redirecionar( int $tipo, string $pagina ): void{

		//Definindo o cabeçalho
		$cabecalho	= match( $tipo ){

			301	=> 'HTTP/1.1 301 Moved Permanently',
			302	=> 'HTTP/1.1 302 Found',
			303	=> 'HTTP/1.1 303 See Other',
			401	=> 'HTTP/1.1 401 Unauthorized',
			403	=> 'HTTP/1.1 403 Forbidden',
			404	=> 'HTTP/1.1 404 Not Found',
    		503 => 'HTTP/1.1 503 Service Unavailable' };

		//Definindo o cabeçalho e redirecionando
		header( $cabecalho );
		header( "Location: {$pagina}" );

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
	 * Definindo o botão de benefício com substituições de placeholders
	 *
	 * @static
	 * @param int $valor Valor/ID do benefício ou status
	 * @param string $tipo Tipo de configuração: 'status_beneficio' ou customizado
	 * @param array $opcoes Array customizado com 'classe', 'texto', 'mensagem' (sobrescreve configuração padrão)
	 * @return string HTML do botão formatado
	 */
	public static function definirBtnBeneficio(int $valor, string $tipo = 'status_beneficio', ?string $dataBeneficio = null): string
	{

		// Estrutura base do HTML
		$html = '<a class="button [CLASSE]" href="">[TEXTO]</a>' .
				'<span>[MENSAGEM]</span>';

		// Configurações padrão por tipo e valor
		$configuracoes = match ($tipo) {
			'status_beneficio' => self::obterConfigBeneficio($valor, $dataBeneficio),
			default => ['classe' => '', 'texto' => '', 'mensagem' => '']
		};

		// Validar e escapar valores
		$substituicoes = [
			'[CLASSE]' => htmlspecialchars($configuracoes['classe'] ?? '', ENT_QUOTES, 'UTF-8'),
			'[TEXTO]' => htmlspecialchars($configuracoes['texto'] ?? '', ENT_QUOTES, 'UTF-8'),
			'[MENSAGEM]' => htmlspecialchars($configuracoes['mensagem'] ?? '', ENT_QUOTES, 'UTF-8')
		];

		// Substituir todos os placeholders de uma vez
		return strtr($html, $substituicoes);

	}

	/**
	 * Definindo o código do evento para o inscrito
	 * 
	 * @static
	 * @param int $length Quantidade de caracteres do código
	 * @return string Código gerado
	 */
	public static function definirCodigoEvento($length = 8)
    {

        $caracteres = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ';

        $codigo = '';

        $max = strlen($caracteres) - 1;

        for ($i = 0; $i < $length; $i++) {
            $codigo .= $caracteres[mt_rand(0, $max)];
        }

        return $codigo;

    }

	/**
	 * Normalizando horário para formato ISO HH:MM:SS
	 *
	 * @static
	 * @param string $horario Horário em qualquer formato (ex: "15h", "15:30", "15h30", "15")
	 * @return string Horário normalizado em formato HH:MM:SS
	 */
	public static function normalizarHorario(string $horario): string
	{
		if (empty(trim($horario))) {
			return '00:00:00';
		}

		$horarioRaw = trim($horario);
		$horaIso = '00:00:00';

		// Formato: "15h" ou "15h 30"
		if (preg_match('/^(\d{1,2})h(?:\s*(\d{2}))?$/', $horarioRaw, $hm)) {
			$hh = str_pad($hm[1], 2, '0', STR_PAD_LEFT);
			$mm = isset($hm[2]) && $hm[2] !== '' ? $hm[2] : '00';
			$horaIso = "{$hh}:{$mm}:00";
		}
		// Formato: "15:30"
		elseif (preg_match('/^(\d{1,2}):(\d{2})/', $horarioRaw, $hm)) {
			$hh = str_pad($hm[1], 2, '0', STR_PAD_LEFT);
			$mm = $hm[2];
			$horaIso = "{$hh}:{$mm}:00";
		}
		// Formato: apenas "15"
		elseif (preg_match('/^(\d{1,2})$/', $horarioRaw, $hm)) {
			$hh = str_pad($hm[1], 2, '0', STR_PAD_LEFT);
			$horaIso = "{$hh}:00:00";
		}

		return $horaIso;
	}

	/**
	 * Obtém referência de data/hora e texto de horário para uma inscrição.
	 * Retorna array com chaves: 'datetime' => ?DateTimeImmutable, 'dataStr' => string|null, 'horaTexto' => string
	 *
	 * @static
	 * @param object $inscricao Objeto de inscrição contendo evento e horario_evento
	 * @param array $agendaMap Mapa id->agenda para lookup
	 * @return array
	 */
	public static function obterReferenciaInscricao(object $inscricao, array $agendaMap = []): array
	{
		// Inicializando variáveis
		$horaTexto = '';
		$dateTime = null;

		// Determinando a data de referência inicial
		$dataReferenciaStr = $inscricao->evento->data_inicio ?? $inscricao->evento->data_publicacao ?? null;

		// Se houver horário de evento, tentar obter a data/hora completa
		if (!empty($inscricao->horario_evento)) {

			// Tentando encontrar o horário na agenda usando o mapa fornecido
			$agenda = $agendaMap[(int) $inscricao->horario_evento] ?? null;

			// Verificando
			if ($agenda) {

				// Obtendo o texto do horário para exibição
				$horaTexto = $agenda->horario ?? '';

				// Normalizando a data e hora para formato ISO
				if (!empty($agenda->dia)) {

					// YYYY-MM-DD ou DD/MM/YYYY 
					if (preg_match('/^(\d{2})\/(\d{2})\/(\d{4})$/', trim($agenda->dia), $m)) {
						$diaIso = "{$m[3]}-{$m[2]}-{$m[1]}";
					} else {
						$diaIso = (new DateTimeImmutable($agenda->dia))->format('Y-m-d');
					}

					// Normalizando o horário para formato ISO HH:MM:SS
					$horaIso 			= self::normalizarHorario($agenda->horario ?? '');
					$dataReferenciaStr  = trim($diaIso . ' ' . $horaIso);

				}

			}

		}

		// Criando o objeto DateTimeImmutable se a string de data/hora estiver disponível
		if (!empty($dataReferenciaStr)) {
			$dateTime = new DateTimeImmutable($dataReferenciaStr);
		}

		// Retornando
		return [
			'datetime' 	=> $dateTime,
			'dataStr' 	=> $dataReferenciaStr,
			'horaTexto' => $horaTexto
		];

	}

	/**
	 * Ordena inscrições pela data mais próxima de hoje.
	 * Prioriza itens com data válida, depois eventos futuros/hoje e por fim passados.
	 *
	 * @static
	 * @param array $inscricoes Lista de inscrições
	 * @param array $agendaMap Mapa id->agenda para lookup
	 * @return array Lista enriquecida com 'inscricao', 'ref' e metadados de ordenação
	 */
	public static function ordenarInscricoesPorProximidadeHoje(array $inscricoes, array $agendaMap = []): array
	{
		// Iniciando variáveis
		$inscricoesComReferencia = [];
		$todayTs 				 = (new DateTimeImmutable('today'))->getTimestamp();

		// Interando as inscrições para obter referências de data/hora
		foreach ($inscricoes as $inscricao) {

			// Verificando se é um objeto válido
			if (!is_object($inscricao)) {
				continue;
			}

			// Obtendo a referência de data/hora e texto do horário para a inscrição
			$ref 		= self::obterReferenciaInscricao($inscricao, $agendaMap);
			$dateTime 	= $ref['datetime'];

			// Fallback para ordenação quando não houver data de agenda/evento
			if (!$dateTime instanceof DateTimeImmutable) {
				$dataCriacao = $inscricao->data_criacao ?? null;
				if (!empty($dataCriacao)) {
					try {
						$dateTime = new DateTimeImmutable((string) $dataCriacao);
					} catch (Exception $e) {
						$dateTime = null;
					}
				}
			}

			// Obtendo o timestamp para ordenação
			$sortTs = $dateTime instanceof DateTimeImmutable ? $dateTime->getTimestamp() : null;

			// Inscrição com referências
			$inscricoesComReferencia[] = [
				'inscricao' => $inscricao,
				'ref' 		=> $ref,
				'sort_ts' 	=> $sortTs,
				'has_date' 	=> !is_null($sortTs),
				'is_past' 	=> !is_null($sortTs) ? ($sortTs < $todayTs) : true,
				'distance' 	=> !is_null($sortTs) ? abs($sortTs - $todayTs) : 9223372036854775807
			];

		}

		// Ordenando as inscrições
		usort($inscricoesComReferencia, function ($a, $b) {

			// Data Válida
			if ($a['has_date'] !== $b['has_date']) {
				return $a['has_date'] ? -1 : 1;
			}

			// Prioriza eventos de hoje/futuros antes dos passados
			if ($a['is_past'] !== $b['is_past']) {
				return $a['is_past'] ? 1 : -1;
			}

			// Mais próximo de hoje
			if ($a['distance'] !== $b['distance']) {
				return $a['distance'] <=> $b['distance'];
			}

			// Ordena os mais próximos de hoje primeiro
			if ($a['is_past']) {
				return ($b['sort_ts'] ?? 0) <=> ($a['sort_ts'] ?? 0);
			}

			// Ordenação padrão por data mais próxima de hoje
			return ($a['sort_ts'] ?? 0) <=> ($b['sort_ts'] ?? 0);

		});

		return $inscricoesComReferencia;

	}

	/**
	 * Obtendo configuração de benefício por status
	 *
	 * @static
	 * @param int $status Status do benefício
	 * @return array Array com 'classe', 'texto', 'mensagem'
	 */
	private static function obterConfigBeneficio(int $status, ?string $dataBeneficio): array
	{

		return match ((int) $status) {

			Status::ATIVO->value => [
				'classe' 	=> 'available',
				'texto' 	=> 'Disponível para retirada',
				'mensagem' 	=> 'Apresente essa tela na loja correspondente dentro do prazo informado ( até dia ' . ($dataBeneficio ?? '') . ' ) para resgatar o seu brinde'
			],

			Status::RETIRADO->value => [
				'classe' 	=> 'retired',
				'texto' 	=> 'Resgatado',
				'mensagem' 	=> 'Resgatado dia ' . ($dataBeneficio ?? '')
			],

			Status::INDISPONIVEL->value => [
				'classe' 	=> 'unavailable',
				'texto' 	=> 'Indisponível',
				'mensagem' 	=> 'Expirado dia ' . ($dataBeneficio ?? '')
			],

			default => [
				'classe' 	=> 'unknown',
				'texto' 	=> 'Desconhecido',
				'mensagem' 	=> 'Status desconhecido'
			]
		};

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
	public static function validarEDecodificarIdentificador( ?string $secureId, ?string $hashKey)
	{
		$hashLength 	= 64;
		$hash 			= substr($secureId, 0, $hashLength);
		$baseIdEncode 	= substr($secureId, $hashLength);
		$id 			= base64_decode($baseIdEncode);
		$validHash 		= hash_hmac('sha256', $id, $hashKey);

		if (hash_equals($validHash, $hash)) {
			return $id;
		} else {
			throw new Exception('Identificador inválido!');
		}

	}

}
