<?php
//Definindo o namespace
namespace App\Modelo;

//Definindo as classes usadas
use App\Infraestrutura\Enum\Mensagem\Conteudo;
use App\Excecao\Conteudo as ConteudoException;
use App\Infraestrutura\Enum\Status\Http;
use App\Infraestrutura\Enum\Tipo\Campo;
use App\Infraestrutura\Fabrica;
use App\Infraestrutura\Base;
use App\Util\Validacao;
use JsonSerializable;
use App\Util\Util;

/**
* Classe dos modelos
* Classes básicas são herdadas por ela
* Herdada da classe base para auxílio nas chamadas
*
* @abstract
* @author Caroline Tacats <caroline.tacats62@gmail.com>
* @package App\Modelo
*/
abstract class Modelo extends Base implements JsonSerializable{

	/**
	 * Quantidade de campos
	 *
	 * @var int Quantidade de campos
	 */
	protected int $quantidadeCampos;

	/**
	 * Campos de forma separada
	 *
	 * @var string Campos separados
	 */
	public string $camposSeparados, $camposSeparadosParaInsercao;

	/**
	 * Campos para inserção
	 *
	 * @var array Campos para inserção
	 */
	public array $camposParaInsercao, $campos, $tipoRepositorio;

	/**
	 * Tipos das variáveis para receber o valor null
	 *
	 * @var array Array com os tipos das variáveis
	 */
	private const TIPOS_NULO	= [

		Campo::DATA_CUSTOMIZADA->value,
		Campo::CPF->value,
		Campo::LONGITUDE->value,
		Campo::LATITUDE->value,
		Campo::INTEIRO->value,
		Campo::STRING->value,
		CAMPO::TEXTO->value,
		CAMPO::URL->value ];

	/**
	 * Método construtor da classe
	 *
	 * @param array $array Array com os campos
	 * @param boolean $formatar Formatar os campos ou não
	 * @param boolean $remover Remover ou não os campos
	 */
	public function __construct( array $array = [], bool $formatar = false, bool $remover = false ){

		//Definindo o tipo de conexão com o repositório
		$this->tipoRepositorio	= $this->configuracao[ 'tipo' ] ?? [ 'BDR' ];

		//Verificando
		if( !empty( $array ) ){

			//Listando
			foreach( $array as $campo => $valor )
				//Verificando
				if( array_key_exists( $campo, $this->atributos ) ){

					//Definindo o atributo
					$atributo		= $this->atributos[ $campo ][ 'tipo' ]->value ?? Campo::STRING->value;

					//Definindo o campo caso esteja "vazio"
					$this->$campo	= ( $valor == '' && in_array( $atributo, self::TIPOS_NULO ) ) ? null : $valor;
					$campos[]		= $campo;

				}

			//Verificando a levantando a exceção caso os campos não sejam passados
			$campos ?? throw new ConteudoException( Conteudo::MODELO_SEM_CAMPOS->texto(), Http::BAD_REQUEST->value );

			//Definindo as configurações
			$this->configuracao( $campos );

			//Validando
			$this->validar( $campos, $formatar );

			//Definindo os relacionamentos
			$this->relacionamento();

			//Verificando se é para remover os campos desnecessários
			if( $remover )
				//Removendo
				unset( $this->atributos, $this->configuracao );

		}

	}

	/**
	 * Definindo as configurações
	 *
	 * @param array $campos Campos
	 * @return void
	 */
	public function configuracao( array $campos ): void{

		//Verificando se o modelo passado é do tipo BDR
		if( in_array( 'BDR', $this->tipoRepositorio ) ){

			//Definindo os campos e a quantidade
			$this->campos						= $campos;
			$this->quantidadeCampos				= count( $campos );
			$this->camposSeparados				= implode( ',', $campos );
			$this->camposSeparadosParaInsercao	= implode( ',', array_map( fn( $campo ) => ":{$campo}", $campos ) );

		}

	}

	/**
	 * Definindo os relacionamentos
	 *
	 * @access private
	 * @return void
	 */
	private function relacionamento(): void{

		//Verificando o tipo
		if( in_array( 'BDR', $this->tipoRepositorio ) && isset( $this->configuracao[ 'relacionamento' ] ) )
			//Listando os relacionamentos
			foreach( $this->configuracao[ 'relacionamento' ] as $relacionamento )
				//Definindo o controlador
				$this->{"controlador{$relacionamento[ 'campo' ]}"} = ( new Fabrica( $relacionamento[ 'classe' ], $relacionamento[ 'tipo' ] ?? 'BDR' ) )->controlador;

	}

	/**
	 * Validando os campos
	 *
	 * @access private
	 * @param array $opcoes Opções
	 * @uses App\Util\Validacao\Validacao::validar() Validando os campos
	 * @uses App\Util\Util::formatarData() Formatando a data
	 * @uses App\Util\Util::formatarCaracteres() Formatando os caracteres
	 * @return void
	 */
	private function validar( array $campos, bool $formatar ): void{

		//Listando
		foreach( $this->atributos as $campo => $opcoes ){

			//Definindo o tipo
			$tipo	= $opcoes[ 'tipo' ]->value ?? Campo::STRING->value;

			//Verificando
			if( in_array( $campo, $campos ) )
				//Validando
				Validacao::validar( $this->$campo, $opcoes );

			//Verificando
			if( $tipo == Campo::DATA_CUSTOMIZADA->value && !is_null( $this->$campo ) && $this->$campo != '' )
				//Definindo
				$this->$campo	= Util::formatarData( $this->$campo, strlen( $this->$campo ) > 10 ? 'banco' : 'banco-sem-hora' );

			//Verificando se é para formatar o campo
			if( $formatar && !isset( $opcoes[ 'formatar' ] ) ){

				//Definindo
				$this->$campo	= !is_null( $this->$campo ) ? Util::formatarCaracteres( $this->$campo ) : null;

				//Verificando
				if( ( $tipo == Campo::DATA_CUSTOMIZADA->value || $tipo == Campo::DATA->value ) && !is_null( $this->$campo ) && $this->$campo != '' )
					//Definindo o campo
					$this->$campo	= Util::formatarData( $this->$campo, strlen( $this->$campo ) > 10 ? 'padrão' : 'pt-sem-hora' );

			}

		}

	}

	/**
	 * Serializando o JSON
	 *
	 * @return mixed
	 */
	public function jsonSerialize(): mixed{

		//Se o modelo ainda não foi inicializado com propriedades, não há o que serializar.
		if( !isset( $this->propriedades ) )
			return null;

		//Listando
		foreach( $this->propriedades as $campo => $valor )
			//Verificando se o campo foi inicializado e não é nulo
			if( isset( $this->$campo ) && !is_null( $this->$campo ) )
				//Definindo os campos
				$campos[ $campo ]	= $valor;

		//Retornando
		return $campos ?? null;

    }

}