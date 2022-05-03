<?php

interface ApiSMS {

	public function __construct ($api_key);

	public function getBalance ();

	public function request ($data, $method, $headers);

}