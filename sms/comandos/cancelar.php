<?php

$id_processo = $complemento; // id do processo é o mesmo id do sms

$info_processo = $processos->getProcesso ($id_processo); // info. do processo

if (empty ($info_processo)){ // processo vazio para
	exit;
}

$status_servico = $api_sms->getStatus ($info_processo ['id_sms']); // sobre o serviço do id

if (strpos ('STATUS_OK', $status_servico) !== false || strpos ('STATUS_WAIT_RESEND', $status_servico) !== false){

	$tlg->answerCallbackQuery ([
		'callback_query_id' => $tlg->Callback_ID (),
		'text' => "Serviço {$info_processo ['nome_servico']} cancelado, mas um sms já foi recebido!",
		'show_alert' => 'true'
	]);

}elseif (minutosPassados ($info_processo ['time_criacao']) >= 20){

	$tlg->editMessageText ([
		'chat_id' => $tlg->ChatID (),
		'text' => "Tempo de espera (20 min.) finalizado, não é possivel cancelar o serviço!",
		'parse_mode' => 'html',
		'message_id' => $tlg->MessageID ()
	]);

}else {

	$api_sms->setStatus (8, $info_processo ['id_sms']); // cancela o serviço

	$tlg->editMessageText ([
		'chat_id' => $tlg->ChatID (),
		'text' => "Serviço <b>{$info_processo ['nome_servico']}</b> cancelado, para ativar novos serviços use /servicos",
		'parse_mode' => 'html',
		'message_id' => $tlg->MessageID ()
	]);

        $tlg->sendMessage ([
		'chat_id' => CHAT_ID_NOTIFICACAO,
		'text' => "Alguem cancelou um serviço!\nID usuario: {$tlg->ChatID ()}\nID sms: {$id_processo}\nNome: ".htmlentities ($tlg->FirstName ()).""
	]);

	// guarda cancelamento
	anti_cancelamento ($info_processo ['codigo_servico']);

}

// apaga processo
$processos->deletaProcesso ($id_processo);

// By @itachivendas