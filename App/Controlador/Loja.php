<?php
//Definindo o namespace
namespace App\Controlador;

//Definindo as classes usadas
use App\Controlador\Principal\Controlador as ControladorPrincipal;

/**
 * Controlador de lojas
 * Herdada da classe pai Controlador
 *
 * @author Caroline Tacats <caroline.tacats62@gmail.com>
 * @package App\Controlador
 */
class Loja extends ControladorPrincipal
{
	// Definindo os campos relacionados
	protected const CAMPOS_RELACIONADOS = [
		'Categoria' => 'categoria'
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

	/**
	 * Listando as lojas do benefício
	 *
	 * @access public
	 * @return mixed
	 * @throws BancoDeDadosException
	 */
	public function listarPorBeneficio(): mixed
	{

		return $this->complemento(
			$this->repositorio
				->beneficio($this->beneficio)
				->listarPorBeneficio(),
			$this->complemento
		);

	}
	
}