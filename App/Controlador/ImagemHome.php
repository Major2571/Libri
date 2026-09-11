<?php
//Definindo o namespace
namespace App\Controlador;

//Definindo as classes usadas
use App\Controlador\Principal\Controlador as ControladorPrincipal;

/**
 * Controlador de imagens da home
 * Herdada da classe pai Controlador
 *
 * @author Caroline Tacats <caroline.tacats62@gmail.com>
 * @package App\Controlador
 */
class ImagemHome extends ControladorPrincipal
{
	// Definindo os campos relacionados
	protected const CAMPOS_RELACIONADOS = [
		'Ge' => 'imagem',
		'SegundaImagem' => 'segunda_imagem'
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