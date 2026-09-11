<?php
//Definindo o namespace
namespace App\Util;

//Definindo as classes usadas
use App\Infraestrutura\Enum\Mensagem\Validacao as EnumValidacao;
use App\Infraestrutura\Enum\Mensagem\Arquivo as EnumArquivo;
use App\Excecao\Conteudo as ConteudoException;
use App\Infraestrutura\Enum\Mensagem\Conteudo;
use App\Infraestrutura\Enum\Status\Http;
use App\Infraestrutura\Enum\Tipo\Campo;
use App\Util\Util;
use DateTime;

/**
 * Classe de validação
 *
 * @final
 * @author Caroline Tacats <caroline.tacats62@gmail.com>
 * @package App\Util
 */
final class Validacao{

	/**
	 * Valor
	 *
	 * @access private
	 * @var mixed Valor
	 */
	private static $valor;

	/**
	 * Opções do campo
	 *
	 * @access private
	 * @var array Opções do campo
	 */
	private static $opcoes;

	/**
	 * Tipo do campo
	 *
	 * @access private
	 * @static
	 * @var string Tipo
	 */
	private static string $tipo;

	/**
	 * Tamanho do campo
	 *
	 * @access private
	 * @static
	 * @var int Tamanho
	 */
	private static int $tamanho;

	/**
	 * Validar ou não o campo
	 *
	 * @access private
	 * @static
	 * @var bool Validar ou não o campo
	 */
	private static bool $validar;

	/**
	 * Validando os campos
	 *
	 * @static
	 * @param mixed $valor Valor
	 * @param array $opcoes Opções dos campos
	 */
	public static function validar( mixed $valor, array $opcoes ){

		//Definindo
		self::$valor	= $valor;
		self::$opcoes	= $opcoes;
		self::$tipo		= $opcoes[ 'tipo' ]->value ?? Campo::STRING->value;
		self::$tamanho	= $opcoes[ 'tamanho' ] ?? 255;
		self::$validar	= $opcoes[ 'validar' ] ?? false;

		//Validando
		self::tamanho();
		self::obrigatorio();
		self::email();
		self::numerico();
		self::coordenada();
		self::url();
		self::data();
		self::documento();

	}

	/**
	 * Validando o tamanho do campo
	 *
	 * @static
	 * @throws ConteudoException
	 * @return void
	 */
	public static function tamanho(): void{

		//Verificando o tamanho do campo
		if( ( strlen( self::$valor ?? '' ) > self::$tamanho ) && self::$tipo != Campo::TEXTO->value && self::$tipo != Campo::JSON->value )
			//Lançando a exceção
			throw new ConteudoException( sprintf( EnumValidacao::TAMANHO_INVALIDO->texto(), self::$opcoes[ 'nome' ], self::$tamanho ), Http::BAD_REQUEST->codigo() );

	}

	/**
	 * Validando os campos obrigatórios
	 *
	 * @static
	 * @throws ConteudoException
	 * @return void
	 */
	public static function obrigatorio(): void{

		//Verificando se o campo é obrigatório
		if( !isset( self::$opcoes[ 'obrigatorio' ] ) && ( is_null( self::$valor ) || self::$valor == '' ) )
			//Lançando a exceção
			throw new ConteudoException( sprintf( EnumValidacao::VAZIO->texto(), self::$opcoes[ 'nome' ] ), Http::BAD_REQUEST->codigo() );

	}

	/**
	 * Validando os campos de email
	 *
	 * @static
	 * @throws ConteudoException
	 * @return void
	 */
	public static function email(): void{

		//Verificando o campo do tipo email
		if( ( self::$valor !== '' || !empty( self::$valor ) ) && self::$tipo == Campo::EMAIL->value && !filter_var( self::$valor, FILTER_VALIDATE_EMAIL ) )
			//Lançando a exceção
			throw new ConteudoException( sprintf( EnumValidacao::EMAIL_INVALIDO->texto(), self::$valor ), Http::BAD_REQUEST->codigo() );

	}

	/**
	 * Validando os campos numéricos
	 *
	 * @static
	 * @throws ConteudoException
	 * @return void
	 */
	public static function numerico(): void{

		//Verificando o campo do tipo numérico
		if( !is_numeric( self::$valor ) && self::$valor != '' && self::$tipo == Campo::INTEIRO->value )
			//Lançando a exceção
			throw new ConteudoException( sprintf( EnumValidacao::NAO_INTEIRO->texto(), self::$opcoes[ 'nome' ] ), Http::BAD_REQUEST->codigo() );

	}

