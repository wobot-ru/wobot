<?php

require_once("class.response.php");
require_once("class.billing.php");
require_once("class.project.php");
require_once("class.protocol.php");
require_once("class.standart.php");

/*
 * Xsolla Config
 */
class XsollaConfig
{
    public static $mysqlHost = "localhost";
    public static $mysqlUser = "wobot";
    public static $mysqlPass = "JFHsvosd";
    public static $mysqlDb = "wobot";

    public static $allowedIPs = array("94.103.26.178","94.103.26.181");

    public static $secretKey = ";&XD,[0@=N,w4%Pt/P{&KrO/@5R&V292";
}


class XsollaStandartSampleProject extends XsollaStandartProject
{
    public function check()
    {
        // put your code here
        return array("code" => XsollaStandartProtocolResponse::$codeSuccess, "description" => "Success");
    }

    public function sell()
    {
        // put your code here
        return array("code" => XsollaStandartProtocolResponse::$codeSuccess, "description" => "Success");
    }

    public function cancel()
    {
        // put your code here
        return array("code" => XsollaStandartProtocolResponse::$codeCancelSuccess, "description" => "Success");
    }
}

class XsollaStandartSampleBilling extends XsollaBilling
{

}