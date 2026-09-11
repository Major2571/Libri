<?php
//Definindo o namespace
namespace App\Repositorio\BDR\Beneficio\Usuario;

//Definindo as classes
use App\Repositorio\BDR\Repositorio as RepositorioPrincipal;
use App\Excecao\BancoDeDados as BancoDeDadosException;
use App\Infraestrutura\Enum\Mensagem\Repositorio;
use App\Infraestrutura\Enum\Status\Http;
use App\Infraestrutura\Enum\Mensagem\Conteudo;
use App\Excecao\Conteudo as ConteudoException;
use PDOException;

/**
 * Repositório dos benefícios do usuário
 * Herdada da classe pai Repositorio.
 *
 * @author Caroline Tacats <caroline.tacats62@gmail.com>
 * @package Repositorio\BDR\Beneficio\Usuario
 */
class Beneficio extends RepositorioPrincipal
{

    /*
     * LISTAGENS
     */

    /**
     * Listando os conteúdos com suporte para busca em benefício relacionado
     *
     * @access public
     * @return mixed
     * @throws BancoDeDadosException
     */
    public function listar(): mixed
    {

        try {

            // Verificando se há termo de busca
            $temBusca = strpos($this->complemento, 'LOWER(') !== false;

            // Preparar o complemento qualificando colunas ambíguas
            $complemento = $this->complemento;

            // Verificando se tem busca
            if ($temBusca) {

                // Qualificar data_criacao para bu.data_criacao quando houver JOIN
                $complemento = str_replace('data_criacao', 'bu.data_criacao', $complemento);

                // SQL com JOIN quando há busca
                $sql = "SELECT bu.id, 
                               bu.usuario, 
                               bu.beneficio, 
                               bu.codigo, 
                               bu.status, 
                               bu.data_resgate
                        FROM beneficio_usuario bu
                        INNER JOIN beneficio b ON (bu.beneficio = b.id)
                        WHERE bu.id IS NOT NULL {$complemento}";

            } else {

                // SQL padrão
                $sql = "SELECT {$this->configuracao->campos} 
                        FROM {$this->configuracao->tabela} 
                        WHERE id IS NOT NULL {$this->complemento}";

            }

            //Preparando a consulta
            $this->executar($sql);

            //Definindo os registros
            $conteudos = $this->pdo->fetchAll();

            //Verificando
            if (count($conteudos) > 0)
                //Listando os conteúdos
                return array_map(
                    fn($conteudo) => (object) (
                        new $this->modelo($conteudo, $this->formatar, $this->remover))->propriedades,
                    $conteudos
                );
            else
                //Verificando se é para exibir a exceção
                if ($this->excecao)
                    //Lançando a exceção
                    throw new ConteudoException(
                        sprintf( Conteudo::NENHUM_CONTEUDO_ENCONTRADO->texto(), $this->configuracao->nome),
                        Http::NOT_FOUND->codigo()
                    );
                else
                    //Retornando
                    return null;

        } catch (PDOException $e) {

            //Lançando a exceção
            throw new BancoDeDadosException(Repositorio::LISTAR->texto(), $e, resposta: Http::BAD_REQUEST->codigo());

        } finally {

            //Limpando os parâmetros e complemento
            $this->limparComplementosParametros();
            
        }

    }

}
