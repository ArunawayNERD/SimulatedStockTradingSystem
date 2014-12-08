<?php

/**
	implements an object to hold the info for a single transaction.
	created by John
*/

class Transaction
{
	private $time;
	private $uid;
	private $portName;
	private $ticker;
	private $stockPrice;
	private $shareChange;
	private $cashChange;


	public function __construct($time, $uid, $name, $ticker, $price, $changeS, $changeC)
	{
		$this->time = $time;
		$this->uid = $uid;
		$this->portName = $name;
		$this->ticker = $ticker;
		$this->stockPrice = $price;
		$this->shareChange = $changeS;
		$this->cashChange = $changeC;
	}

	public function toString()
	{	
		$string = $this->getTime() . ",".$this->getUid().",".$this->getPortName().",";
		$string = $string.$this->getTicker().",".$this->getStockPrice().",";
		$string = $string.$this->getShareChange().",".$this->getCashChange();

		return $string;
	}

	public function getUid()
	{
		return $this->uid;
	}

	public function getStockPrice()
	{
		return $this->stockPrice;
	}

	public function getTime()
	{
		return $this->time;
	}

	public function getPortName()
	{
		return $this->portName;
	}
	public function getTicker()
	{
		return $this->ticker;
	}
	
	public function getShareChange()
	{
		return $this->shareChange;
	}
	public function getCashChange()
	{	
		return $this->cashChange;
	}
}
