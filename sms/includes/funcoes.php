<?php

/*
*
** Por @httd1<t.me/httd1>
*
*/

// pega cotação do rublo(₽) usado na api para fazer conversão para reais
// é usado o redis para cache da cotação e evitar requisições desnecessarias
function cotacaoRublo ()
{

	global $redis;

	if ($redis->exists ('cotacao-rublo')){
		return $redis->get ('cotacao-rublo');
	}

	$get = @json_decode (file_get_contents ('https://www.xe.com/pt/api/stats.php?fromCurrency=RUB&toCurrency=BRL'), true);

	if (isset ($get ['payload'])){

		$cotacao = $get ['payload']['Last_30_Days']['high'];
		// guarda cache por 24 horas
		$redis->setEx ('cotacao-rublo', 86400, $cotacao);

			return $cotacao;

	}

	return 0.0735315535; // cotação padrão, dia 13/01/2020

}

function getPorcento ($valor, $porcentagem){
	return number_format ((($porcentagem*$valor)/100), 2);
}

function valorSMS ($valor_original, $porcentagem)
{

	$conversao = cotacaoRublo ()*$valor_original; // convertido para real
	$porcentagem_lucro = getPorcento ($conversao, $porcentagem); // valor do lucro
	
		return number_format ($conversao+$porcentagem_lucro, 2);

}

function gerarHash ($tamanho = 13)
{

	$str = str_shuffle('abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ*%$!1234567890');
		return substr ($str, 0, $tamanho);

}

function incrementoPorcento ($valor, $porcentagem)
{

	return (getPorcento ($valor, $porcentagem)+$valor); // soma e retorna
	
}

function __is ($tipo)
{

	global $tlg;

	$data = $tlg->getData ();

	$request_tipo = @$data ['message']['chat']['type'] ?? @$data ['callback_query']['message']['chat']['type'] ?? 'not-private';

	if ($tipo == $request_tipo){
		return true;
	}

		return false;

}

function show_logs ($data){

	$data = $data ['result'][0];

	$idioma = @$data ['message']['from']['language_code'] ?? $data ['callback_query']['from']['language_code'] ?? 'n\a';
	$nome = @$data ['message']['from']['first_name'] ?? $data ['callback_query']['from']['first_name'] ?? 'n\a';
	$username = @$data ['message']['from']['username'] ?? $data ['callback_query']['from']['username'] ?? 'n\a';
	$user_id = @$data ['message']['from']['id'] ?? $data ['callback_query']['from']['id'] ?? 'n\a';
	$comando = @$data ['message']['text'] ?? @$data ['callback_query']['data'];
	$data = date ('d/m/Y H:i');

	echo "ID : {$user_id}\nNome : {$nome}\nUser : @{$username}\nLang : {$idioma}\nComando : {$comando}\nData: {$data}";
	echo "\n\e[1;34m----------------------\e[0m\n";

}

function show_logs_processos ()
{

	global $info_processo, $pendente, $id_telegram, $id_sms, $nome_servico, $numero, $valor, $info_user, $sms, $segundo_sms;

	echo "\e[1;34m=======================\e[0m\n";
	echo "Processo: {$pendente ['id']}\n";
	echo "Usuario: {$id_telegram}\n";
	echo "ID SMS: {$id_sms}\n";
	echo "Serviço: {$nome_servico}\n";
	echo "Numero: +{$numero}\n";
	echo "Valor: R\${$valor}\n";
	echo "Pais: ".PAISES [$info_user ['pais']]."\n";
	echo "Segundo SMS: {$segundo_sms}\n";
	echo "Tempo Ativ.: ".minutosPassados ($pendente ['createDate'])." min.\n";
	echo "SMS: {$sms}\n";

}

function show_logs_alertas ()
{

	global $alerta, $nome_servico, $servico_alerta;

	echo "Usuário: {$alerta ['id_telegram']}";
	echo "\n";
	echo "Serviço: {$nome_servico} ({$alerta ['id_servico']})";
	echo "\n";
	echo "Data: ".date ('d/m H:i');
	echo "\n";
	echo "Quant. Números: {$servico_alerta ['count']}";
	echo "\n";
	echo "\e[1;34m=======================\e[0m\n\r";

}

function check_member (){

	// global
	global $tlg, $bd_tlg, $redis;

	if (STATUS_BONUS_ADICAO){

		$data = $tlg->getData ();
		$tipo = @$data ['message']['new_chat_participant'];

		if (empty ($tipo)){
			return;
		}

		$id_usuario = @$data ['message']['from']['id'];
		$id_adicionado = @$tipo ['id'];
		$usuario_bot = @$tipo ['is_bot'];
		$chat = @$data ['message']['chat']['id'];

		if ($id_usuario == $id_adicionado || $usuario_bot || $chat != GRUPO_ID){
			return;
		}

		// incremeta quantidade de usuarios s=adicionados por esse usuario ao grupo
		$count_add = $redis->incr ("ref:{$id_usuario}");

		if ($count_add >= MINIMO_ADICAO){ // deleta se tiver chegado ao limite e adiciona saldo

			$redis->del ("ref:{$id_usuario}");
			$saldo_atual = $bd_tlg->getSaldo ($id_usuario);

			// adiciona créditos
			$bd_tlg->setSaldo ($id_usuario, $saldo_atual+BONUS_ADICAO);

			$tlg->sendMessage ([
				'chat_id' => $id_usuario,
				'text' => '🤩 <b>Foram somados R$'.number_format (BONUS_ADICAO, 2).' na sua conta por adiconar usuários ao grupo</b>',
				'parse_mode' => 'html'
			]);

		}

	}

}