	/**
	 * Validando as coordenadas
	 *
	 * @static
	 * @throws ConteudoException
	 * @return void
	 */
	public static function coordenada(): void{

		//Verificando o campo do tipo latitude
		if( ( self::$valor !== '' || !empty( self::$valor ) ) && self::$tipo == Campo::LATITUDE->value && self::$validar && !preg_match( '/^[-]?(([0-8]?[0-9])\.(\d+))|(90(\.0+)?)$/', self::$valor ) )
			//Lançando a exceção
			throw new ConteudoException( sprintf( EnumValidacao::LATITUDE_INVALIDA->texto(), self::$valor ), Http::BAD_REQUEST->codigo() );

		//Verificando o campo do tipo longitude
		if( ( self::$valor !== '' || !empty( self::$valor ) ) && self::$tipo == Campo::LONGITUDE->value && self::$validar && !preg_match( '/^[-]?((((1[0-7][0-9])|([0-9]?[0-9]))\.(\d+))|180(\.0+)?)$/', self::$valor ) )
			//Lançando a exceção
			throw new ConteudoException( sprintf( EnumValidacao::LONGITUDE_INVALIDA->texto(), self::$valor ), Http::BAD_REQUEST->codigo() );

	}

	/**
	 * Validando os campos de URL
	 *
	 * @static
	 * @throws ConteudoException
	 * @return void
	 */
	public static function url(): void{

		//Verificando o campo do tipo URL
		if( ( self::$valor !== '' && !empty( self::$valor ) && !is_null( self::$valor ) ) && ( self::$tipo == Campo::URL->value ) && filter_var( self::$valor, FILTER_VALIDATE_URL ) === FALSE )
			//Lançando a exceção
			throw new ConteudoException( sprintf( EnumValidacao::URL_INVALIDA->texto(), self::$valor ), Http::BAD_REQUEST->codigo() );

	}

	/**
	 * Validando as datas
	 *
	 * @static
	 * @return void
	 */
	public static function data(): void{

		//Verificando o campo do tipo data
		if( ( self::$valor !== '' || !empty( self::$valor ) ) && self::$tipo == Campo::DATA->value )
			//Validando a data
			self::validarData( [ self::$valor ] );

	}

	/**
	 * Validando o documento
	 *
	 * @static
	 * @return void
	 */
	public static function documento(): void{

		//Verificando o campo do tipo documento(CNPJ)
		if( ( self::$valor !== '' && !is_null( self::$valor ) && !empty( self::$valor ) ) && self::$tipo == Campo::CNPJ->value && self::$validar )
			//Validando
			self::validarCNPJ( self::$valor );
		//Verificando o campo do tipo documento(CPF)
		else if( ( self::$valor !== '' && !is_null( self::$valor ) && !empty( self::$valor ) ) && self::$tipo == Campo::CPF->value && self::$validar )
			//Validando
			self::validarCPF( self::$valor );

	}

	/**
	 * Validando os campos vazios
	 *
	 * @static
	 * @param array $campos Campos
	 * @throws ConteudoException
	 * @return void
	 */
	public static function validarCampoVazio( array $campos ): void{

		//Listando os campos
		foreach( $campos as $descricao => $valor )
			//Verificando se o campo não está vazio
			if( is_null( $valor ) || $valor == '' )
				//Lançando a exceção
				throw new ConteudoException( sprintf( EnumValidacao::VAZIO->texto(), $descricao ), Http::BAD_REQUEST->codigo() );

	}

	/**
	 * Validando os campos numéricos
	 *
	 * @static
	 * @param array $campos Campos do array
	 * @throws ConteudoException
	 * @return void
	 */
	public static function validarCampoNumerico( array $campos ): void{

		//Listando os campos
		foreach( $campos as $descricao => $valor )
			//Verificando se o campos não é numérico
			if( is_null( $valor ) || !is_numeric( $valor ) )
				//Lançando a exceção
				throw new ConteudoException( sprintf( EnumValidacao::NAO_INTEIRO->texto(), $descricao ), Http::BAD_REQUEST->codigo() );

	}

