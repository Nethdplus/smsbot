<?php

class TelegramTools extends Telegram {

	public function __construct ($token)
	{
		parent::__construct ($token);
	}

	public function msg ($data)
	{

		$envia = $this->editMessageText ($data);

		if (!$envia ['ok']){ // erro envia modo normal

			// remove campos desnescessarios
			unset ($data ['message_id']);

			$this->sendMessage ($data);

		}

	}

	public function getUsuarioTlg ($id_telegram)
	{

		return @$this->getChat (['chat_id' => $id_telegram])['result'];

	}

	public function isCommand (){

		return (bool)preg_match('/(^\/[a-zA-Z0-9]+)/', $this->Text ());

	}

}