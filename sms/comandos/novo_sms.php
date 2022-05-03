<?php

$tlg->answerCallbackQuery ([
	'callback_query_id' => $tlg->Callback_ID ()
]);

$id_sms = $complemento;
$id_processo = $complemento;

$info_processo = $processos->getProcesso ($id_processo);

if (minutosPassados ($info_processo ['time_criacao']) > 20){

	$tlg->editMessageText ([
		'chat_id' => $tlg->ChatID (),
		'text' => "<b>Não é possível pedir outro SMS tempo de ativação (20 min.) esgotado, peça outro número com o comando /servicos</b>",
		'parse_mode' => 'html',
		'message_id' => $tlg->MessageID ()
	]);

}else {

	$api_sms->setStatus (3, $id_sms); // pede outro sms no msm número

	// sinaliza que é o segundo sms
	$processos->updateProcesso ($id_processo, 'segundo_sms', true);
	// muda status de visualizado para não visualizado o segundo sms
	$processos->updateProcesso ($id_processo, 'visualizado', false);

	$tlg->editMessageText ([
		'chat_id' => $tlg->ChatID (),
		'text' => "Pais: <b>".PAISES [$user ['pais']]."</b>\nServiço: <b>{$info_processo ['nome_servico']}</b>\nNúmero: <code>+{$info_processo ['numero']}</code>\n\nEspera: 20 minutos\nSTATUS: <em>Aguardando sms...</em>",
		'parse_mode' => 'html',
		'message_id' => $tlg->MessageID (),
		'reply_markup' => $tlg->buildInlineKeyboard ([
			[$tlg->buildInlineKeyBoardButton ('❗', null, "/info")],
			[$tlg->buildInlineKeyBoardButton ('Cancelar SMS', null, "/cancelar {$id_sms}")]
		])
	]);

}