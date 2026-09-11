<?php
//Definindo o namespace
namespace App\Repositorio\BDR\Inscricao\Evento;

//Definindo as classes
use App\Repositorio\BDR\Repositorio as RepositorioPrincipal;
use App\Excecao\BancoDeDados as BancoDeDadosException;
use App\Infraestrutura\Enum\Mensagem\Repositorio;
use App\Infraestrutura\Enum\Status\Http;
use App\Excecao\Conteudo as ConteudoException;
use App\Infraestrutura\Enum\Mensagem\Conteudo;
use PDO;
use PDOException;

/**
 * Repositório das inscrições de eventos
 * Herdada da classe pai Repositorio.
 *
 * @author Caroline Tacats <caroline.tacats62@gmail.com>
 * @package Repositorio\BDR\Inscricao\Evento
 */
class Inscricao extends RepositorioPrincipal
{

    /*
     * LISTAGENS
     */

    /**
     * Listando os conteúdos com suporte para busca em evento relacionado
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

            // Definindo o complemento para a consulta
            $complemento = $this->complemento;

            // Ajustando os campos para a consulta com JOIN quando há busca
            if ($temBusca) {

                // Verificando se o complemento já possui alias para os campos, caso contrário, adiciona o alias 'ie' para evitar ambiguidades na consulta com JOIN
                $patterns = [
                    '/(?<![:.\w])data_criacao(?![:.\w])/i',
                    '/(?<![:.\w])status(?![:.\w])/i'
                ];

                $replacements = ['ie.data_criacao', 'ie.status'];
                $complemento  = preg_replace($patterns, $replacements, $complemento);

                // SQL com JOIN quando há busca
                $sql = "SELECT ie.id, 
                               ie.usuario, 
                               ie.evento, 
                               ie.horario_evento, 
                               ie.nome, 
                               ie.documento, 
                               ie.email, 
                               ie.telefone, 
                               ie.codigo, 
                               ie.status
                        FROM inscricao_evento ie
                        INNER JOIN evento e ON (ie.evento = e.id)
                        WHERE ie.id IS NOT NULL {$complemento}";

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
                        sprintf(Conteudo::NENHUM_CONTEUDO_ENCONTRADO->texto(), $this->configuracao->nome),
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

    /**
     * Contar inscrições por CPF, evento e horário de evento
     *
     * @param string $cpf O CPF do participante
     * @param int $eventoId O ID do evento
     * @param ?int $horarioEvento O ID do horário do evento (opcional)
     * @return int O número de inscrições encontradas
     * @throws BancoDeDadosException Se ocorrer um erro ao contar as inscrições
     */
    public function contarPorCpfEvento(string $cpf, int $eventoId, ?int $horarioEvento = null): int
    {
        try {

            // Definindo a consulta SQL para contar as inscrições por CPF, evento e horário de evento
            $sql = "SELECT COUNT(*) FROM {$this->configuracao->tabela} 
                    WHERE documento = :cpf 
                    AND evento = :eventoId 
                    AND status = 1";

            // Adicionando a condição para o horário do evento se for fornecido
            if ($horarioEvento !== null) {
                $sql .= " AND horario_evento = :horarioEvento";
            }

            // Preparando a consulta
            $stmt = $this->conexao->prepare($sql);

            // Definindo os parâmetros
            $stmt->bindParam(':cpf', $cpf, PDO::PARAM_STR);
            $stmt->bindParam(':eventoId', $eventoId, PDO::PARAM_INT);

            // Definindo o parâmetro para o horário do evento se for fornecido
            if ($horarioEvento !== null) {
                $stmt->bindParam(':horarioEvento', $horarioEvento, PDO::PARAM_INT);
            }

            // Executando a consulta
            $stmt->execute();

            // Retornando o número de inscrições encontradas
            return (int) $stmt->fetchColumn();

        } catch (PDOException $e) {

            //Lançando a exceção
            throw new BancoDeDadosException(Repositorio::CONTAR->texto(), $e, resposta: Http::BAD_REQUEST->codigo());

        }
    }

}
