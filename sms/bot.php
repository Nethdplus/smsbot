<?php

/*
*
** Por @httd1<t.me/httd1>
*
*/

include __DIR__.'/includes/includes.php';

$tlg = new TelegramTools (TOKEN_BOT);

$updates = $tlg->getUpdates ();

for ($i = 0; $i < $tlg->UpdateCount (); $i++):

	$tlg->serveUpdate ($i);

	$bd_tlg = new bdTelegram ();
	$redis = rDis::con ();
	$processos = new ControleProcessos ();
	$api_sms = new SMSActivate (KEY_SMS);

	if (MODO_DESENVOLVEDOR && !in_array ($tlg->UserID (), ADMS)){
		exit;
	}

	// controla quantidade de usuarios adicionados no grupo e distribui saldos
	check_member ();
	
	// limpa usuarios novos e saidos do grupo
	limpa_novos ();

	// anti flood
	anti_flood ();

	// verifica se ta em grupo/canais ou pv
	if (!__is ('private')){
		continue;
	}

	show_logs ($updates); // mostra os logs de uso do bot

	// adiciona o usuário no bd ou pega inf. dele
	$user = $bd_tlg->usuario ($tlg->UserID ());

	// separa comando de complemento se nescessario
	@list ($comando, $complemento) = ($tlg->isCommand ()) ?
	@explode (' ', $tlg->Text (), 2) : [$tlg->Text (), null];

	switch ($comando):

		case '/start':
			include 'comandos/start.php';
		break;

		case '/services':
		case '/servicos':
		case '🔥 Comprar':
			include 'comandos/servicos.php';
		break;

		case '/paises':
		case '/countries':
			include 'comandos/paises.php';
		break;

		case '/pais':
			include 'comandos/pais.php';
		break;

		case '/sms':
			include 'comandos/servico.php';
		break;

		case '/balance':
		case '/saldo':
		case 'Saldo':
		case 'saldo':
		case '👤 Meu Saldo':
			include 'comandos/saldo.php';
		break;

		case '/recarregar':
		case 'Recarga':
		case 'Recarregar':
		case '💴 Depositar':
			include 'comandos/recarregar.php';
		break;

		case '/resgatar':
			include 'comandos/resgatar.php';
		break;

		case '/confirmar':
			include 'comandos/confirmar.php';
		break;

		case '/cancelar':
			include 'comandos/cancelar.php';
		break;

		case '/operadora':
			include 'comandos/operadora.php';
		break;

		case '/novo_sms':
			include 'comandos/novo_sms.php';
		break;

		case '/info':
			include 'comandos/info.php';
		break;

  case '/enviar':
			include 'comandos/enviar.php';
	 break;

		case '/sobre':
		case '👥 Informações':
			include 'comandos/sobre.php';
		break;

		case '/codigo':
			include 'comandos/codigo.php';
		break;

		case '/totaladicionados':
			include 'comandos/totaladicionados.php';
		break;

		case '/afiliado':
		case '/afiliados':
		case '/referencias':
			include 'comandos/afiliados.php';
		break;

		case '/comprar':
			include 'comandos/comprar.php';
		break;

		case '/alertas':
			include 'comandos/alertas.php';
		break;

		// parte dedicada ao dono do bot
		case '/addsaldo':
			include 'comandos/addsaldo.php';
		break;

		case '/removesaldo':
			include 'comandos/removesaldo.php';
		break;

		case '/removeblock':
			include 'comandos/removeblock.php';
		break;

		case '/addresgate':
			include 'comandos/addresgate.php';
		break;

		case '/getsaldo':
			include 'comandos/getsaldo.php';
		break;

	endswitch;

endfor;
