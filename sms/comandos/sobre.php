<?php

$tlg->sendMessage ([
	'chat_id' => $tlg->ChatID (),
	'text' => "🎈<b>Como receber sms pelo bot?</b>\nUse o comando /servicos e veja os serviços disponíveis para receber sms.\n\n🎈<b>Como colocar saldo na minha conta?</b>\nUse o comando /recarregar para ver o link de pagamento ou entre em contato com o dono do bot com /info\n\n🎈 <b>Posso ficar com o número após o uso?</b>\nNão, os números são descartaveis/temporarios, após o uso eles são substituidos por novos, servindo somente para confirmação ou criação de cadastros.\n\n🎈<b>Como resgatar meu saldo comprado?</b>\nUse o comando <code>/resgatar [id pagamento]</code> para resgatar o saldo, ele será adicionado na sua conta.\n\nNosso canal de referências @cubosnet💬",
	'parse_mode' => 'html'
]);
