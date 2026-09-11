<?php
//Definindo o namespace
namespace App\Infraestrutura;

//Definindo as classes usadas
use App\Infraestrutura\Enum\Tipo\Debug as TipoDeDebug;
use App\Infraestrutura\Configuracao;
use App\Infraestrutura\Base;

/**
 * Classe do debug do sistema
 * Herdada da classe base para auxílio nas chamadas
 *
 * @final
 * @author Caroline Tacats <caroline.tacats62@gmail.com>
 * @package App\Infraestrutura
 */
final class Debug extends Base{

    /**
     * Constante com as palavras chaves para o banco de dados
     *
     * @access private
     * @var array Array com as palavras chaves
     */
    private const PALAVRAS_CHAVES_BANCO_DADOS = [

        'AUTO_INCREMENT',
        'REFERENCES',
        'TIMESTAMP',
        'DATETIME',
        'TRUNCATE',
        'UNSIGNED',
        'DESCRIBE',
        'DISTINCT',
        'TINYINT',
        'VARCHAR',
        'INTEGER',
        'DEFAULT',
        'CHARSET',
        'DECIMAL',
        'BOOLEAN',
        'PRIMARY',
        'BETWEEN',
        'FOREIGN',
        'CASCADE',
        'EXISTS',
        'UNIQUE',
        'DELETE',
        'INSERT',
        'VALUES',
        'SELECT',
        'UPDATE',
        'ENGINE',
        'CREATE',
        'OFFSET',
        'SIGNED',
        'HAVING',
        'DOUBLE',
        'COLUMN',
        'TABLE',
        'ALTER',
        'RIGHT',
        'INNER',
        'OUTER',
        'GROUP',
        'ORDER',
        'LIMIT',
        'COUNT',
        'FALSE',
        'INDEX',
        'WHERE',
        'LOWER',
        'WHEN',
        'THEN',
        'ELSE',
        'TRUE',
        'DATE',
        'DROP',
        'JOIN',
        'LEFT',
        'INTO',
        'FROM',
        'SHOW',
        'TEXT',
        'NULL',
        'LIKE',
        'CASE',
        'KEY',
        'SET',
        'NOT',
        'NOW',
        'MAX',
        'MIN',
        'SUM',
        'AVG',
        'INT',
        'AND',
        'END',
        'ON',
        'IF',
        'IS',
        'IN',
        'AS',
        'OR',
        'BY' ];

    /**
     * Método construtor da classe
     *
     * @param int $tipo Tipo do debug
     * @return void
     */
    public function __construct( private int $tipo = TipoDeDebug::BANCO_DE_DADOS->value ){}

    /**
     * Debug do tipo banco de dados
     *
     * @return self
     */
    public function bancoDeDados(): self{

        //Listando todos os campos do debug
        foreach( $this->campos as $chave => $valor ){

			//Definindo e formatando o valor
            $valor	= is_numeric( $valor ) ? $valor : "'" . str_replace( "'", "''", $valor ) . "'";

			//Verificando a existência do parâmetro ":parametro"
            if( is_string( $chave ) && strpos( $chave, ':' ) !== 0 )
				//Definindo a chave do parâmetro
                $chave	= ":{$chave}";

            //Definindo a consulta caso o parâmetro seja "?", caso contrário, pegando a chave
            $this->string    = is_int( $chave ) ? preg_replace( '/\?/', $valor, $this->string, 1 ) : str_replace( $chave, $valor, $this->string ?? '' );

		}

        //Definindo as palavras chaves sem ser por referência
        $palavrasChaves = self::PALAVRAS_CHAVES_BANCO_DADOS;

        //Ordenando as palavras chaves para as maiores aparecerem primeiro. Ex: GROUP BY
        usort( $palavrasChaves, fn( $a, $b ) => strlen( $b ) <=> strlen( $a ) );

        //Definindo uma cor para as strings
        $this->queryFormatada    = preg_replace( '/(\'|&quot;)(.*?)(\\1)/', '<span style="color:#d69d85;">$1$2$3</span>', $this->string );

        //Definindo uma cor para os números
        $this->queryFormatada    = preg_replace( '/\b(\d+(\.\d+)?)\b/', '<span style="color:yellow;">$1</span>', $this->queryFormatada );

        //Definindo uma cor para o nome da tabela após palavras chaves
        $this->queryFormatada    = preg_replace_callback( '/\b(?:FROM|JOIN|INTO|UPDATE|TABLE|DESC|DESCRIBE)\s+([a-zA-Z0-9_\.]+)/i', fn( $match ) => str_ireplace( $match[ 1 ], '<span style="color:#b5cea8;">' . $match[ 1 ] . '</span>', $match[ 0 ] ), $this->queryFormatada );

        //Definindo uma cor para os aliases das colunas
        $this->queryFormatada    = preg_replace_callback( '/\bAS\s+([a-zA-Z0-9_]+)/i', fn( $match ) => str_ireplace( $match[ 1 ], '<span style="color:#c586c0;">' . $match[ 1 ] . '</span>', $match[ 0 ]), $this->queryFormatada );

        //Listando as palavras chaves
        foreach( $palavrasChaves as $palavra )
            //Definindo uma cor para as palavras chaves
            $this->queryFormatada = preg_replace( '/\b' . preg_quote( $palavra, '/') . '\b/i', '<span style="color:#569cd6;font-weight:bold;">$0</span>', $this->queryFormatada );

        //Retornando o objeto
        return $this;

    }

