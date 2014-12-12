<?php

class Player
{
	private $uid;
	private $pName;
	private $compName;

	public function __construct($uid, $pName, $compName)
	{
		$this->uid = $uid;
		$this->pName = $pName;
		$this->compName = $compName;
	}

	public function getUid()
	{
		return $this->uid;
	}

	public function getPName()
	{
		return $this->pName;
	}

	public function getCompName()
	{
		return $this->compName;
	}

	public function toString()
	{
		return ($this->uid . ",".$this->pName.",".$this->compName);
	}

	public function toArray()
	{
		$output = array();

		$output["uid"] = $this->uid;
		$output["pName"] = $this->pName;
		$output["compName"] = $this->compName;

		return $output;
	}
}
