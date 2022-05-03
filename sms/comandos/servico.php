<?php

// encerra carregamento do botão de callback
$tlg->answerCallbackQuery ([
	'callback_query_id' => $tlg->Callback_ID ()
]);

if (isset ($complemento)){

	$codigo_servico = $complemento;

	$nome_servicos = json_decode (file_get_contents ('estaticos/nome_servicos.json'), true);
	$dados_servico = json_decode ($api_sms->getPrices ($user ['pais'], $codigo_servico), true)[$user ['pais']];

	// dados do serviço solicitado
	$nome_real = @ucfirst ($nome_servicos [$codigo_servico]['nome']);
	$valor_real = @$dados_servico [$codigo_servico]['cost'];
	$quantidade_real = @$dados_servico [$codigo_servico]['count'];

	$valor_sms = valorSMS ($valor_real, PORCENTAGEM_LUCRO); // quanto vai custar esse sms, já convertido

	if ($user ['saldo'] <= 0){
		$botoes [][] = $tlg->buildInlineKeyBoardButton ('Recarregar Conta', null, "/recarregar");
	}

	$botoes [][] = $tlg->buildInlineKeyBoardButton ('Receber SMS', null, "/confirmar {$codigo_servico}");
	$botoes [][] = $tlg->buildInlineKeyBoardButton ('Operadoras', null, "/operadora {$codigo_servico}");
	$botoes [][] = $tlg->buildInlineKeyBoardButton ('🔙', null, "/servicos");

	$notas = [
		"<em><b>Nota:</b> Novos números são adicionados durante o dia.</em>",
		"<em><b>Nota:</b> Aproveite, o reenvio de sms no mesmo número é grátis.</em>",
		"<em><b>Nota:</b> Gostou do bot? Indique aos seus amigos, agradecemos.</em>",
		"<em><b>Nota:</b> Somos inegavelmente o melhor bot de sms do Telegram!</em>",
		"<em><b>Nota:</b> Entre no nosso canal @TEDDYNet24, lá enviamos códigos de resgate.</em>",
		"<em><b>Nota:</b> Algum problema? Contate @teddynet23 para suporte!</em>",
		"<em><b>Nota:</b> <a href=\"https://www.youtube.com/watch?v=gRq-UL4E48M\">Veja como usar o bot</a></em>",
		"<em><b>Nota:</b> Os valores variam de acordo com o país, use /paises</em>",
		"<em><b>Nota:</b> Evite abusos você pode ser penalizado com desconto no saldo e block :)</em>",
		"<em><b>Nota:</b> Quando não tiver números disponíveis use o comando /alertas</em>",
	];

	$tlg->editMessageText ([
		'chat_id' => $tlg->ChatID (),
		'text' => "Pais: <b>".PAISES [$user ['pais']]."</b>\nServiço: <b>$nome_real</b>\nValor: R$ {$valor_sms}\n\n🔹 <b>{$quantidade_real}</b> números disponíveis!\n\n{$notas [mt_rand (0, (count ($notas)-1))]}",
		'message_id' => $tlg->MessageID (),
		'parse_mode' => 'html',
		'disable_web_page_preview' => 'true',
		'reply_markup' => $tlg->buildInlineKeyboard ($botoes)
	]);

}
