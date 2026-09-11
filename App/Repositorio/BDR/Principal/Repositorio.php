<?php
//Definindo o namespace
namespace App\Repositorio\BDR\Principal;

//Definindo as classes usadas
use App\Excecao\BancoDeDados as BancoDeDadosException;
use App\Infraestrutura\Enum\Mensagem\Repositorio as MensagemRepositorio;
use App\Infraestrutura\Enum\Status\Http;
use PDOException;
use App\Repositorio\BDR\Repositorio as RepositorioAbstrato;
use PDO;

/**
 * Repositório
 * Herdada da classe pai Repositorio. Classe usada em caso da não existência do repositório do modelo.
 *
 * @author Caroline Tacats <caroline.tacats62@gmail.com>
 * @package App\Repositorio\BDR\Principal
 */
class Repositorio extends RepositorioAbstrato
{

    /**
     * Método para remover o relacionamento entre os registros de uma tabela de relacionamento
     *
     * @param int $id O ID do registro a ser removido
     * @param string $nomeTabela O nome da tabela de relacionamento
     * @param string $nomeColuna O nome da coluna que representa o ID do registro na tabela de relacionamento
     * @throws BancoDeDadosException Se ocorrer um erro ao remover o relacionamento
     */
    public function removerRelacionamento(int $id, string $nomeTabela, string $nomeColuna): void
    {

        try {

            // Definindo a consulta SQL para remover o relacionamento
            $sql = sprintf(
                'DELETE FROM %s 
					     WHERE %s = :id',
                $nomeTabela,
                $nomeColuna
            );

            //Preparando a consulta
            $this->pdo = $this->conexao->prepare($sql);

            //Definindo os valores
            $this->pdo->bindValue(':id', $id, PDO::PARAM_INT);

            //Executando a consulta
            $this->pdo->execute();

        } catch (PDOException $e) {

            //Lançando a exceção
            throw new BancoDeDadosException(
                MensagemRepositorio::RELACIONAR->texto(),
                $e,
                resposta: Http::BAD_REQUEST->codigo()
            );


        }

    }

    /**
     * Método para relacionar registros de duas tabelas
     *
     * @param int $primeiroId O ID do primeiro registro
     * @param int $segundoId O ID do segundo registro
     * @param string $nomeTabela O nome da tabela de relacionamento
     * @param ?string $primeiroCampo O nome da coluna que representa o ID do primeiro registro na tabela de relacionamento
     * @param ?string $segundoCampo O nome da coluna que representa o ID do segundo registro na tabela de relacionamento
     * @throws BancoDeDadosException Se ocorrer um erro ao relacionar os registros
     */
    public function relacionar(
        int $primeiroId,
        int $segundoId,
        string $nomeTabela,
        ?string $primeiroCampo,
        ?string $segundoCampo
    ): void {

        try {

            // Definindo a consulta SQL para relacionar os registros
            $sql = sprintf(
                'INSERT INTO %s (%s, %s)
                VALUES (:primeiro, :segundo)',
                $nomeTabela,
                $primeiroCampo,
                $segundoCampo
            );

            // Preparando a consulta
            $this->pdo = $this->conexao->prepare($sql);

            // Definindo os valores
            $this->pdo->execute([
                ':primeiro' => $primeiroId,
                ':segundo' => $segundoId
            ]);

        } catch (PDOException $e) {

            // Lançando a exceção
            throw new BancoDeDadosException(
                MensagemRepositorio::RELACIONAR->texto(),
                $e,
                resposta: Http::BAD_REQUEST->codigo()
            );

        }
    }

}