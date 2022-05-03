<?php

$tlg->sendMessage ([
	'chat_id' => $tlg->ChatID (),
	'text' => "😀 <b>Olá ".htmlentities ($tlg->FirstName ())."</b>, Aqui Você Poderá Gerar o Seu Número Temporario Para Receber SMS, Use os Comandos Abaixo:\n\n/servicos - <u>Serviços Disponíveis</u>\n\n/saldo - <b>Seu Saldo Disponível</b>\n\n/alertas - <b>Alerta quando SMS estiver disponível</b>\n\n/sobre - <b>Mais Informações</b>\n\n/recarregar - <b>Adicionar Saldo Na Conta</b>\n\n/paises - <b>Pais dos Números</b>\n\n/Afiliados - <b>Manutenção</b>\n\n/codigo - <b>Código Fonte do Bot \n\n Dono: @cubosnet</b>",
	'parse_mode' => 'html',
	'reply_markup' => $tlg->buildKeyBoard ([
		[$tlg->buildInlineKeyboardButton ('🔥 Comprar'), $tlg->buildInlineKeyboardButton ('👥 Informações')],
		[$tlg->buildInlineKeyboardButton ('👤 Meu Saldo'), $tlg->buildInlineKeyboardButton ('💴 Depositar')]
	], true, true)
]);

// afiliados
if (isset ($complemento) && is_numeric ($complemento) && STATUS_AFILIADO){

	$ref = $tlg->getUsuarioTlg ($complemento);

	// se usuario existir e não tiver entrado no bot por indicação de alguem e tambem não pode ser ele mesmo
	if (isset ($ref ['id']) && $bd_tlg->checkReferencia ($tlg->UserID ()) == false && $complemento != $tlg->UserID ()){

		// salva usuario atual como referencia do dono do link
		$bd_tlg->setReferencia ($complemento, $tlg->UserID ());

	}

}
