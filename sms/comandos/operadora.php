<?php

// encerra carregamento do botão de callback
$tlg->answerCallbackQuery ([
	'callback_query_id' => $tlg->Callback_ID ()
]);

$codigo_servico = $complemento;

$operadoras = json_decode ($api_sms->getOperators ($user ['pais'] ?? 73), true);
$i = 1;

// asort($operadoras ['operators']);

// define botão outros
$botoes [][] = $tlg->buildInlineKeyBoardButton ('QUALQUER', null, "/confirmar {$codigo_servico} any");

foreach ($operadoras ['operators'] as $key => $nome){

	if ($key == 'any'){ // pula outros
		continue;
	}

	$botoes [$i][] = $tlg->buildInlineKeyBoardButton (strtoupper($nome), null, "/confirmar {$codigo_servico} {$key}");

	if (count ($botoes [$i]) == 2){
		$i++;
	}

}

$botoes [][] = $tlg->buildInlineKeyBoardButton ('🔙', null, "/sms {$codigo_servico}");

$tlg->editMessageText ([
	'chat_id' => $tlg->ChatID (),
	'text' => "<b>🚩 Selecione a operadora do número:</b>",
	'message_id' => $tlg->MessageID (),
	'parse_mode' => 'html',
	'disable_web_page_preview' => 'true',
	'reply_markup' => $tlg->buildInlineKeyboard ($botoes)
]);