<?php

$tlg->answerCallbackQuery ([
	'callback_query_id' => $tlg->Callback_ID (),
	'text' => 'Gerando link de pagamento...'
]);

$valor = number_format ($complemento, 2);
$hash = $tlg->UserID ().mt_rand(111111, 999999);

$mp = new MercadoPago (ACCESS_TOKEN_MERCADO_PAGO);
$pagamento = $mp->setPreferencia ([
	"items" => [
		[
			"picture_url" => "https://i.imgur.com/tsTPWau.jpg",
			"title" => "Saldo @SMSCUBOSNET_BOT",
            "description" => "Saldo para o bot @SMSCUBOSNET_BOT no Telegram",
            "quantity" => 1,
            "currency_id" => "BRL",
            "unit_price" => (float)$valor
		]
	],
	"external_reference" => $hash,
	"expires" => true,
	"expiration_date_to" => date ('c', strtotime('+1 day'))
]);

if (!isset ($pagamento ['id'])){

	$tlg->editMessageText ([
		'chat_id' => $tlg->ChatID (),
		'text' => "<em>⚠️ Erro ao gerar o seu link de pagamento, por favor tente novamente!</em>",
		'parse_mode' => 'html',
		'message_id' => $tlg->MessageID (),
		'reply_markup' => $tlg->buildInlineKeyboard ([
			[
				$tlg->buildInlineKeyBoardButton ("Tentar Novamente", null, "/comprar {$valor}")
			]
		])
	]);

}else {

	$tlg->editMessageText ([
		'chat_id' => $tlg->ChatID (),
		'text' => "💡 Pague por <em>pix, boleto, saldo ou cartão.</em>\n\n<u>Após o pagamento o saldo será adicionado na sua conta automaticamente.</u>",
		'parse_mode' => 'html',
		'message_id' => $tlg->MessageID (),
		'reply_markup' => $tlg->buildInlineKeyboard ([
			[
				$tlg->buildInlineKeyBoardButton ("Pagar R\${$valor}", $pagamento ['init_point'])
			]
		])
	]);

}