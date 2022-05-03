<?php

/*
*
** Por @httd1<t.me/httd1>
*
*/

class bdTelegram {

	public function __construct ($nome_bd = 'recebersmsbot.db')
	{
		$this->nome_bd = $nome_bd;
		$this->con = new PDO ("sqlite:{$this->nome_bd}");
	}

	public function usuario ($id_telegram)
	{

		if (empty ($this->getUsuario ($id_telegram))){
			$this->addUsuario ($id_telegram);
		}

		return $this->getUsuario ($id_telegram);

	}

	public function addUsuario ($id_telegram)
	{
		return $this->queryExec ("INSERT INTO usuarios (id_telegram) VALUES (?)", [$id_telegram], true);
	}

	public function getUsuario ($id_telegram)
	{

		$busca = $this->queryExec ("SELECT * FROM usuarios WHERE id_telegram=?", [$id_telegram]);
			return $busca->fetch ();

	}

	// define um novo pais para o usuário
	public function setPais ($id_telegram, $id_pais)
	{
		return $this->queryExec ("UPDATE usuarios SET pais=? WHERE id_telegram=?", [$id_pais, $id_telegram], true);
	}

	// adiciona saldo a um usuario
	public function setSaldo ($id_telegram, $saldo)
	{

		return $this->queryExec ("UPDATE usuarios SET saldo=? WHERE id_telegram=?", [$saldo, $id_telegram], true);

	}

	public function getSaldo ($id_telegram)
	{

		$busca = $this->queryExec ("SELECT * FROM usuarios WHERE id_telegram=?", [$id_telegram]);
			return @$busca->fetch ()['saldo'];

	}

	// remove saldo de um usuario
	public function removeSaldo ($id_telegram, $saldo = 0)
	{
		return $this->queryExec ("UPDATE usuarios SET saldo=? WHERE id_telegram=?", [$saldo, $id_telegram], true);
	}

	// adiciona saldo resgatado no bd
	public function addResgate ($id_telegram, $id_resgate, $saldo_resgate)
	{
		return $this->queryExec ("INSERT INTO resgates (id_telegram, id_resgate, saldo_resgate, data_resgate) VALUES (?, ?, ?, ?)", [$id_telegram, $id_resgate, $saldo_resgate, time ()], true);
	}

	// busca um saldo resgatado por id de pagamento
	public function getResgate ($id_resgate)
	{

		$busca = $this->queryExec ("SELECT * FROM resgates WHERE id_resgate=?", [$id_resgate]);
			return $busca->fetch ();

	}

	// checa se é o primeiro resgate de saldo desse usuario, usado no sistema de afiliados
	public function checkPrimeiroResgate ($id_telegram)
	{

		$busca = $this->queryExec ("SELECT COUNT(*) AS total FROM resgates WHERE id_telegram = ?", [$id_telegram]);
			return ($busca->fetch ()['total'] == 0) ? true : false;

	}

	// adiciona um código para ser resgatado no bd
	public function addCodigoResgate ($codigo, $valor)
	{
		return $this->queryExec ("INSERT INTO codigos (codigo, valor, data) VALUES (?, ?, ?)", [$codigo, $valor, time ()], true);
	}

	// busca um codigo de resgate salvo
	public function getCodigoResgate ($codigo)
	{

		$busca = $this->queryExec ("SELECT * FROM codigos WHERE codigo=?", [$codigo]);
			return $busca->fetch ();

	}

	// deleta um codigo de resgate do bd
	public function deleteCodigoResgate ($codigo)
	{
		return $this->queryExec ("DELETE FROM codigos WHERE codigo=?", [$codigo], true);
	}

	// pega todos os usuarios cadastrados no sistema
	public function todosUsuarios ()
	{

		$busca = $this->queryExec ("SELECT * FROM usuarios", [], false);
			return $busca->fetchAll ();
			
	}

	// busca referencia do usuario e o indicado
	public function getReferencia ($id_telegram, $id_indicado)
	{

		$busca = $this->queryExec ("SELECT * FROM referencias WHERE id_telegram = ? AND id_indicado = ? LIMIT 1", [$id_telegram, $id_indicado]);
			return $busca->fetch ();
			
	}

	// verifica se esse usuario já é referencia de alguêm
	public function checkReferencia ($id_indicado) :bool
	{

		$busca = $this->queryExec ("SELECT COUNT(*) AS total FROM referencias WHERE id_indicado = ? LIMIT 1", [$id_indicado]);
			return ($busca->fetch ()['total'] == 0) ? false : true;
			
	}

	// guarda referencia do usuario
	public function setReferencia ($id_telegram, $id_indicado)
	{

		return $this->queryExec ("INSERT INTO referencias (id_telegram, id_indicado, data) VALUES (?, ?, ?)", [$id_telegram, $id_indicado, time ()], true);
			
	}

	// busca referencia do usuario (quem indicou o bot para esse usuario)
	public function getReferenciaIndicado ($id_indicado)
	{

		$busca = $this->queryExec ("SELECT * FROM referencias WHERE id_indicado = ? LIMIT 1", [$id_indicado]);
			return $busca->fetch ();
			
	}

	// retorna quantas indicações esse usuário já fez
	public function countReferencias ($id_telegram)
	{

		$busca = $this->queryExec ("SELECT COUNT(*) as total FROM referencias WHERE id_telegram = ?", [$id_telegram]);
			return $busca->fetch ()['total'];

	}

	// pega indicações por prametros
	public function getReferencias ($id_telegram, $limit = 10, $ordem = 'DESC')
	{

		$busca = $this->queryExec ("SELECT * FROM referencias WHERE id_telegram = ? ORDER BY id {$ordem} LIMIT {$limit}", [$id_telegram]);
			return $busca->fetchAll ();

	}

	public function setAlerta ($id_telegram, $id_servico){

		return $this->queryExec ("INSERT INTO alertas (id_telegram, id_servico) VALUES (?, ?)", [$id_telegram, $id_servico], true);

	}

	public function getAlerta ($id_telegram, $id_servico){

		$busca = $this->queryExec ("SELECT * FROM alertas WHERE id_telegram = ? AND id_servico = ? LIMIT 1", [$id_telegram, $id_servico]);
			return $busca->fetch ();

	}

	public function deleteAlerta ($id_telegram, $id_servico){

		return $this->queryExec ("DELETE FROM alertas WHERE id_servico = ? AND id_servico = ?", [$id_servico, $id_servico], true);

	}

	// retorna alertas salvos com base no pais dos usuarios que criaram o alerta
	public function getAlertasPaises ($id_pais){

		$busca = $this->queryExec ("SELECT * FROM alertas WHERE id_telegram IN (SELECT id_telegram FROM usuarios WHERE pais = ?)", [$id_pais]);
			return $busca->fetchAll ();

	}

	public function limpaAlertasUsuario ($id_telegram){

		if (empty ($id_telegram)){
			return false;
		}

		return $this->queryExec ("DELETE FROM alertas WHERE id_telegram = ?", [$id_telegram], true);

	}

	public function queryExec ($sql, $data = [], $status = false)
	{

		$prepare = $this->con->prepare ($sql);
		$exec = $prepare->execute ($data);

		if ($status){
			return $exec;
		}

			return $prepare;

	}
}