	/**
	 * Validando a data
	 *
	 * @static
	 * @param array $datas Datas
	 * @uses App\Util\Util::formatarData() Formatando a data
	 * @uses DateTime::createFromFormat() Criando uma data a partir de um formato
	 * @uses DateTime::getLastErrors() Pegando os erros
	 * @throws ConteudoException
	 * @return void
	 */
	public static function validarData( array $datas ): void{

		//Listando as datas
		foreach( $datas as $data ){

			//Verificando se a data não está nula
			if( !is_null( $data ) ){

				//Definindo a data formatada
				DateTime::createFromFormat( 'd/m/Y', Util::formatarData( $data, 'pt-sem-hora' ) );

				//Definindo os erros
				$erro	= DateTime::getLastErrors();

				//Verificando a existência de erro
				if ( !empty( $erro[ 'warning_count' ] ) || count( $erro[ 'errors' ] ?? [] ) > 0 )
					//Lançando a exceção
					throw new ConteudoException( sprintf( EnumValidacao::DATA_INVALIDA->texto(), $data ), Http::BAD_REQUEST->codigo() );

			}

		}

	}

	/**
	 * Validando se é um array
	 *
	 * @static
	 * @param null|array $campo Campo
	 * @param string $nome Nome do campo
	 * @throws ConteudoException
	 * @return void
	 */
	public static function validarArray( ?array $campo, string $nome ): void{

		//Verificando se o campo passado é um array
		if( !is_array( $campo ) || count( $campo ) == 0 )
			//Lançando a exceção
			throw new ConteudoException( sprintf( EnumValidacao::ARRAY_INVALIDO->texto(), $nome ), Http::BAD_REQUEST->codigo() );

	}

	/**
	 * Validando o email
	 *
	 * @static
	 * @param string $email E-mail
	 * @throws ConteudoException
	 * @return void
	 */
	public static function validarEmail( string $email ): void{

		//Verificando se o campo passado é um email
		if( !filter_var( $email, FILTER_VALIDATE_EMAIL ) )
			//Lançando a exceção
			throw new ConteudoException( sprintf( EnumValidacao::EMAIL_INVALIDO->texto(), $email ), Http::BAD_REQUEST->codigo() );

	}

	/**
	 * Validando o CPF
	 *
	 * @static
	 * @param string $cpf CPF
	 * @throws ConteudoException
	 * @return void
	 */
	public static function validarCPF( string $cpf, bool $excecao = true ): bool{

		//Formatando o documento
		$cpf	= preg_replace( '/\D/', '', $cpf );

		//Verificando o tamanho
		if( strlen( $cpf ) != 11 || preg_match( '/(\d)\1{10}/', $cpf ) )
			//Verificando se é para lançar a exceção
			if( $excecao )
				//Lançando a exceção
				throw new ConteudoException( sprintf( EnumValidacao::CPF->texto(), $cpf ), Http::BAD_REQUEST->codigo() );
			else
				//Retornando
				return false;

		//Definindo os digitos
		$digito	= str_split( $cpf );

		//Definindo algumas variáveis
		$soma	= 0;

		//Listando
		for( $i = 0; $i < 9; $i++ )
			//Somando
			$soma	+= $digito[ $i ] * ( 10 - $i );

		//Definindo algumas variáveis
		$resto			= $soma % 11;
		$primeiroDigito	= ( $resto < 2 ) ? 0 : ( 11 - $resto );

		//Verificando o dígito
		if( $digito[ 9 ] != $primeiroDigito )
			//Verificando se é para lançar a exceção
			if( $excecao )
				//Lançando a exceção
				throw new ConteudoException( sprintf( EnumValidacao::CPF->texto(), $cpf ), Http::BAD_REQUEST->codigo() );
			else
				//Retornando
				return false;

		//Definindo algumas variáveis
		$soma	= 0;

		//Listando
		for( $i = 0; $i < 10; $i++ )
			//Somando
			$soma	+= $digito[ $i ] * ( 11 - $i );

		//Definindo algumas variáveis
		$resto 			= $soma % 11;
		$segundoDigito	= ( $resto < 2 ) ? 0 : ( 11 - $resto );

		//Verificando o dígito
		if( $digito[ 10 ] != $segundoDigito )
			//Verificando se é para lançar a exceção
			if( $excecao )
				//Lançando a exceção
				throw new ConteudoException( sprintf( EnumValidacao::CPF->texto(), $cpf ), Http::BAD_REQUEST->codigo() );
			else
				//Retornando
				return false;

		//Retornando
		return true;

	}

