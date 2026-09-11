<?php
//Definindo o namespace
namespace App\Controlador;

//Definindo as classes usadas
use App\Controlador\Principal\Controlador as ControladorPrincipal;

/**
 * Controlador de anúncios
 * Herdada da classe pai Controlador
 *
 * @author Caroline Tacats <caroline.tacats62@gmail.com>
 * @package App\Controlador
 */
class Genero extends ControladorPrincipal
{
	// Definindo os campos relacionados
	protected const CAMPOS_RELACIONADOS = [
		'Genero' => 'genero_pai'
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
	
}