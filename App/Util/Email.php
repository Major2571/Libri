<?php
//Definindo o namespace
namespace App\Util;

//Definindo as classes usadas
use App\Infraestrutura\Configuracao;
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;

/**
 * Classe de email
 *
 * @author Caroline Tacats <caroline.tacats62@gmail.com>
 * @package App\Sistema\Util\Email
 */
class Email{

	/**
	 * Método construtor da classe
	 *
	 * @param mixed $destinatario Destinatário
	 * @param string $assunto Assunto
	 * @param string $mensagem Mensagem
	 * @param null|string $anexo Arquivo de anexo
	 * @param bool $debug Exibindo ou não o modo debug
	 * @uses App\Infraestrutura\Configuracao::get() Retornando a variável do projeto
	 */
	public function __construct(
		private mixed $destinatario,
		private string $assunto,
		private string $mensagem,
		private ?string $anexo = null,
		private bool $debug = false ){

        //Definindo o email
		$email 				= new PHPMailer();

		//Configurações
		$email->isSMTP();
		$email->SMTPAuth 	= true;
		$email->CharSet		= 'UTF-8';
		$email->Host 		= Configuracao::get( 'email.servidor' );
		$email->Username 	= Configuracao::get( 'email.endereco' );
		$email->Password 	= Configuracao::get( 'email.senha' );
		$email->SMTPSecure 	= Configuracao::get( 'email.tipo_seguranca' );
		$email->Port 		= Configuracao::get( 'email.porta' );
		$email->Sender		= Configuracao::get( 'email.endereco' );
		$email->Subject 	= $this->assunto;
		$email->Body    	= $this->mensagem;
		$email->isHTML( true );
		$email->setFrom( Configuracao::get( 'email.endereco' ), Configuracao::get( 'email.nome_remetente' ) );

		//Verificando se existe anexo
		if( !is_null( $this->anexo ) )
			//Anexando
			$email->addAttachment( $this->anexo );

		//Verificando se é para debugar ou não
		if( $this->debug )
			//Definindo o modo debug
			$email->SMTPDebug 	= SMTP::DEBUG_SERVER;
		
		//Adicionando apenas um destinatário
		$email->addAddress( $this->destinatario );

		//Enviando o email
		$email->send();

		

	}

}