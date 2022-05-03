<?php

$servicos = json_decode ($api_sms->getPrices ($user ['pais']), true);
$lista_servicos = $servicos [$user ['pais']];
$dados_servico = json_decode (file_get_contents ('estaticos/nome_servicos.json'), true); // info

// cria visualização para serviços personalizados dentro do serviço OUTROS
$lista_servicos = servicosPersonalizados ($lista_servicos, $dados_servico);

@natsort ($dados_servico);

if (empty ($lista_servicos)){

	$tlg->sendMessage ([
		'chat_id' => $tlg->ChatID (),
		'text' => "Não foi encontrado nenhum serviço para esse pais, use /paises para trocar",
		'parse_mode' => 'html'
	]);

}else {

	foreach ($dados_servico as $id_servico => $nome_servico){

		if (!isset ($lista_servicos [$id_servico]['cost'])){
			continue;
		}

		// troca id dos serviços personalizados pelo id do serviço Outros
		if (strpos ($id_servico, 'pers_') !== false){
			$id_servico = 'ot';
		}

		$valor_real = $lista_servicos [$id_servico]['cost'];
		$quantidade = $lista_servicos [$id_servico]['count'] ?? 0;
		$valor_sms = valorSMS ($valor_real, PORCENTAGEM_LUCRO); // quanto vai custar esse sms, já convertido = valorSMS ($valor_real, PORCENTAGEM_LUCRO); // quanto vai custar esse sms, já convertido

		$nome = "{$nome_servico ['nome']} [{$quantidade}] - R$ {$valor_sms}";
		$comando = "/sms {$id_servico}";

		$botoes [$i][] = $tlg->buildInlineKeyBoardButton ($nome, null, $comando);

		if (count ($botoes [$i]) == 1){
			$i++;
		}

	}

	if ($tlg->Callback_ID () != null){

		$tlg->answerCallbackQuery ([
			'callback_query_id' => $tlg->Callback_ID ()
		]);

		$tlg->editMessageText ([
			'chat_id' => $tlg->ChatID (),
			'text' => "País atual <u><b>".PAISES [$user ['pais']]."</b></u> mude com /paises\n\n<b>Escolha para qual serviço você deseja receber SMS:</b>",
			'reply_markup' => $tlg->buildInlineKeyBoard ($botoes),
			'message_id' => $tlg->MessageID (),
			'parse_mode' => 'html'
		]);

	}else {

		$tlg->sendMessage ([
			'chat_id' => $tlg->ChatID (),
			'text' => "País atual <u><b>".PAISES [$user ['pais']]."</b></u> mude com /paises\n\n<b>Escolha para qual serviço você deseja receber SMS:</b>",
			'reply_markup' => $tlg->buildInlineKeyBoard ($botoes),
			'parse_mode' => 'html'
		]);

	}

}
