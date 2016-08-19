<?php

require_once("class.config.php");

$project = new XsollaStandartSampleProject();
$billing = new XsollaStandartSampleBilling();

$protocol = new XsollaStandartProtocol($project, $billing);
$protocol->processRequest();
