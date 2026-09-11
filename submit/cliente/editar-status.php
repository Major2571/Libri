<?php
/*
 * Requisição para editar o status do benefício para retirado
 */

//Definindo as classes usadas
use App\Excecao\BancoDeDados as BancoDeDadosException;
use App\Infraestrutura\Configuracao;
use App\Infraestrutura\Enum\Mensagem\Conteudo;
use App\Infraestrutura\Enum\Status\Http;
use App\Infraestrutura\Enum\Status\Status;
use App\Infraestrutura\Fachada;
use App\Util\Util;

//Definindo o cabeçalho do arquivo
header('Content-type: application/json');

try {

	// Verificando o tipo de requisição
	Util::verificarRequisicao('POST');

    // Validando e decodificando os identificadores
    $clienteId      = Util::validarEDecodificarIdentificador($_POST['cliente'], Configuracao::get('projeto.hash'));
    $beneficioId    = Util::validarEDecodificarIdentificador($_POST['beneficio'], Configuracao::get('projeto.hash'));

    // Verificando a referência e o token CSRF
    if (Util::verificarReferencia() && Util::verificarTokenCsrf($_POST['csrf-token'] ?? null)) {

        // Editando o status do benefício para 'retirado'
        $resposta = Fachada::instancia('Beneficio\Usuario\Beneficio')->editarStatus($beneficioId, Status::RETIRADO->value);

        // Definindo o retorno
        $retorno = [
            'tipo'      => 'sucesso',
            'acao'      => 'editar-status',
            'resposta'  => Http::OK->codigo(),
            'mensagem'  => 'Retirada do benefício realizada com sucesso!',
        ];

    } else {

        //Definindo o código de resposta do arquivo
        http_response_code(Http::FORBIDDEN->codigo());

        //Definindo o retorno
        $retorno = [
            'tipo'      => 'atencao',
            'mensagem'  => Conteudo::SEM_AUTORIZACAO->texto(),
            'resposta'  => Http::FORBIDDEN->codigo()
        ];

    }

} catch (Exception $e) {

    //Definindo o retorno
    $retorno = [
        'tipo'      => $e instanceof BancoDeDadosException ? 'erro' : 'atencao',
        'resposta'  => $e->getCode(),
        'mensagem'  => $e->getMessage()
    ];

}

//Retornando
echo json_encode($retorno);