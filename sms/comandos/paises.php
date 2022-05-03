<?php

if ($tlg->Callback_ID () !== null){ // salva novo pais

	// encerra carregamento do botão de callback
	$tlg->answerCallbackQuery ([
		'callback_query_id' => $tlg->Callback_ID ()
	]);

	$id_pais = (PAISES [$complemento]) ? $complemento : 73;
	$pais = PAISES [$id_pais];

	$resp = [
		'chat_id' => $tlg->ChatID (),
		'text' => "Seu novo pais <b>{$pais}</b> foi salvo",
		'parse_mode' => 'html',
		'message_id' => $tlg->MessageID ()
	];

	$bd_tlg->setPais ($tlg->UserID (), $id_pais);

}else { // mostra lista de paises

	$paises = PAISES;

	ordenaPaises ($paises);

	foreach ($paises as $key => $pais){

		$btn [$i][] = $tlg->buildInlineKeyBoardButton ($pais, null, "/paises {$key}");

		if (count ($btn [$i]) == 2){
			$i++;
		}

	}

	$resp = [
		'chat_id' => $tlg->ChatID (),
		'text' => "<b>País atual <b>".PAISES [$user ['pais']]."</b>, escolha:</b>",
		'parse_mode' => 'html',
		'reply_markup' => $tlg->buildInlineKeyboard ($btn)
	];

}

$tlg->msg ($resp);