<?php

$tlg->sendMessage ([
	'chat_id' => $tlg->ChatID (),
	'text' => "<b>O código fonte desse bot está a venda, contate @cubosnet e tenha o seu proprio bot de sms.</b>\n\n<b>#resquisitos:</b> <code>php >=7.4, redis, sqlite3, Linux (ubuntu >=18)</code>\n\n VALOR: $300",
	'parse_mode' => 'html'
]);