    /**
     * Formatando o trace
     *
     * @return void
     */
    public function trace(): void{

        //Definindo o trace
        $traces = debug_backtrace( DEBUG_BACKTRACE_IGNORE_ARGS );

        //Removendo o primeiro item do array que é justamente esse método
        array_shift( $traces );

        //Removendo o último item do array que é o arquivo de requisição
        array_pop( $traces );

        //Iniciando o trace
        $this->trace    = '';

        //Listando todo o trace
        foreach( $traces as $posicao => $array ){

            //Incrementando as informações
            $this->trace .= sprintf(
                '<div style="margin-bottom: 4px;">
                    <span style="color:#cccccc;">#%02d</span>
                    <span style="color:#9cdcfe;">%s</span>
                    <span style="color:#6a9955;">(%s):</span>
                    <span style="color:#dcdcaa;">%s%s%s</span>
                </div>',
                $posicao,
                $array[ 'file' ] ?? '[interno]',
                $array[ 'line' ] ?? '-',
                $array[ 'class' ] ?? null,
                $array[ 'type' ] ?? null,
                $array[ 'function' ] ?? null );

        }

    }

    /**
     * Renderizando o debug
     *
     * @return void
     */
    public function renderizar(): void{

        //Definindo um ID único para a cópia do código via JS
        $this->id   = 'unico_' . uniqid();

        //Verificando o tipo
        match( $this->tipo ){
            //Padrão(Banco de Dados)
            default => $this->renderizarBancoDeDados() };

    }

    /**
     * Renderizando o HTML para queries de Banco de Dados
     *
     * @access private
     * @return void
     */
    private function renderizarBancoDeDados(): void{

        //Exibindo o HTML
        echo <<<HTML
            <div id="{$this->id}" style="font-family: Consolas, Menlo, Monaco, monospace;background: #1e1e1e;color: #d8a0df;padding: 16px;border-left: 4px solid #007acc;border-radius: 6px;margin: 24px 0;overflow-x: auto;box-shadow: 0 4px 10px rgba(0,0,0,0.3);position: relative;">
                <div style="font-size: 14px;color: #9cdcfe;margin-bottom: 8px;font-weight: bold;display: flex;justify-content: space-between;align-items: center;">
                    <span style="font-size: 12px; color: #6a9955;">⚙️SQL ⏱️{$this->duracao}ms</span>
                    <button onclick="(function(){ const el = document.querySelector('#{$this->id} pre'); const temp = document.createElement('textarea'); temp.value = el.innerText; document.body.appendChild(temp); temp.select(); document.execCommand('copy'); document.body.removeChild(temp); alert('Consulta copiada!'); })()" style="background: #007acc;color: #fff;border: none;padding: 4px 10px;font-size: 12px;border-radius: 4px;cursor: pointer;">📋 Copiar</button>
                </div>
                <pre style="margin: 0;font-size: 13px;line-height: 1.6;white-space: pre-wrap;">{$this->queryFormatada}</pre>
                {$this->renderizarTrace()}
            </div>
        HTML;

    }

    /**
     * Renderizando o HTML para traces
     *
     * @access private
     * @uses App\Infraestrutura\Configuracao::get() Retornando a variável do projeto
     * @return mixed
     */
    public function renderizarTrace(): mixed{

        //Retornando ou não o HTML do trace
        return Configuracao::get( 'debug.trace' ) ? <<<HTML
            <div style="color: #cccccc; font-size: 12px; border-top: 1px solid #333; padding-top: 8px;margin-top:8px;">
                <strong style="color: #9cdcfe;">Trace:</strong>
                <div style="font-size: 12px; line-height: 1.4;margin-top:4px;">{$this->trace}</div>
            </div>
        HTML : null;

    }

}