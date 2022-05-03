<?php

$total = (int)$redis->get ("ref:{$tlg->UserID ()}");

if (STATUS_BONUS_ADICAO){

	$tlg->sendMessage ([
		'chat_id' => $tlg->ChatID (),
		'text' => "⚡️ Você adicionou <b>{$total}</b> usuários no grupo @SMSCUBOSNET_BOT até agora.\n\n<u>Adicionando ".MINIMO_ADICAO." usuários você ganha R\$".number_format (BONUS_ADICAO, 2)." de saldo no bot</u>",
		'parse_mode' => 'html'
	]);

}else {

	$tlg->sendMessage ([
		'chat_id' => $tlg->ChatID (),
		'text' => "<b>Sistema de bônus por adição desativado por enquanto</b>",
		'parse_mode' => 'html'
	]);

}