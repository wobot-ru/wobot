<?php

abstract class XsollaProject
{
    public $invoice = null;
    public $v1 = null;
    public $v2 = null;
    public $v3 = null;
    public $amount = null;
    public $currency = null;
    public $date = null;
    public $command = null;
    public $signature = null;

    public $order = null;

    protected $secretKey = null;

    public function  __construct()
    {
        $this->secretKey = XsollaConfig::$secretKey;
    }
    
    public function setParams($command, $invoice, $v1, $v2, $v3, $amount, $currency, $date, $signature)
    {
        $this->v1 = $v1;
        $this->v2 = $v2;
        $this->v3 = $v3;
        $this->amount = $amount;
        $this->currency = $currency;
        $this->invoice = $invoice;
        $this->date = $date;
        $this->command = $command;
        $this->signature = $signature;
    }

    public function checkCancelSignature()
    {
        return md5($this->command.$this->invoice.$this->secretKey) == $this->signature;
    }
}

abstract class XsollaStandartProject extends XsollaProject
{
    abstract public function check();

    abstract public function sell();

    abstract public function cancel();

    public function checkStatusSignature()
    {
        return md5($this->command.$this->v1.$this->secretKey) === $this->signature;
    }

    public function checkPaySignature()
    {
        return md5($this->command.$this->v1.$this->invoice.$this->secretKey) === $this->signature;
    }
}
