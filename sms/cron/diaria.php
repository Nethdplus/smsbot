<?php

include __DIR__.'/../includes/includes.php';

$tlg = new Telegram (TOKEN_BOT);
$bd_tlg = new bdTelegram (__DIR__.'/../recebersmsbot.db');

foreach ($bd_tlg->todosUsuarios () as $usuario){

	$msg = @$tlg->sendMessage ([
		'chat_id' => $usuario ['id_telegram'],
		'text' => "<b>🤓 RECEBA SMS COM NÚMEROS NOVOS PARA CRIAR CONTAS</b>

- Telegram
- Whatsapp
- 99app
- Banqi
- Uber
- TIKTOK
- KWAI
- IFOOD 
- E muitos outros...

💬 Receba os códigos no nosso bot
@MandrackBOT

📍 Nosso grupo
@MandrackSMSChecker

*Preço e serviço incomparável com os existentes.
*Mais de 4 mil números disponíveis",
'parse_mode' => 'html'
	]);

	*$msg = $tlg->forwardMessage ([
	 	'chat_id' => $usuario ['id_telegram'],
	 	'from_chat_id' => '-100143886425',
	 	'text' => "<b>🤓 RECEBA SMS COM NÚMEROS NOVOS PARA CRIAR CONTAS</b>

- Telegram
- Whatsapp
- 99app
- Banqi
- Uber
- E muitos outros...

💬 Receba os códigos no nosso bot
@MandrackBOT

📍 Nosso grupo
@MandrackSMSChecker

*Preço e serviço incomparável com os existentes.
*Mais de 4 mil números disponíveis",
'parse_mode' => 'html'
	 ]); */

	 $msg = @$tlg->sendMessage ([
	 	'chat_id' => $usuario ['id_telegram'],
	 	'text' => "✨ Use o comando /totaladicionados para saber a quantidade de usuários que você adicionou no nosso grupo @chatrecebersms\n\n<u>Adicionando ".MINIMO_ADICAO." usuários você ganha R\$".number_format (BONUS_ADICAO, 2)." de saldo no bot</u>",
	 	'parse_mode' => 'html'
	 ]);

	if ($msg ['ok']){

		$nome = $msg ['result']['chat']['first_name'] ?? $usuario ['id'];
		echo "{$nome} enviada\n";

	}

}
