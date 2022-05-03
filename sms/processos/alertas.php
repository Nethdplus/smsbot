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
$redis = rDis::con ();

foreach (PAISES as $key => $pais){ // passa pelos paises salvos no bot

	// pega os alertas salvos para usuarios que usam esse pais
	$alertas_pais = $bd_tlg->getAlertasPaises ($key);

	if (empty ($alertas_pais)){ // não tem alertas para usuarios com esse pais então volta para o loop
		continue;
	}

	// lista de serviços e a quantidade de números
	$servicos = json_decode ($api_sms->getPrices ($key), true);
	$lista_servicos = @$servicos [$key];

	if (empty ($lista_servicos)){
		continue;
	}

	foreach ($alertas_pais as $alerta){ // passa pelos alertas salvos no bd

		if (!isset ($lista_servicos [$alerta ['id_servico']])){
			continue;
		}

		$servico_alerta = $lista_servicos [$alerta ['id_servico']]; // dados so serviço, valor em rublo e quantidade de números
		$id_usuario_alerta = $alerta ['id_telegram']; // usuario que vai receber o alerta
		$key_cache_redis = cache-alerta:{$id_usuario_alerta}:{$alerta ['id_servico']}; // identificador cache do redis

		if ($servico_alerta ['count'] > 50 && (date ('H') > 05)){

			$info_servico = json_decode (file_get_contents ('estaticos/nome_servicos.json'), true); // pega inf, do serviço como nome
			$nome_servico = $info_servico [$alerta ['id_servico']]['nome'];

			if (!$redis->exists ($key_cache_redis)){ // cache de um hora não existe então manda alerta

				$tlg->sendMessage ([
					chat_id => $id_usuario_alerta,
					text => "🔔 Novos números disponíveis para o serviço <b>{$nome_servico}</b> use /servicos\n\n<b>Nota:</b> <em>Você pode desativar as notificaçẽs com o botão abaixo.</em>",
					parse_mode => html,
					reply_markup => $tlg->buildInlineKeyboard ([
						[$tlg->buildInlineKeyBoardButton ('Desativar esse Alerta', null, "/alertas {$alerta ['id_servico']} 0")]
					])
				]);

				$redis->setEx ($key_cache_redis, 3600, true); // cria cache para informar que já foi enviado

				// logs do sistema
				show_logs_alertas ();

			}

		}

	}

}