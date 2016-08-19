<?php

class XsollaStandartProtocol extends XsollaProtocol
{
    public function __construct($project, $billing = null)
    {
        try
        {
            if (!is_subclass_of($project, "XsollaStandartProject"))
                throw new Exception("Incorrect object instance of XsollaStandartProject");

            $this->project = $project;

            if ($billing != null)
            {
                if (!is_subclass_of($billing, "XsollaBilling"))
                    throw new Exception("Incorrect object instance of XsollaBilling");

                $this->billing = $billing;
            }
        }
        catch (Exception $e)
        {
            $this->errorResponse($e);
        }
    }

    protected function processCheckRequest()
    {
        try
        {
            $this->project->setParams(
                    $_GET["command"]
                ,   null
                ,   urldecode($_GET["v1"])
                ,   urldecode($_GET["v2"])
                ,   urldecode($_GET["v3"])
                ,   null
                ,   null
                ,   null
                ,   $_GET["md5"]
            );

            if (!$this->project->checkStatusSignature())
                throw new Exception("Incorrect signature");

            if (!isset($_GET["v1"]))
                throw new Exception("User ID is undefined");

            $responseArr = $this->project->check();

            if (!is_array($responseArr) || !isset($responseArr["code"]) || !isset($responseArr["description"]))
                throw new Exception("Response code or description is undefined");

            $response = $this->generateCheckResponse($responseArr["code"], $responseArr["description"]);
        }
        catch (Exception $e)
        {
            $this->errorCheckResponse($e);
        }
    }

    protected function processPayRequest()
    {
        try
        {
            $this->project->setParams(
                    $_GET["command"]
                ,   $_GET["id"]
                ,   urldecode($_GET["v1"])
                ,   urldecode($_GET["v2"])
                ,   urldecode($_GET["v3"])
                ,   $_GET["sum"]
                ,   null
                ,   urldecode($_GET["date"])
                ,   $_GET["md5"]
            );

            if (!$this->project->checkPaySignature())
                throw new Exception("Incorrect signature");

            if (!isset($_GET["id"]))
                throw new Exception("Invoice is undefined");

            if (!isset($_GET["v1"]))
                throw new Exception("User ID is undefined");

            if (!isset($_GET["sum"]))
                throw new Exception("Amount is undefined");

            if ($this->billing->getState($this->project->invoice) != XsollaStandartProtocolResponse::$codeSuccess)
            {
                $this->billing->putTransaction($this->project->invoice, iconv("windows-1251", "utf-8", $this->project->v1), iconv("windows-1251", "utf-8", $this->project->v2), iconv("windows-1251", "utf-8", $this->project->v3), $this->project->amount, null, $this->project->date);
            
                $responseArr = $this->project->sell();

                if (!is_array($responseArr) || !isset($responseArr["code"]) || !isset($responseArr["description"]))
                    throw new Exception("Response code or description is undefined");

                $order = $this->billing->getId($this->project->invoice);

                $this->billing->updateState($this->project->invoice, $responseArr["code"], $responseArr["description"]);

                $response = $this->generatePayResponse($responseArr["code"], $responseArr["description"], $this->project->invoice, $order, $this->project->amount);
            }
            else
            {
                $response = $this->generatePayResponse(XsollaStandartProtocolResponse::$codeSuccess, "Success", $this->project->invoice, $this->billing->getId($this->project->invoice), $this->project->amount);
            }
            
        }
        catch (Exception $e)
        {
            $this->errorPayResponse($e);
        }
    }

    protected function processCancelRequest()
    {
        try
        {
            $this->project->setParams($_GET["command"], $_GET["id"], null, null, null, null, null, null, $_GET["md5"]);

            if (!$this->project->checkCancelSignature())
                throw new Exception("Incorrect signature");

            if (!isset($_GET["id"]))
                throw new Exception("Invoice is undefined");

            $responseArr = $this->project->cancel();

            if (!is_array($responseArr) || !isset($responseArr["code"]) || !isset($responseArr["description"]))
                throw new Exception("Response code or description is undefined");

            $this->billing->cancelTransaction($this->project->invoice);

            $response = $this->generateCancelResponse($responseArr["code"], $responseArr["description"]);
        }
        catch (Exception $e)
        {
            $this->errorCancelResponse($e);
        }
    }

    public function processRequest()
    {
        if ($this->project == null || $this->billing == null)
            return;

        try
        {
            if (!$this->checkIP($_SERVER["REMOTE_ADDR"]))
                throw new Exception ("IP address is not allowed", 1);

            if (!isset($_GET["command"]))
                throw new Exception("Command is undefined");

            $command = $_GET["command"];

            if ($command == "check")
            {
                $this->processCheckRequest();
            }
            elseif ($command == "pay")
            {
                $this->processPayRequest();
            }
            elseif ($command == "cancel")
            {
                $this->processCancelRequest();
            }
            else
            {
                throw new Exception("Incorrect command");
            }
        }
        catch (Exception $e)
        {
            $this->errorCheckResponse($e);
        }
    }

    protected function generateCheckResponse($code, $description)
    {
        $xml = new SimpleXMLElement("<response></response>");

        $xml->addChild("result", $code);
        $xml->addChild("comment", $description);
        
        header("Content-Type: text/xml; charset=cp1251");
        echo html_entity_decode($xml->asXML(), ENT_COMPAT, 'windows-1251');
    }

    protected function generatePayResponse($code, $description, $invoice = 0, $order = 0, $sum = 0)
    {
        $xml = new SimpleXMLElement("<response></response>");

        $xml->addChild("id", $invoice);
        $xml->addChild("id_shop", $order);
        $xml->addChild("sum", $sum);
        $xml->addChild("result", $code);
        $xml->addChild("comment", $description);

        header("Content-Type: text/xml; charset=cp1251");
        echo html_entity_decode($xml->asXML(), ENT_COMPAT, 'windows-1251');
    }

    protected function generateCancelResponse($code, $description)
    {
        $xml = new SimpleXMLElement("<response></response>");

        $xml->addChild("result", $code);
        $xml->addChild("comment", $description);

        header("Content-Type: text/xml; charset=cp1251");
        echo html_entity_decode($xml->asXML(), ENT_COMPAT, 'windows-1251');
    }

    protected function errorCheckResponse($e)
    {
        $this->generateCheckResponse(XsollaStandartProtocolResponse::$codePaymentCannotBeProcessed, $e->getMessage());
    }

    protected function errorPayResponse($e)
    {
        $this->generatePayResponse(XsollaStandartProtocolResponse::$codePaymentCannotBeProcessed, $e->getMessage());
    }

    protected function errorCancelResponse($e)
    {
        $this->generateCancelResponse(XsollaStandartProtocolResponse::$codeCancelFailed, $e->getMessage());
    }
}
