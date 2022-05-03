<?php

if (in_array ($tlg->UserID (), ADMS)){

	$id_telegram = $complemento;

	$get_usuario = $tlg->getChat ([
		'chat_id' => $id_telegram
	]);

	if (!$get_usuario ['ok']){

		$tlg->sendMessage ([
			'chat_id' => $tlg->ChatID (),
			'text' => "<b>Saldo API: <code>{$api_sms->getBalance ()}</code></b>",
			'parse_mode' => 'html'
		]);

	}else {

		$usuario = $get_usuario ['result'];
		$usuario_bd = $bd_tlg->getUsuario ($id_telegram);

		$tlg->sendMessage ([
			'chat_id' => $tlg->ChatID (),
			'text' => "Usuário: <a href=\"tg://user?id={$id_telegram}\">{$usuario ['first_name']}</a>\nSaldo <b>R\${$usuario_bd ['saldo']}</b>",
			'parse_mode' => 'html'
		]);

	}

}