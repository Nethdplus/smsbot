<?php

if (in_array ($tlg->UserID (), ADMS)){

	if (empty ($complemento)){

		$tlg->sendMessage ([
			'chat_id' => $tlg->ChatID (),
			'text' => "Erro informe o id do usuário Ex:<code>/removerblock {$tlg->UserID ()}</code>",
			'parse_mode' => 'html'
		]);

	}else {

		$id_telegram = $complemento;
		$usuario = $tlg->getUsuarioTlg ($id_telegram);

		if (empty ($usuario)){

			$tlg->sendMessage ([
				'chat_id' => $tlg->ChatID (),
				'text' => "Usuário não encontrado!",
				'parse_mode' => 'html'
			]);

		}else {

			// remove usuario da lista de block
			$redis->del ("block-abuso:{$tlg->UserID ()}");

			$tlg->sendMessage ([
				'chat_id' => $tlg->ChatID (),
				'text' => "Usuário <a href=\"tg://user?id={$usuario ['id']}\">{$usuario ['first_name']}</a> desbloqueado!",
				"parse_mode" => 'html'
			]);

		}

	}

}