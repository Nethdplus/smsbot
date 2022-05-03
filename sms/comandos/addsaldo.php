<?php

if (in_array ($tlg->UserID (), ADMS)){

	@list ($id_telegram, $saldo) = @explode (' ', $complemento);

	if (!is_numeric ($id_telegram) || !is_numeric ($saldo)){

		$tlg->sendMessage ([
			'chat_id' => $tlg->ChatID (),
			'text' => "Erro, envie um id de usúario com o saldo Ex: <code>/addsaldo 275123569 10</code>",
			'parse_mode' => 'html'
		]);

	}else {

		$get_usuario = $tlg->getChat ([
			'chat_id' => $id_telegram
		]);

		$usuario = @$get_usuario ['result'];

		$tlg->sendMessage ([
			'chat_id' => $tlg->ChatID (),
			'text' => "Novo saldo ({$saldo}) para <a href=\"tg://user?id={$usuario ['id']}\">{$usuario ['first_name']}</a>",
			'parse_mode' => 'html'
		]);

		$saldo_usuario = $bd_tlg->getSaldo ($id_telegram);

		$bd_tlg->setSaldo ($id_telegram, $saldo+$saldo_usuario); //add saldo novo para o usuário

	}

}