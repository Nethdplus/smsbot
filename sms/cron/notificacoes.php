<?php

include __DIR__.'/../includes/includes.php';

$tlg = new Telegram (TOKEN_BOT);
$bd_tlg = new bdTelegram (__DIR__.'/../recebersmsbot.db');

foreach ($bd_tlg->todosUsuarios () as $usuario){

/*	$msg = @$tlg->sendMessage ([
		'chat_id' => $usuario ['id_telegram'],
		'text' => "🚀 <b>Gere Números Para Receber SMS no Seu Serviço Preferido, TikTok, Kwai, PicPay, Whatsapp, Telegram, BanQi...</b>\n\n💠 É Facíl, Apenas Recarregue Sua Conta Com o Comando /recarregar e Use o Saldo Para Comprar Números, Não Se Preocupe Você Só Paga Depois Que Recebe o Sms!\n\n🎃 Nosso Grupo De Consultas Gratis. https://t.me/MandrackSMSChecker",
		'parse_mode' => 'html'
	]);*/

	$msg = $tlg->sendMessage ([
	 	'chat_id' => $usuario ['id_telegram'],
	 	'from_chat_id' => CHAT_ID_NOTIFICACAO,
	 	'text' => "🚀 <b>Gere Números Para Receber SMS no Seu Serviço Preferido, TikTok, Kwai, PicPay, Whatsapp, Telegram, BanQi...</b>\n\n💠 É Facíl, Apenas Recarregue Sua Conta Com o Comando /recarregar e Use o Saldo Para Comprar Números, Não Se Preocupe Você Só Paga Depois Que Recebe o Sms!\n\n🎃 Nosso Grupo De Consultas Gratis. https://t.me/cubosnet",
		'parse_mode' => 'html'
	 ]); 

	/* $msg = @$tlg->sendMessage ([
	 	'chat_id' => $usuario ['id_telegram'],
	 	'text' => "✨ Use o comando /totaladicionados para saber a quantidade de usuários que você adicionou no nosso grupo @chatrecebersms\n\n<u>Adicionando ".MINIMO_ADICAO." usuários você ganha R\$".number_format (BONUS_ADICAO, 2)." de saldo no bot</u>",
	 	'parse_mode' => 'html'
	 ]);*/

	if ($msg ['ok']){

		$nome = $msg ['result']['chat']['first_name'] ?? $usuario ['id'];
		echo "{$nome} enviada\n";

	}

}
