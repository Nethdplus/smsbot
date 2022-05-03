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

$mp = new MercadoPago (ACCESS_TOKEN_MERCADO_PAGO);
$tlg = new TelegramTools (TOKEN_BOT);
$bd_tlg = new bdTelegram ();
$redis = rDis::con ();

$ultimos_pagamento = $mp->buscaPagamento ([
	'sort' => 'date_last_updated',
	'criteria' => 'desc',
	'status' => 'approved',
	'limit' => 20
]);

if (empty ($ultimos_pagamento ['results'])) exit;

foreach ($ultimos_pagamento ['results'] as $pagamento){

	if ($pagamento ['status'] != 'approved') continue;

	$id_pagamento = $pagamento ['id'];
	$external_reference = $pagamento ['external_reference'];
	$id_telegram = substr ($external_reference, 0, -6);

	if (!empty ($bd_tlg->getResgate ($external_reference))) continue;

	$usuarioTlg = $tlg->getUsuarioTlg ($id_telegram); // usuario no telegram
	$usuarioBd = $bd_tlg->getUsuario ($id_telegram); // usuario no sistema/bd

	if (empty ($usuarioBd) || empty ($usuarioTlg)) continue;

	$valor_pagamento = $pagamento ['transaction_amount'];
	$valor = incrementoPorcento ($valor_pagamento, BONUS);

	### SISTEMA AFILIADO ###

	// afiliados ativo e verifica se é o primeiro resgate desse usuario e se ele já foi indicado de alguem
	if (STATUS_AFILIADO && $bd_tlg->checkPrimeiroResgate ($id_telegram) && $bd_tlg->checkReferencia ($id_telegram)){

		// pega quem fez a indicação desse usuario
		$afiliado = $bd_tlg->getReferenciaIndicado ($id_telegram);
		$saldo_afiliado = $bd_tlg->getSaldo ($afiliado ['id_telegram']); // pega o saldo atual do afiliado

		if (isset ($afiliado)){

			// o afiliado ganha % do valor recarregado
			$novo_saldo_afiliado = getPorcento ($valor_pagamento, BONUS_AFILIADO);
			$bd_tlg->setSaldo ($afiliado ['id_telegram'], $novo_saldo_afiliado+$saldo_afiliado);

			$tlg->sendMessage ([
				'chat_id' => $afiliado ['id_telegram'],
				'text' => "👏 Parabéns, um dos seus indicados acaba de fazer uma recarga.\n<b>Por indicação você ganhou R\$".number_format ($novo_saldo_afiliado, 2)." (".BONUS_AFILIADO."%) da recarga dele, use /saldo</b>",
				'parse_mode' => 'html'
			]);

		}

	}

	if ($bd_tlg->addResgate ($id_telegram, $external_reference, $valor)){

		echo "Pagamento: {$usuarioTlg ['first_name']} ({$id_telegram}) - Valor: {$valor}\n";

		$tlg->sendMessage ([
			'chat_id' => $id_telegram,
			'text' => "<b>Pronto, saldo de R\${$valor} adicionado na sua conta</b>",
			'parse_mode' => 'html'
		]);

		 $tlg->sendMessage ([
			'chat_id' => CHAT_ID_NOTIFICACAO,
			'text' => "<b>Saldo Resgatado por {$usuarioTlg ['first_name']}!</b>\nID: {$id_telegram}\n\n Valor: R\${$valor}\n\n@SMSCUBOSNET_BOT",
			'parse_mode' => 'html'
		]);

		$bd_tlg->setSaldo ($id_telegram, $valor+$usuarioBd ['saldo']);

	}

}