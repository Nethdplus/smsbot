<?php
#BY BlackMetodos
if (in_array ($tlg->UserID (), ADMS)){
	if (!$complemento){
		$tlg->sendMessage ([
			'chat_id' => $tlg->ChatID (),
			'text' => "Digite o comando /enviar TEXTO\nExemplo: <b>/enviar Bom dia!!!</b> ",
			'parse_mode' => 'html'
		]);
	}else {
			$tlg->sendMessage ([
				'chat_id' => $tlg->ChatID (),
				'text' => "<b>Por favor, Aguarde que eu estou entregrando o seu recado!!!...</b>",
				'parse_mode' => 'html'
			]);
		foreach ($bd_tlg->todosUsuarios () as $usuario){
			$msg = @$tlg->sendMessage ([
				'chat_id' => $usuario ['id_telegram'],
				'text' => $complemento,
				'parse_mode' => 'html'
			]);
			if ($msg ['ok']){

				$nome = $msg ['result']['chat']['first_name'] ?? $usuario ['id'];
				echo "{$nome} enviada\n";
			}
		}
	}
}
