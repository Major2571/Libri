<?php
/*
 * Requisição para a redefinição da senha do cliente
 */

//Definindo as classes usadas
use App\Excecao\BancoDeDados as BancoDeDadosException;
use App\Infraestrutura\Enum\Mensagem\Conteudo;
use App\Infraestrutura\Enum\Tipo\Sucesso;
use App\Infraestrutura\Enum\Status\Http;
use App\Infraestrutura\Fachada;
use App\Util\Util;

//Definindo o cabeçalho do arquivo
header('Content-type: application/json');

try {

	//Verificando o tipo de requisição
	Util::verificarRequisicao('POST');

	//Verificando o token
	if ( Util::verificarReferencia() && Util::verificarTokenCsrf($_POST['csrf-token'] ?? null)) {

		// Redefinindo a senha do cliente
		Fachada::instancia('Lojista')->esqueciMinhaSenha($_POST['email'] ?? null);

		//Definindo o retorno
		$retorno = [
			'tipo'      => 'sucesso',
			'acao'      => 'recuperar-senha',
			'resposta'  => Http::OK->codigo(),
			'dados' 	=> [
				'codigo' => Sucesso::ESQUECI_MINHA_SENHA->value
			]
		];

	} else {

		//Definindo o código de resposta do arquivo
		http_response_code(Http::FORBIDDEN->codigo());

		//Definindo o retorno
		$retorno = [
			'tipo'     => 'atencao',
			'mensagem' => Conteudo::SEM_AUTORIZACAO->texto(),
			'resposta' => Http::FORBIDDEN->codigo()
		];

	}

} catch (Exception $e) {

	//Definindo o retorno
	$retorno = [
		'tipo' 	   => $e instanceof BancoDeDadosException ? 'erro' : 'atencao',
		'resposta' => $e->getCode(),
		'mensagem' => $e->getMessage()
	];

}

//Retornando
echo json_encode($retorno);