<?php

if (in_array ($tlg->UserID (), ADMS)){

	$id_telegram = $complemento;

	if (!is_numeric ($id_telegram)){

		$tlg->sendMessage ([
			'chat_id' => $tlg->ChatID (),
			'text' => "Erro, informe o id do usuário Ex: <code>/addsaldo 275123569</code>",
			'parse_mode' => 'html'
		]);

	}else {

		$get_usuario = $tlg->getChat ([
			'chat_id' => $id_telegram
		]);

		$usuario = @$get_usuario ['result'];

		$tlg->sendMessage ([
			'chat_id' => $tlg->ChatID (),
			'text' => "Todo o saldo do usuário <a href=\"tg://user?id={$usuario ['id']}\">{$usuario ['first_name']}</a> foi zerado.",
			'parse_mode' => 'html'
		]);

		$bd_tlg->removeSaldo ($id_telegram); //addremove todo saldo de um usuário

	}

}