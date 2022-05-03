<?php

@list ($id_servico, $status) = @explode (' ', $complemento);

$servicos = json_decode ($api_sms->getPrices ($user ['pais']), true);
$lista_servicos = $servicos [$user ['pais']];
$dados_servico = json_decode (file_get_contents ('estaticos/nome_servicos.json'), true);

// salva status do serviço se ele existir antes de continuar
if (!empty ($id_servico) && isset ($status)){

	if ($status == 0){ // deleta servico da lista de alertas

		$bd_tlg->deleteAlerta ($tlg->UserID (), $id_servico);

		$tlg->answerCallbackQuery ([
			'callback_query_id' => $tlg->Callback_ID (),
			'text' => "Desativado alertas para {$dados_servico [$id_servico]['nome']}"
		]);

	}else {

		$bd_tlg->setAlerta ($tlg->UserID (), $id_servico);

		$tlg->answerCallbackQuery ([
			'callback_query_id' => $tlg->Callback_ID (),
			'text' => "Alerta de novos números para {$dados_servico [$id_servico]['nome']}"
		]);

	}

}

if (empty ($lista_servicos)){

	$tlg->sendMessage ([
		'chat_id' => $tlg->ChatID (),
		'text' => "Não foi encontrado nenhum serviço para esse pais, use /paises para trocar",
		'parse_mode' => 'html'
	]);

}else {

	foreach ($lista_servicos as $servico => $dados){

		if (empty ($bd_tlg->getAlerta ($tlg->UserID (), $servico))){ // não está recebendo alertas desse serviço ainda

			if ($dados ['count'] > 50){
				continue;
			}

			$nome = "{$dados_servico [$servico]['nome']}";
			$comando = "/alertas {$servico} 1";

		}else{ // já está recerebendo alertas desse serviço

			$nome = "🔔 - {$dados_servico [$servico]['nome']}";
			$comando = "/alertas {$servico} 0";

		}

		$botoes [$i][] = $tlg->buildInlineKeyBoardButton ($nome, null, $comando);

		if (count ($botoes [$i]) == 2){
			$i++;
		}

	}

	$tlg->msg ([
		'chat_id' => $tlg->ChatID (),
		'text' => '<b>♻️ Selecione um serviço, o bot ira te alertar quando tiver novos números:</b>',
		'parse_mode' => 'html',
		'message_id' => $tlg->MessageID (),
		'reply_markup' => $tlg->buildInlineKeyboard ($botoes)
	]);

}