function limpa_novos ()
{

	// global
	global $tlg;

	$data = $tlg->getData ();
	
	if (!isset ($data ['message']['new_chat_participant']) && !isset ($data ['message']['left_chat_participant'])){
		return;
	}

	$tlg->deleteMessage ([
		'chat_id' => $tlg->ChatID (),
		'message_id' => $tlg->MessageID ()
	]);

}

function minutosPassados ($times)
{
	return round((time ()-$times)/60);
}

function segundosPassados ($times)
{
	return round((time ()-$times));
}

function ordenaPaises (&$paises){

	uasort ($paises, function ($v1, $v2){ /* usando indice 9 por conta do padrão do nome do pais com o emoji no inicio */
		return ($v1[9] < $v2[9]) ? -1 : 1;
	});

		return $paises;

}

// retorna true se $timestamp for menor que $tempo_resgate
function resgateValido ($tempo_resgate, $timestamp)
{
	return (minutosPassados ($timestamp) <= $tempo_resgate);
}

function anti_flood (){

	global $redis, $bd_tlg, $tlg;

	// barra se o usuário já tiver sido bloqueado
	if ($redis->exists ("block-flood:{$tlg->UserID ()}")){

		$tlg->msg ([
			'chat_id' => $tlg->UserID (),
			'text' => "<i>⚠️ Você foi bloqueado por 10 min. por tentativa de flood, se acha que foi injusto vai se fuder :)</i>",
			'parse_mode' => 'html',
			'message_id' => $tlg->MessageID ()
		]);

		exit;
	}


	$ultima_interacao = $redis->get ("anti-flood:{$tlg->UserID ()}");

	if (minutosPassados ($ultima_interacao) < 1){
	// se a ultima interação do usuario for menor que 1 minuto

		$contador = $redis->incr ("contador-flood:{$tlg->UserID ()}"); // contador de flood, incrementando

		if ($contador >= 50){ // passou do limite

			// block no usuário floodero, malandro, vagabundo por 10 min.
			$redis->setEx ("block-flood:{$tlg->UserID ()}", 600, 'true');

		}

	}else {
		$redis->set ("anti-flood:{$tlg->UserID ()}", time()); // guarda a ultima interação do usuário
		$redis->del ("contador-flood:{$tlg->UserID ()}"); // limpa contador de flood
	}

}

/*
	Adiciona os serviços personalizados com prefixo pers_ na lista de serviços,
	usando dados do serviço Outros
*/
function servicosPersonalizados ($lista_servicos, $dados_servico){

	global $user;

	$novo_servico = [];

	foreach ($dados_servico as $id_servico => $dados){

		if (strpos($id_servico, 'pers_') !== false && $dados ['pais'] == $user ['pais']){

			$novo_servico [$id_servico] = [
				'cost' => $lista_servicos ['ot']['cost'],
				'count' => $lista_servicos ['ot']['count']
			];

		}

	}

	return array_merge ($novo_servico, $lista_servicos);

}

function anti_cancelamento ($id_servico){

	if (ANTI_CANCELAMENTO){

		global $redis, $bd_tlg, $tlg, $user;

		$key_tempo_redis = "tempo:{$id_servico}:{$tlg->UserID ()}";
		$key_contador_redis = "contador-cancelamento:{$id_servico}:{$tlg->UserID ()}:{$user ['pais']}";

		// controle para que a contagem só valha por hj
		if (!$redis->exists ($key_tempo_redis)){

			$redis->setEx ($key_tempo_redis, 43200, 'true');
			$redis->del ($key_contador_redis);

		}

		$contador = $redis->incr ($key_contador_redis);

		if ($contador == CANCELAMENTO_MINIMO){

			// penalisa removendo saldo
			$saldo_atual = $bd_tlg->getSaldo ($tlg->UserID ());
			$novo_saldo = ($saldo_atual <= VALOR_DESCONTO_BLOCK) ? 0 : number_format (abs (($saldo_atual-VALOR_DESCONTO_BLOCK)), 2);
			$bd_tlg->setSaldo ($tlg->UserID (), $novo_saldo);

			// block no usuário abusador de serviços alheios
			$redis->setEx ("block-abuso:{$tlg->UserID ()}", TEMPO_BLOCK, 'true');
			$redis->del ($key_contador_redis); // reinicia o contador de cancelamento

		}

	}

}

function check_block ($id_servico){

	global $tlg, $redis, $bd_tlg;

	if ($redis->exists ("block-abuso:{$tlg->UserID ()}")){

		$tlg->msg ([
			'chat_id' => $tlg->UserID (),
			'text' => "<i>⚠️ Você foi bloqueado por 30 min. por abuso do sistema, cancelar muitos números sem usa-los reduz a quantidade disponível afetando outros usuários :{</i>",
			'parse_mode' => 'html',
			'message_id' => $tlg->MessageID ()
		]);

		exit;

	}

}