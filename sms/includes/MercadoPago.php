<?php

/*
**
* @httd1 <t.me/httd1>
**
* @meurepositorio <t.me/meurepositorio>
**
*/

class MercadoPago {
	
	function __construct ($access_token)
	{
		
		$this->access_token = $access_token;
		
	}
		
	public function getPagamento ($id_pagamento)
	{
		
		$getPagamento = $this->request ('https://api.mercadopago.com/v1/payments/'.$id_pagamento);
		
		return $getPagamento;
		
	}

	public function setPreferencia ($dados)
	{
		
		$preferencia = $this->request ('https://api.mercadopago.com/checkout/preferences', 'POST', json_encode ($dados));
		
		return $preferencia;
		
	}

	public function buscaPagamento ($dados)
	{
		
		$getPagamento = $this->request ('https://api.mercadopago.com/v1/payments/search?'.http_build_query ($dados));
		
		return $getPagamento;
		
	}
		
	private function request ($url, $method = 'GET', $data = null, $header = [])
	{

	$header = array_merge ($header , ["Authorization: Bearer {$this->access_token}"]);

	$connect = curl_init ();

	curl_setopt ($connect, CURLOPT_URL, $url);
	curl_setopt ($connect, CURLOPT_RETURNTRANSFER, true);
	curl_setopt ($connect, CURLOPT_SSL_VERIFYPEER, false);
	curl_setopt ($connect, CURLOPT_SSL_VERIFYHOST, false);
	curl_setopt ($connect, CURLOPT_HTTPHEADER, $header);
	
	if ($method == 'POST'){
		curl_setopt ($connect, CURLOPT_POST, true);
		curl_setopt ($connect, CURLOPT_POSTFIELDS, $data);
	}
				
	$request = curl_exec ($connect);
	
	if ($request == false){
		return 'Error: '.curl_error ($connect);
	}
	
		return json_decode ($request, true);

	}
		
	public function getMe ()
	{
		
		return $this->request ('https://api.mercadolibre.com/users/me');
		
	}
		
	public function getSaldo ()
	{
		
		$id_usuario=$this->getMe ()['id'];
		
		return $this->request ('https://api.mercadopago.com/users/'.$id_usuario.'/mercadopago_account/balance');
		
	}
	
}