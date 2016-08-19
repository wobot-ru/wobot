<?php

abstract class XsollaProtocol
{
    protected $project = null;

    protected $billing = null;

    final protected function checkIP($ip)
    {
        return in_array($ip, XsollaConfig::$allowedIPs);
    }

    abstract public function processRequest();
}
