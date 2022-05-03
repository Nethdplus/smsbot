<?php

if (in_array ($tlg->UserID (), ADMS)){

	$valor = $complemento;

	if (!is_numeric ($valor)){

		$tlg->sendMessage ([
			'chat_id' => $tlg->ChatID (),
			'text' => "Siga o exemplo <code>/addresgate 10</code>",
			'parse_mode' => 'html'
		]);

	}else {

		$codigo = 'rsg-'.gerarHash (9);

		$bd_tlg->addCodigoResgate ($codigo, $valor);

		$tlg->sendMessage ([
			'chat_id' => $tlg->ChatID (),
			'text' => "CÓDIGO CRIADO: <code>{$codigo}</code>\nVALOR: <b>R\$ {$valor}</b> 💰\n\nUSE: <code>/resgatar {$codigo}</code>",
			'parse_mode' => 'html'
		]);

	}

}