<?php
//Definindo o namespace
namespace App\Infraestrutura;

//Definindo as classes usadas
use App\Infraestrutura\Configuracao;
use App\Util\Util;
use Exception;

/**
 * Template Management for PHP5
 *
 * The Template engine allows to keep the HTML code in some external files
 * which are completely free of PHP code. This way, it's possible keep logical
 * programmin (PHP code) away from visual structure (HTML or XML, CSS, etc).
 *
 * If you are familiar with PHP template concept, this class includes these
 * features: object support, auto-detect blocks, auto-clean children blocks,
 * warning when user call for a non-existent block, warning when a mal-formed
 * block is detected, warning when user sets a non existant variable, and other
 * 	or features.
 *
 * @author Rael G.C. (rael.gc@gmail.com)
 * @version 2.2.1
 */
class Template extends \raelgc\view\Template{

	/**
	 * A hash of existent object properties variables in the document.
	 * @var	array
	 */
	protected $properties = array();

	/**
	 * Regular expression to find var and block names.
	 * Only alfa-numeric chars and the underscore char are allowed.
	 *
	 * @var		string
	 */
	protected static $REG_NAME = "([[:alnum:]]|_)+";

	/**
	 * Minificando o HTML ou não
	 *
	 * @access private
	 * @var mixed
	 */
	private bool $min	= false;

	/**
	 * Campos para não tradução
	 *
	 * @access private
	 * @var array
	 */
	private const CAMPOS	= [
		'texto',
		'descricao',
		'valor' ];

	/**
	 * Creates a new template, using $filename as main file.
	 *
	 * When the parameter $accurate is true, blocks will be replaced perfectly
	 * (in the parse time), e.g., removing all \t (tab) characters, making the
	 * final document an accurate version. This will impact (a lot) the
	 * performance. Usefull for files using the &lt;pre&gt; or &lt;code&gt; tags.
	 *
	 * @param     string $filename		file path of the file to be loaded
	 * @param     booelan $accurate		true for accurate block parsing
	 * @uses App\Infraestrutura\Configuracao::get() Retornando a variável do projeto
	 */
	public function __construct(
		mixed $pagina,
		protected $accurate = false,
		$padrao 			= true){

		//Verificando se o projeto está em produção ou não
		$this->min	= Configuracao::get( 'projeto.producao' );

		//Verificando se é arquivo padrão
		if( $padrao ){

			//Definindo a ação
			$acao	= !is_null( $pagina->acao ) ? "{$pagina->acao}.html" : "index.html";

			//Carregando o arquivo
			$this->loadfile( '.', Configuracao::get( 'dir.html' ) . "/{$pagina->diretorio}/{$acao}" );

		}else
			//Carregando o arquivo padrão
			$this->loadfile( '.', $pagina );

	}

	/**
	 * Loads a file identified by $filename.
	 *
	 * The file will be loaded and the file's contents will be assigned as the
	 * variable's value.
	 * Additionally, this method call Template::identify() that identifies
	 * all blocks and variables automatically.
	 *
	 * @access protected
	 * @param     string $varname		contains the name of a variable to load
	 * @param     string $filename		file name to be loaded
	 *
	 * @return    void
	 */
	protected function loadfile( $varname, $filename ): void{

	    $ext		= pathinfo( $filename, PATHINFO_EXTENSION );
		$filename	= ( $this->min && $ext == 'html' && !str_contains( $filename, '.min' ) ) ? substr_replace( $filename, 'min.html', -4 ) : $filename;
		if( !file_exists( $filename ) )
			throw new Exception( "O arquivo({$filename}) não existe" );

		//If it's PHP file, parse it
		if( $this->isPHP( $filename ) ){

			ob_start();
			require_once $filename;
			$str	= ob_get_contents();
			ob_end_clean();
			$this->setValue( $varname, $str );

		}else{

			//Reading file and hiding comments
			$str	= preg_replace( '/<!---.*?--->/smi', '', file_get_contents( $filename ) );
			if( empty( $str ) )
				throw new Exception("O arquivo({$filename}) está vazio");
			$this->setValue( $varname, $str );
			$blocks	= $this->identify( $str, $varname );
			$this->createBlocks( $blocks );

		}

	}

