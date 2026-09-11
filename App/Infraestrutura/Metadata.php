<?php
//Definindo o namespace
namespace App\Infraestrutura;

//Definindo as classes usadas
use App\Infraestrutura\Configuracao;
use App\Infraestrutura\Fachada;
use App\Infraestrutura\Pagina;
use App\Infraestrutura\Base;
use App\Util\Util;

/**
 * Classe da metasdata
 * Herdada da classe base para auxílio nas chamadas
 *
 * @final
 * @author Caroline Tacats <caroline.tacats62@gmail.com>
 * @package App\Infraestrutura
 */
final class Metadata extends Base{

    /**
     * Método construtor da classe
     *
     * @param string $lingua língua passada para tradução
	 * @uses App\Infraestrutura\Configuracao::get() Retornando a variável do projeto
     * @uses App\Util\Util::definirNomeConstante() Definindo o nome da constante
     * @uses App\Util\Util::verificarMetadata() Verificando a metadata
     * @uses App\Infraestrutura\Fachada::instancia() Definindo a instância da fachada
     */
    public function __construct( private Pagina $pagina ){

        //Definindo o arquivo
        $arquivo    = Configuracao::get( 'projeto.metadata' );

		//Verificando se o arquivo de metadatas existe
		if( file_exists( $arquivo ) ){

			//Incluindo o arquivo de metadatas
			require_once( $arquivo );

            //Definindo o nome da página
            $meta       = Util::definirNomeConstante( $pagina->diretorio );

            //Definindo a constante
            $constante  = Util::verificarMetadata( "METADATA_{$meta}" );

            //Verificando a constante
            if( $constante ){

                //Definindo o objeto
                $objeto = Fachada::instancia( 'Metadata' )
                    ->configurar( excecao: false )
                    ->procurarPorIdentificador( $constante );

                //Verificando
                if( !is_null( $objeto ) ){

                    //Definindo a URL
                    $url                            = Configuracao::get( 'url.padrao' );

                    //Definindo o objeto
                    $this->metadata                 = $objeto;

                    //Definindo a descrição
                    $this->metadata->meta           = $objeto->meta_descricao;

                    //Definindo a imagem de compartilhamento
                    $this->metadata->imagem         = !is_null( $objeto->imagem ) ? Configuracao::get( 'url.repositorio' ) . "/metadatas/{$objeto->imagem}" : Configuracao::get( 'url.compartilhamento' );

                    //Definindo o título da imagem de compartilhamento
                    $this->metadata->titulo_imagem  = $objeto->titulo_imagem ?? 'Imagem de compartilhamento';

                    //Definindo a URL
                    $this->metadata->url            = $pagina->diretorio == 'home' ? $url : "{$url}/{$pagina->diretorio}";

                }

            }

        }

    }

}