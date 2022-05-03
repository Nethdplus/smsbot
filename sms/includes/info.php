<?php

if ($tlg->Callback_ID () !== null){

	// encerra carregamento do botão de callback e mostra inf. (limite de 200 caracteres)
	$tlg->answerCallbackQuery ([
		'callback_query_id' => $tlg->Callback_ID (),
		'text' => "Após os 20 minutos caso não receba nenhum SMS o serviço será cancelado.\n\nVocê também pode cancelar a qualquer momento antes de receber o SMS, mas evite abusos",
		'show_alert' => 'true'
	]);

}else {

	$tlg->sendMessage ([
		'chat_id' => $tlg->ChatID (),
		'text' => "💬 Entre em contato com o dono do bot para mais informações @cubosnet",
		'parse_mode' => 'html'
	]);

}
