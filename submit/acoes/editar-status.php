<?php

use App\Excecao\BancoDeDados as BancoDeDadosException;
use App\Infraestrutura\Enum\Mensagem\Conteudo;
use App\Infraestrutura\Enum\Status\Http;
use App\Infraestrutura\Enum\Status\Status;
use App\Infraestrutura\Fachada;
use App\Util\Util;

header('Content-type: application/json');

try {

	Util::verificarRequisicao('POST');

	if (!Util::verificarReferencia() || !Util::verificarTokenCsrf($_POST['csrf-token'] ?? null)) {

		http_response_code(Http::FORBIDDEN->codigo());
		$retorno = [
			'tipo' => 'atencao',
			'mensagem' => Conteudo::SEM_AUTORIZACAO->texto(),
			'resposta' => Http::FORBIDDEN->codigo()
		];

	} else {

		$status = filter_var($_POST['status'] ?? null, FILTER_VALIDATE_INT);

		if ($status === false || !Status::tryFrom($status)) {

			http_response_code(Http::BAD_REQUEST->codigo());
			throw new InvalidArgumentException('O status informado é inválido.');

		}

		$id = $pagina->id ?? $_POST['id'] ?? 0;

		Fachada::instancia($pagina->modelo)->editarStatus((int) $id, $status);

		$retorno = [
			'tipo' => 'sucesso',
			'acao' => 'editar-status',
			'resposta' => Http::OK->codigo(),
			'mensagem' => 'Status atualizado com sucesso!'
		];

	}

} catch (Exception $e) {

	$retorno = [
		'tipo' => $e instanceof BancoDeDadosException ? 'erro' : 'atencao',
		'resposta' => $e->getCode() ?: Http::BAD_REQUEST->codigo(),
		'mensagem' => $e->getMessage()
	];
    
}

echo json_encode($retorno);
