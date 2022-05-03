<?php

include __DIR__.'/../includes/dados_bot.php';
include __DIR__.'/../includes/rDis.php';
include __DIR__.'/../includes/ApiSMS.php';
include __DIR__.'/../includes/SMSActivate.php';
include __DIR__.'/../includes/MercadoPago.php';
include __DIR__.'/../includes/Telegram.php';
include __DIR__.'/../includes/TelegramTools.php';
include __DIR__.'/../includes/bdTelegram.php';
include __DIR__.'/../includes/funcoes.php';
include __DIR__.'/../includes/ControleProcessos.php';

$api_sms = new SMSActivate (KEY_SMS);
$tlg = new TelegramTools (TOKEN_BOT);
$bd_tlg = new bdTelegram ();
$processos = new ControleProcessos ();

$pendentes = json_decode ($api_sms->getCurrentActivationsDataTables (), true);

if (@$pendentes ['status'] == 'fail' || empty ($pendentes ['array'])){
	echo "\rAguardando processos para executar...\r";
	exit;
}

foreach (@$pendentes ['array'] as $pendente){

	$info_processo = $processos->getProcesso ($pendente ['id']);

	if (empty ($info_processo)){
		continue;
	}

	// criador do problema do sms
	$info_user = $bd_tlg->usuario ($info_processo ['id_usuario']);

	$id_processo = $pendente ['id'];
	$id_telegram = $info_processo ['id_usuario'];
	$message_id = $info_processo ['message_id'];
	$id_sms = $info_processo ['id_sms'];
	$nome_servico = $info_processo ['nome_servico'];
	$numero = $pendente ['phone'];
	$valor = $info_processo ['valor'];
	$sms = strip_tags ($pendente ['smsText'] ?? 'Aguardando sms...');
	$segundo_sms = ($info_processo ['segundo_sms']) ? 'sim' : 'não';

	show_logs_processos ();

	// sms NÃO recebido ainda
	if (empty ($pendente ['smsText']) && !$info_processo ['visualizado']){ // não recebel nenhum sms e tambem não foi visualizado

		// saldo do usuario foi zerado, ele não tem como pagar por esse sms
		if ($info_user ['saldo'] < $valor) {

			$tlg->editMessageText ([
				'chat_id' => $id_telegram,
				'text' => "Você não tem créditos suficiente para pagar por esse serviço, recarregue sua conta!!",
				'message_id' => $message_id,
				'parse_mode' => 'html'
			]);

			$api_sms->setStatus (8, $id_processo); // cancela o serviço
			$processos->deletaProcesso ($id_processo); // apaga processo

			exit;

		}

		if (minutosPassados ($info_processo ['time_criacao']) >= 18 && empty ($pendente ['smsText'])){ // PASSOU DE 19 MIN.
			// Usa como referencia o tempo guardado no processo, prq as inf. do sms somen após vencer o tempo

			// atualiza msg do usuário
			$tlg->editMessageText ([
				'chat_id' => $id_telegram,
				'text' => "Tempo limite (20 min.) do serviço <b>{$nome_servico}</b> atingido!",
				'message_id' => $message_id,
				'parse_mode' => 'html'
			]);

			$processos->deletaProcesso ($id_processo); // remove da lista de processos

		}

	}else {

		// atualiza msg do usuário
		$tlg->editMessageText ([
			'chat_id' => $id_telegram,
			'text' => "Pais: <b>".PAISES [$info_user ['pais']]."</b>\nServiço: <b>{$nome_servico}</b>\nNúmero: <code>+{$numero}</code>\n\n<b>🔹SMS:</b> <code>{$sms}</code>",
			'message_id' => $message_id,
			'parse_mode' => 'html',
			'reply_markup' => $tlg->buildInlineKeyboard ([
				[$tlg->buildInlineKeyBoardButton ('Novo SMS', null, "/novo_sms {$id_sms}")]
			])
		]);

		if (isset ($info_processo ['descontado']) && !$info_processo ['descontado']){

			$saldo_atual = $info_user ['saldo']; // saldo atual do usuatio
			$novo_saldo = ($saldo_atual <= $valor) ? 0 : ($saldo_atual-$valor); // novo saldo

			$bd_tlg->setSaldo ($id_telegram, $novo_saldo); // desconta o valor do sms recebido
			$processos->updateProcesso ($id_processo, 'descontado', true); // informa que já foi descontado

			// zera contador de cancelamentos do usuario para esse serviço e esse país
			rDis::con ()->del ("contador-cancelamento:{$info_processo ['codigo_servico']}:{$id_telegram}:{$info_user ['pais']}");

		}

		$processos->updateProcesso ($id_processo, 'visualizado', true); // status para visualizado

	}

	sleep(1);

}