	/**
	 * Validando o CNPJ
	 *
	 * @static
	 * @param string $cnpj CNPJ
	 * @param bool $excecao Exibindo ou não a exceção
	 * @throws ConteudoException
	 * @return void
	 */
	public static function validarCNPJ( string $cnpj, bool $excecao = true ): bool{

		//Formatando
		$cnpj	= preg_replace( '/[^0-9]/', '', $cnpj );

		//Verificando a quantidade de caracteres
		if( strlen( $cnpj ) != 14 )
			//Verificando se é para lançar a exceção
			if( $excecao )
				//Lançando a exceção
				throw new ConteudoException( sprintf( EnumValidacao::CNPJ->texto(), $cnpj ), Http::BAD_REQUEST->codigo() );
			else
				//Retornando
				return false;

		//Definindo algumas variáveis
		$soma	= 0;
		$peso	= 5;

		//Listando
		for( $i = 0; $i < 12; $i++ ) {

			//Somando
			$soma	+= $cnpj[ $i ] * $peso;

			//Definindo o peso
			$peso	= ( $peso == 2 ) ? 9 : $peso - 1;

		}

		//Definindo algumas variáveis
		$resto 			= $soma % 11;
		$primeiroDigito	= ( $resto < 2 ) ? 0 : 11 - $resto;

		//Verificando o primeiro dígito
		if( $cnpj[ 12 ] != $primeiroDigito )
			//Verificando se é para lançar a exceção
			if( $excecao )
				//Lançando a exceção
				throw new ConteudoException( sprintf( EnumValidacao::CNPJ->texto(), $cnpj ), Http::BAD_REQUEST->codigo() );
			else
				//Retornando
				return false;

		//Definindo algumas variáveis
		$soma	= 0;
		$peso	= 6;

		//Listando
		for( $i = 0; $i < 13; $i++ ){

			//Somando
			$soma	+= $cnpj[ $i ] * $peso;

			//Definindo o peso
			$peso	= ( $peso == 2 ) ? 9 : $peso - 1;
		}

		//Definindo algumas variáveis
		$resto 			= $soma % 11;
		$segundoDigito	= ( $resto < 2 ) ? 0 : 11 - $resto;

		//Verificando o segundo dígito
		if( $cnpj[ 13 ] != $segundoDigito )
			//Verificando se é para lançar a exceção
			if( $excecao )
				//Lançando a exceção
				throw new ConteudoException( sprintf( EnumValidacao::CNPJ->texto(), $cnpj ), Http::BAD_REQUEST->codigo() );
			else
				//Retornando
				return false;

		//Retornando
		return true;

	}

	/**
	 * Validando o conteúdo da senha
	 *
	 * @static
	 * @param string $senha Senha
	 * @throws ConteudoException
	 * @return void
	 */
	public static function validarFormatoSenha( string $senha ): void{

		//Verifica se a senha tem no mínimo 8 caracteres
		if( strlen( $senha ) < 8 )
			//Lançando a exceção
			throw new ConteudoException( EnumValidacao::SENHA_MINIMO_CARACTER->texto() );

		//Verifica se a senha contém pelo menos um número
		if( !preg_match( '/[0-9]/', $senha ) )
			//Lançando a exceção
			throw new ConteudoException( EnumValidacao::SENHA_SEM_NUMERICO->texto() );

		//Verifica se a senha contém pelo menos um caractere especial
		if( !preg_match( '/[\W_]/', $senha ) )
			//Lançando a exceção
			throw new ConteudoException( EnumValidacao::SENHA_SEM_CARACTERE_ESPECIAL->texto() );

		if( !preg_match( '/[a-zA-Z]/', $senha ) )
			//Lançando a exceção
			throw new ConteudoException( EnumValidacao::SENHA_SEM_LETRA->texto() );

	}

	/**
	 * Validando a senha
	 *
	 * @access public
	 * @static
	 * @param string $senhaInformada Senha informada
	 * @param string $senhaSalva Senha salva
	 * @throws ConteudoException
	 * @return void
	 */
	public static function validarSenha(string $senhaInformada, string $senhaSalva): void
	{

		// Verificando a senha
		$senhaValida = password_verify($senhaInformada, $senhaSalva);

		// Verificação de compatibilidade com senhas salvas em MD5
		if (!$senhaValida && $senhaSalva !== '')
			$senhaValida = hash_equals($senhaSalva, md5($senhaInformada));

		// Bloqueio imediato por senha inválida
		if (!$senhaValida)
			throw new ConteudoException(Conteudo::SENHA_INCORRETA->texto(), Http::BAD_REQUEST->codigo());


	}

