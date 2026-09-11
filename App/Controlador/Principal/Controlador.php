<?php
//Definindo o namespace
namespace App\Controlador\Principal;

//Definindo as classes usadas
use App\Controlador\Controlador as ControladorPrincipal;

/**
 * Classe do controlador Principal
 * Herdada da classe pai Controlador. Classe usada em caso da não existência do controlador do modelo.
 *
 * @author Caroline Tacats <caroline.tacats62@gmail.com>
 * @package App\Controlador\Principal
 */
class Controlador extends ControladorPrincipal
{

    /**
     * Aplicando o complemento nos conteúdos
     *
     * @param mixed $conteudos Conteúdos
     * @param array $controladores Controladores para aplicar o complemento
     * @uses App\Controlador\Controlador::removerControlador() Removendo os controladores
     * @uses App\Controlador\Principal\Controlador::obterDadosConteudo() Obtendo os dados do conteúdo
     * @return mixed
     */
    public function aplicarComplemento(mixed $conteudos, ?array $controladores = []): mixed
    {

        // Definindo os controladores para remoção
        $controladoresRemover = array_keys($controladores);

        // Verificando se é para definir o complemento
        if (!$this->complemento) {

            // Removendo os controladores
            $this->removerControlador(
                controladores: $controladoresRemover,
                conteudos: $conteudos
            );

            // Retornando
            return $conteudos;

        }

        // Verificando
        if (is_array($conteudos))
            // Listando
            foreach ($conteudos as $conteudo)
                // Definindo os dados do conteúdo
                $this->obterDadosConteudo($conteudo, $controladores);

        // Verificando a existência de conteúdos
        if (!is_null($conteudos) && !is_array($conteudos))
            // Definindo os dados do conteúdo
            $this->obterDadosConteudo($conteudos, $controladores);

        // Removendo os controladores
        $this->removerControlador(
            controladores: $controladoresRemover,
            conteudos: $conteudos
        );

        // Retornando
        return $conteudos;

    }

    /**
     * Obtendo os dados do conteúdo
     *
     * @param object $conteudo Conteúdo
     * @param array $campos Campos do conteúdo
     * @return void
     */
    public function obterDadosConteudo(object $conteudo, ?array $campos = []): void
    {

        // Listando os campos
        foreach ($campos as $nomeControlador => $campoBanco) {

            // Definindo o controlador
            $propControlador = 'controlador' . $nomeControlador;

            // Verificando a existência do controlador para o conteúdo
            if (!property_exists($conteudo, $propControlador) || is_null($conteudo->{$propControlador})) {
                continue;
            }

            // Definindo o controlador
            $controlador = $conteudo->{$propControlador};

            // Verificando o nome do controlador para obter os dados do conteúdo
            if ($nomeControlador === 'Agenda' || $nomeControlador === 'CampoAdicional') {

                $conteudo->{$campoBanco} = $controlador
                    ->configurar(excecao: false, complemento: false)
                    ->evento($conteudo->id)
                    ->listarPorEvento();

                continue;

            }

            // Verificando o nome do controlador para obter os dados do conteúdo
            if ($nomeControlador === 'BeneficioUsuario') {

                $conteudo->{$campoBanco} = $controlador
                    ->usuario($conteudo->id)
                    ->listarPorUsuario();

                continue;

            }

            // Verificando o nome do controlador para obter os dados do conteúdo
            if ($nomeControlador === 'Loja') {

                $conteudo->lojas = $conteudo->controladorLoja
                    ->configurar(excecao: false, complemento: false)
                    ->beneficio($conteudo->id)
                    ->listarPorBeneficio();

                continue;

            }

            // Verificando o nome do controlador para obter os dados do conteúdo
            if ($nomeControlador === 'Categoria') {

                $conteudo->categoria = $conteudo->controladorCategoria
                    ->configurar(excecao: false, complemento: false)
                    ->id($conteudo->categoria)
                    ->procurarPorIdentificador();

                continue;

            }

            // Obtendo os dados do conteúdo
            $conteudo->{$campoBanco} = $controlador
                ->configurar(excecao: false)
                ->id($conteudo->{$campoBanco})
                ->procurarPorIdentificador();

        }

    }

    /**
     * Listando os conteúdos por usuário
     *
     * @uses App\Repositorio\Repositorio::usuario() Definindo o usuário para os métodos(chamada via _call)
     * @uses App\Repositorio\BDR\Repositorio::listar() Lista os conteúdos
     * @return mixed
     */
    public function listarPorUsuario(): mixed
    {

        return $this->complemento(
            $this->repositorio
                ->usuario($this->usuario)
                ->listarPorUsuario(),
            $this->complemento
        );

    }

    /**
     * Listando os conteúdos por evento
     *
     * @uses App\Repositorio\Repositorio::evento() Definindo o evento para os métodos(chamada via _call)
     * @uses App\Repositorio\BDR\Repositorio::listar() Lista os conteúdos
     * @return mixed
     */
    public function listarPorEvento(): mixed
    {

        return $this->complemento(
            $this->repositorio
                ->evento($this->evento)
                ->listar(),
            $this->complemento
        );

    }

    /**
     * Listando os conteúdos por evento, apenas as datas futuras, ordenado
     *
     * @uses App\Repositorio\Repositorio::evento() Definindo o evento para os métodos(chamada via _call)
     * @uses App\Repositorio\BDR\Repositorio::dataEhFutura() Definindo a data futura para os métodos(chamada via _call)
     * @uses App\Repositorio\BDR\Repositorio::ordem() Definindo a ordem para os métodos(chamada via _call)
     * @uses App\Repositorio\BDR\Repositorio::listar() Lista os conteúdos
     * @return mixed
     */
    public function listarDatasFuturasPorEventoOrdenado(): mixed
    {

        return $this->complemento(
            $this->repositorio
                ->ordem($this->ordem)
				->dataEhFutura()
                ->evento($this->evento)
                ->listar(),
            $this->complemento
        );

    }

    /**
     * Listando os conteúdos por usuário, ordenado por quantidade
     *
     * @uses App\Repositorio\Repositorio::usuario() Definindo o usuário para os métodos(chamada via _call)
     * @uses App\Repositorio\Repositorio::ordem() Definindo a ordem para os métodos(chamada via _call)
     * @uses App\Repositorio\Repositorio::quantidade() Definindo a quantidade para os métodos(chamada via _call)
     * @uses App\Repositorio\BDR\Repositorio::listar() Lista os conteúdos
     * @return mixed
     */
    public function listarPorUsuarioOrdenadoPorQuantidade(): mixed
    {

        return $this->complemento(
            $this->repositorio
                ->quantidade($this->quantidade)
                ->ordem($this->ordem)
                ->usuario($this->usuario)
                ->listar(),
            $this->complemento
        );

    }

    /**
     * Listando os conteúdos por usuário, por termos e ordenado por quantidade
     * 
     * @uses App\Repositorio\Repositorio::usuario() Definindo o usuário para os métodos(chamada via _call)
     * @uses App\Repositorio\Repositorio::termos() Definindo os termos para os métodos(chamada via _call)
     * @uses App\Repositorio\BDR\Repositorio::listar() Lista os conteúdos
     * @return mixed
     */
    public function listarPorUsuarioPorTermosOrdenadoPorQuantidade(): mixed
    {

        return $this->complemento(
            $this->repositorio
                ->quantidade($this->quantidade)
                ->ordem($this->ordem)
                ->usuario($this->usuario)
                ->termos($this->termos)
                ->listar(),
            $this->complemento
        );

    }

}