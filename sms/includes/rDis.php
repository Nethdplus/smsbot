<?php

class rDis {

	public function __construct ()
	{}

	public static function con ()
	{

		static $conexao = null;

		if (is_null ($conexao)){

			$conexao = new Redis ();
			$conexao->pconnect ('localhost');

		}

			return $conexao;

	}
	
}