	/*
	 * VERIFICAÇÕES
	 */

	/**
	 * Verificando a imagem
	 *
	 * @static
	 * @param array $arquivo Arquivo do upload
	 * @param array $configuracao Configurações
	 * @uses App\Util\Util::formatarNomeArquivo() Formatando o nome do arquivo
	 * @return array
	 * @throws ConteudoException
	 */
	public static function verificarImagem( array $arquivo, array $configuracao = [] ): array{

		//Configurações padrões
		$padrao	= [
			'tamanho_maximo'		=> 1024 * 1024,
			'tipo_suportado'  		=> [ 'image/jpeg', 'image/png' ],
			'extensao_suportada'	=> [ 'jpg', 'jpeg', 'png' ],
			'largura_minima'      	=> 170,
			'altura_minima'     	=> 170,
			'largura_maxima'      	=> 800,
			'altura_maxima'     	=> 800 ];

		//Unindo as configurações
		$configuracao	= array_merge( $padrao, $configuracao );

		//Verificando se houve erro no upload
		if( $arquivo[ 'error' ] !== UPLOAD_ERR_OK )
			//Lançando a exceção
			throw new ConteudoException( sprintf( EnumArquivo::PADRAO->texto(), $configuracao[ 'error' ] ), Http::BAD_REQUEST->codigo() );

		//Verificando o tamanho da imagem
		if( $arquivo[ 'size' ] > $configuracao[ 'tamanho_maximo' ] )
			//Lançando a exceção
			throw new ConteudoException( sprintf( EnumArquivo::TAMANHO_INVALIDO->texto(), "{$configuracao[ 'tamanho_maximo' ]}kb" ), Http::BAD_REQUEST->codigo() );

		//Definindo o arquivo temporário
		$temporario	= $arquivo[ 'tmp_name' ];

		//Verificando o mime type do arquivo
		$informacao	= finfo_open( FILEINFO_MIME_TYPE );

		//Definindo o mime type o arquivo
		$mime		= finfo_file( $informacao, $temporario );

		//Fechando o arquivo
		finfo_close( $informacao );

		//Verificando o mime se está válido
		if( !in_array( $mime, $configuracao[ 'tipo_suportado' ] ) )
			//Lançando a exceção
			throw new ConteudoException( sprintf( EnumArquivo::TIPO_INVALIDO->texto(), implode( ', ', $configuracao[ 'tipo_suportado' ] ) ), Http::BAD_REQUEST->codigo() );

			//Definindo a extensão
		$extensao	= strtolower( pathinfo( $arquivo[ 'name' ], PATHINFO_EXTENSION ) );

		//Verificando a extensão
		if( !in_array( $extensao, $configuracao[ 'extensao_suportada' ] ) )
			//Lançando a exceção
			throw new ConteudoException( sprintf( EnumArquivo::EXTENSAO_INVALIDA->texto(), implode( ', ', $configuracao[ 'extensao_suportada' ] ) ), Http::BAD_REQUEST->codigo() );

		//Definindo as dimensões do arquivo
		$dimensao	= getimagesize( $temporario );

		//Verificando a dimensão
		if( $dimensao === false )
			//Lançando a exceção
			throw new ConteudoException( EnumArquivo::DIMENSAO->texto(), Http::BAD_REQUEST->codigo() );

		//Definindo as dimensões
		[ $largura, $altura ]	= $dimensao;

		//Verificando as dimensões mínimas
		if( $largura < $configuracao[ 'largura_minima' ] || $altura < $configuracao[ 'altura_minima' ] )
			//Lançando a exceção
			throw new ConteudoException( EnumArquivo::DIMENSAO_MINIMA_INVALIDA->texto(), Http::BAD_REQUEST->codigo() );

		//Verificando as dimensões máximas
		if( $largura > $configuracao[ 'largura_maxima' ] || $altura > $configuracao[ 'altura_maxima' ] )
			//Lançando a exceção
			throw new ConteudoException( EnumArquivo::DIMENSAO_MAXIMA_INVALIDA->texto(), Http::BAD_REQUEST->codigo() );

		//Retornando
		return [
			'nome'			=> Util::formatarNomeArquivo( $arquivo[ 'name' ] ),
			'mime'   		=> $mime,
			'extensao'  	=> $extensao,
			'largura'  		=> $largura,
			'altura' 		=> $altura,
			'tamanho'		=> $arquivo[ 'size' ],
			'temporario'	=> $temporario ];
	}

}