<?php
//Definindo o namespace
namespace App\Repositorio\BDR;

//Definindo as classes
use App\Repositorio\BDR\Repositorio as RepositorioPrincipal;
use App\Excecao\BancoDeDados as BancoDeDadosException;
use App\Infraestrutura\Enum\Mensagem\Repositorio;
use App\Infraestrutura\Enum\Status\Http;
use PDOException;

/**
 * Repositório dos interesses
 * Herdada da classe pai Repositorio.
 *
 * @author Caroline Tacats <caroline.tacats62@gmail.com>
 * @package App\Repositorio\BDR\Principal
 */
class Interesse extends RepositorioPrincipal
{

    /*
     * LISTAGENS
     */

    /**
     * Listando os conteúdos por usuario
     *
     * @access public
     * @return mixed
     * @throws BancoDeDadosException
     */
    public function listarPorUsuario(): mixed
    {

        try {

            // Definindo a consulta
            $sql = "SELECT I.id, I.nome
                    FROM interesse I 
                    INNER JOIN usuario_plaza_mais_interesse UPMI ON (I.id = UPMI.interesse) 
                    WHERE I.id IS NOT NULL {$this->complemento}";

            //Preparando a consulta
            $this->executar($sql);

            //Definindo os registros
            $conteudos = $this->pdo->fetchAll();

            //Verificando
            if (count($conteudos) > 0)
                //Listando os conteúdos
                foreach ($conteudos as $conteudo)
                    //Definindo
                    $array[] = (object) (new $this->modelo($conteudo, $this->formatar, $this->remover))->propriedades;
            else
                //Retornando
                return null;

            //Retornando
            return $array ?? null;

        } catch (PDOException $e) {

            //Lançando a exceção
            throw new BancoDeDadosException(Repositorio::LISTAR->texto(), $e, resposta: Http::BAD_REQUEST->codigo());

        } finally {

            //Limpando os parâmetros e complemento
            $this->limparComplementosParametros();

        }

    }

}