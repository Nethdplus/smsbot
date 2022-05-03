<?php

class ControleProcessos {

	public function __construct ()
	{
		$this->redis = rDis::con ();
	}

	public function getProcesso ($id)
	{
		return json_decode ($this->redis->get ($id), true);
	}

	public function setProcesso ($id, $dados)
	{
		return $this->redis->setEx ($id, 1500, json_encode ($dados));
	}

	public function existsProcesso ($id)
	{
		return $this->redis->exists ($id);
	}

	public function deletaProcesso ($id)
	{
		return $this->redis->del ($id);
	}

	public function updateProcesso ($id, $parametro, $valor)
	{

		$processo = $this->getProcesso ($id);

		if (!isset ($processo [$parametro])) return false;

		$processo [$parametro] = $valor;

			return $this->setProcesso ($id, $processo);

	}

}