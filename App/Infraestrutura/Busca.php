<?php
//Definindo o namespace
namespace App\Infraestrutura;

//Definindo as classes usadas
use App\Infraestrutura\Pagina;
use App\Util\Util;

/**
 * Classe de busca
 *
 * @final
 * @author Caroline Tacats <caroline.tacats62@gmail.com>
 * @package App\Infraestrutura\Busca
 */
final class Busca
{

    /**
     * Filtros
     *
     * @var array<string, mixed>
     */
    private array $filtros = [];

    /**
     * Apelidos
     *
     * @var array<string, mixed>
     */
    private array $apelidos = [];

    /**
     * Tem filtros?
     *
     * @var bool
     */
    private bool $temFiltros = false;

    /**
     * Limitador de pÃ¡ginaÃ§Ã£o
     *
     * @var int
     */
    private int $limitador;

    /**
     * Construtor da classe.
     *
     * @param Pagina $pagina PÃ¡gina.
     * @param array $mapaGet Mapa do array $_GET.
     * @param array $filtrosFixos Filtros fixos.
     * @return void
     */
    public function __construct(
        Pagina $pagina,
        array $mapaGet = [],
        array $filtrosFixos = [],
    ) {

        //Definindo o limitador
        $this->limitador = Util::definirLimitador($pagina->pagina);

        //Listando
        foreach ($mapaGet as $chaveInterna => $paramGet) {

            //Definindo os filtros
            $this->filtros[$chaveInterna] = Util::sanitizar($_GET[$paramGet] ?? null);

            //Definindo os apelidos
            $this->apelidos[$chaveInterna] = $paramGet;

        }

        //Listando
        foreach ($filtrosFixos as $chave => $valor)
            //Definindo os filtros
            $this->filtros[$chave] = $valor;

        //Definindo se tem filtros
        $this->temFiltros = (bool) array_filter(
            $this->filtros,
            fn($v) => !is_null($v) && $v !== ''
        );

    }

    /**
     * Retornando todos os filtros.
     *
     * @return array<string, mixed>
     */
    public function todos(): array
    {

        //Retornando
        return $this->filtros;

    }

    /**
     * Obtendo um filtro pela chave.
     *
     * @param string $chave Chave do filtro.
     * @return mixed
     */
    public function obter(string $chave): mixed
    {

        //Retornando
        return $this->filtros[$chave] ?? null;

    }

    /**
     * Indica se hÃ¡ ao menos um filtro preenchido.
     *
     * @return bool
     */
    public function temFiltros(): bool
    {

        //Retornando
        return $this->temFiltros;

    }

    /**
     * Retornando o limitador de paginaÃ§Ã£o.
     *
     * @return int
     */
    public function limitador(): int
    {

        //Retornando
        return $this->limitador;

    }

    /**
     * Obtendo o apelido pela chave interna.
     *
     * @param string $chaveInterna Chave interna.
     * @return null|string
     */
    public function apelido(string $chaveInterna): ?string
    {

        //Retornando
        return $this->apelidos[$chaveInterna] ?? null;

    }

    /**
     * Montando a query string apenas com as queries preenchidas.
     *
     * @return string
     */
    public function query(): string
    {

        //Definindo os parametros
        $parametros = [];

        //Listando
        foreach ($this->filtros as $chaveInterna => $valor)
            //Verificando
            if (!is_null($valor) && $valor !== '')
                //Definindo
                $parametros[$this->apelidos[$chaveInterna] ?? $chaveInterna] = $valor;

        //Retornando
        return http_build_query($parametros);

    }

}