	/**
	 * Identifies all variables defined in the document.
	 *
	 * @access protected
	 * @param     string $content	file content
	 */
	protected function identifyVars( &$content ): void{

		$r = preg_match_all( '/{(' . self::$REG_NAME . ')((\-\>(' . self::$REG_NAME . '))*)?((\|.*?)*)?}/', $content, $m );
		if( $r ){
			for( $i=0; $i<$r; $i++ ){

				//Object var detected
				if( $m[ 3 ][ $i ] && ( !isset( $this->properties[ $m[ 1 ][ $i ] ] ) || !in_array( $m[ 3 ][ $i ], $this->properties[ $m[ 1 ][ $i ] ] ) ) )
					$this->properties[ $m[ 1 ][ $i ] ][]	= $m[ 3 ][ $i ];

				//Modifiers detected
				if( $m[ 7 ][ $i ] && ( !isset( $this->modifiers[ $m[ 1 ][ $i ] ] ) || !in_array( $m[ 7 ][ $i ], $this->modifiers[ $m[ 1 ][ $i ] . $m[ 3 ][ $i ] ] ) ) )
					$this->modifiers[ $m[ 1 ][ $i ] . $m[ 3 ][ $i ] ][]	= $m[ 1 ][ $i ] . $m[ 3 ][ $i ] . $m[ 7 ][ $i ];

				//Common variables
				if( !in_array( $m[ 1 ][ $i ], $this->vars ) )
					$this->vars[]	= $m[ 1 ][ $i ];

			}
		}
	}
	/**
	 * Fill in all the variables contained in variable named $value.
	 * $value. The resulting string is not "cleaned" yet.
	 *
	 * @param  string 	$value		var value
	 * @return string	content with all variables substituted.
	 */
	protected function subst( $value ): string{

		//Common variables replacement
		$s	= str_replace( array_keys( $this->values ), $this->values, $value );

		//Common variables with modifiers
		foreach( $this->modifiers as $var => $expressions )
			if( false !== strpos( $s, '{' . $var . '|' ) ) foreach( $expressions as $exp )
				if( false === strpos( $var, '->' ) && isset( $this->values[ '{' . $var . '}' ] ) )
					$s	= str_replace( '{' . $exp . '}', $this->substModifiers( $this->values[ '{' . $var . '}' ], $exp ), $s );

		//Object variables replacement
		foreach($this->instances as $var => $instance){

			foreach( $this->properties[ $var ] as $properties ){

				if( false !== strpos( $s, '{' . $var . $properties . '}' ) || false !== strpos( $s, '{' . $var . $properties . '|' ) ){

					$pointer 	= $instance;
					$property	= explode( '->', $properties );

					for( $i = 1; $i < sizeof( $property ); $i++ ){

						if( !is_null( $pointer ) ){

							$obj	= strtolower( $property[ $i ] );

							if( !empty(array_filter( self::CAMPOS, fn( $palavra ) => str_contains( $obj, $palavra ) ) ) )
								$obj	.= Util::definirOpcaoLingua()['sufixo'];

							//Get accessor
							if( method_exists( $pointer, "get{$obj}" ) )
								$pointer	= $pointer->{ "get{$obj}" }();
							//Magic __get accessor
							else if( method_exists( $pointer, '__get' ) )
								$pointer	= $pointer->__get( $property[ $i ] );

							//Property acessor
							else if( property_exists( $pointer, $obj ) )
								$pointer	= $pointer->$obj;

							else{

								$className	= $property[ $i - 1 ] ? $property[ $i - 1 ] : get_class( $instance );
								$class 		= is_null( $pointer ) ? "NULL" : get_class( $pointer );
								throw new Exception( "Nenhum método de acesso na classe({$class}) para {$className}->{$property[ $i ]}" );

							}
						}

					}

					//hecking if final value is an object...
					if( is_object( $pointer ) )
						$pointer	= method_exists( $pointer, '__toString' ) ? $pointer->__toString() : 'Object';
					//... or an array
					else if( is_array( $pointer ) ){
						$value = '';
						for( $i = 0; list( $key, $val ) = each( $pointer ); $i++){

							$value	.= "{$key} => {$val}";

							if( $i < sizeof( $pointer ) -1 )
								$value	.= ',';

						}

						$pointer = $value;

					}

					//Replacing value
					$s	= str_replace( "{" . $var . $properties . "}", $pointer ?? '', $s );

					//Object with modifiers
					if( isset( $this->modifiers[ $var . $properties ] ) )
						foreach( $this->modifiers[ $var . $properties ] as $exp )
							$s	= str_replace( '{' . $exp . '}', $this->substModifiers( $pointer, $exp ), $s );

				}

			}

		}

		//Verificando e definindo se o conteúdo possui um domínio
		$s	= preg_replace_callback( '/\{\{(.*?)\}\}/', function( $matches ){

			//Definindo o array para separar o domínio e conteúdo
			$array	= explode( '::', $matches[ 1 ], 2 );

			//Verificando a existência de domínio e conteúdo
			if( count( $array ) === 2 ){

				//Definindo o domínio e conteudo
				[ $dominio, $texto ]	= $array;

				//Retornando o conteúdo traduzido
				return dgettext( $dominio, $texto );

			}else
				//Lançando a exceção
				throw new \Exception( 'Domínio/tradução não fornecida.' );

		}, $s );

		//Retornando o conteúdo
		return $s;

	}

	/**
	 * Shortcut to iterate over a block, assign values, then parse the related block.
	 *
	 * @param     string $block		the block name to be parsed
	 * @param     string $varname	constains a varname
	 * @param     iterable $values	any collection of values
	 * @param     boolean $append	true if the content must be appended (default)
	 * @return    void
	 */
	public function mapBlock( $block, $varname, $values, $append = true ): void{

		//Listando os valores
		foreach( $values as $v ){

			//Definindo a variável
			$this->$varname	= $v;

			//Exibindo o bloco
			$this->block( $block, $append );

		}

	}

	/**
	 * Exibindo o conteúdo
	 *
	 * @uses App\Infraestrutura\Configuracao::get() Retornando a variável do projeto
	 * @return void
	 */
	public function show(): void{

		//Verificando se é para exibir o debug ou não
		if( !Configuracao::get( 'debug.exibir' ) )
			//Exibindo o conteúdo
			echo $this->parse();

	}
}