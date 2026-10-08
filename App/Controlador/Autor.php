<?php
//Definindo o namespace
namespace App\Controlador;

//Definindo as classes usadas
use App\Controlador\Principal\Controlador as ControladorPrincipal;
use App\Infraestrutura\Configuracao;
use App\Util\Util;
use App\Util\Validacao;
use Override;

/**
 * Controlador de autores
 * Herdada da classe pai Controlador
 *
 * @author Caroline Tacats <caroline.tacats62@gmail.com>
 * @package App\Controlador
 */
class Autor extends ControladorPrincipal
{
	// Definindo os campos relacionados
	protected const CAMPOS_RELACIONADOS = [
		'GenerosRelacionados' => 'generos'
	];

	/**
	 * Obetendo os complementos dos modelos
	 *
	 * @param mixed $conteudos Conteúdos
	 * @uses ControladorPrincipal::aplicarComplemento() Para aplicar os complementos
	 * @return mixed
	 */
	public function complemento(mixed $conteudos): mixed
	{
		return parent::aplicarComplemento($conteudos, self::CAMPOS_RELACIONADOS);
	}

	public function inserir(array $campos, array $arquivo = []): int
	{

		$campos['generos'] = $campos['generos'] ?? [];

		$this->processarImagem($campos, $arquivo);

		//Definindo o modelo
		$modelo = new $this->modelo($campos);

		//Inserindo e definindo o identificador
		$id = $this->repositorio->inserir($modelo);

		$this->relacionarComGenero(
			autor: $id,
			generos: $campos['generos']
		);

		//Retornando
		return $id;

	}

	public function editar(array $campos = [], array $arquivo = []): void
	{

		$this->processarImagem($campos, $arquivo);

		$this->relacionarComGenero(
			autor: $campos['id'],
			generos: $campos['generos'] ?? []
		);

		parent::editar($campos, $arquivo);
	}


	public function relacionarComGenero(int $autor, array $generos): void
	{

		//Removendo o relacionamento
		$this->repositorio->removerRelacionamento($autor, 'autor_genero', 'autor');

		//Listando
		foreach ($generos as $genero)
			//Relacionando
			$this->repositorio->relacionar($autor, $genero, 'autor_genero', 'autor', 'genero');

	}

	private function processarImagem(array &$campos, array $arquivo): void
	{
		// Verificando se um arquivo de imagem foi enviado
		if (!isset($arquivo['imagem']['name']) || $arquivo['imagem']['name'] === '') {
			return;
		}

		// Formatando o nome do arquivo
		$nomeArquivo = Util::formatarNomeArquivo($arquivo['imagem']['name']);
		Validacao::verificarImagem($arquivo['imagem']);

		// Definindo o caminho do arquivo e movendo para o repositório
		$caminhoArquivo = Configuracao::get('dir.repositorio') . "/autores/{$nomeArquivo}";
		move_uploaded_file($arquivo['imagem']['tmp_name'], $caminhoArquivo);

		// Definindo o campo de imagem para edição
		$campos['imagem'] = $nomeArquivo;

	}

}