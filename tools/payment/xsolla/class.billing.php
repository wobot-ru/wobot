<?php

/*
CREATE TABLE  `dvapay`.`xsolla_billing` (
 `id` int(10) NOT NULL AUTO_INCREMENT,
 `invoice` bigint(20) NOT NULL COMMENT 'Xsolla invoice ID',
 `v1` varchar(100) NOT NULL,
 `v2` varchar(100) DEFAULT NULL,
 `v3` varchar(100) DEFAULT NULL,
 `amount` decimal(10,2) NOT NULL COMMENT 'Amount of payment',
 `currency` varchar(3) DEFAULT NULL COMMENT 'Payment currency',
 `date` timestamp NULL DEFAULT NULL COMMENT 'Date and time of payment',
 `code` int(10) DEFAULT NULL COMMENT 'Response code',
 `description` varchar(100) DEFAULT NULL COMMENT 'Response description',
 `date_add` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
 `canceled` enum('0','1') NOT NULL DEFAULT '0',
 `date_cancel` timestamp NULL DEFAULT NULL,
 PRIMARY KEY (`id`),
 UNIQUE KEY `invoice_UNIQUE` (`invoice`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;
*/

/*
 * Xsolla Billing
 */
class XsollaBilling
{
    protected static $connection;

    public function  __construct()
    {
        self::$connection = new mysqli(XsollaConfig::$mysqlHost, XsollaConfig::$mysqlUser, XsollaConfig::$mysqlPass, XsollaConfig::$mysqlDb);
        
        if (mysqli_connect_errno()) 
            throw new Exception("Connection error: ".mysqli_connect_error());

        self::$connection->query("SET NAMES utf8");
    }

    public function putTransaction($invoice, $v1, $v2, $v3, $amount, $currency, $date)
    {
        $sql = "
        INSERT INTO
            xsolla_billing
        SET
            invoice = ?
        ,   v1 = ?
        ,   v2 = ?
        ,   v3 = ?
        ,   amount = ?
        ,   currency = ?
        ,   date = ?
        ON DUPLICATE KEY UPDATE
            v1 = ?
        ,   v2 = ?
        ,   v3 = ?
        ,   amount = ?
        ,   currency = ?
        ";

        $stmt = self::$connection->prepare($sql);
        $stmt->bind_param("isssdsssssds", $invoice, $v1, $v2, $v3, $amount, $currency, $date, $v1, $v2, $v3, $amount, $currency);
        if (!$stmt->execute())
            throw new Exception ("Can't execute query: ".$sql.", error ".$stmt->errno.": ".$stmt->error, $stmt->errno);
    }

    public function getState($invoice)
    {
        $sql = "SELECT code FROM xsolla_billing WHERE invoice = ? LIMIT 1";

        $stmt = self::$connection->prepare($sql);
        $stmt->bind_param("i", $invoice);
        if (!$stmt->execute())
            throw new Exception ("Can't execute query: ".$sql.", error ".$stmt->errno.": ".$stmt->error, $stmt->errno);

        $stmt->bind_result($code);
        if (!$stmt->fetch())
            $code = -1;

        return $code;
    }

    public function updateState($invoice, $code, $description)
    {
        $sql = "
        UPDATE
            xsolla_billing
        SET
            code = ?
        ,   description = ?
        WHERE
            invoice = ?
        ";

        $stmt = self::$connection->prepare($sql);
        $stmt->bind_param("isi", $code, $description, $invoice);
        if (!$stmt->execute())
            throw new Exception ("Can't execute query: ".$sql.", error ".$stmt->errno.": ".$stmt->error, $stmt->errno);
    }

    public function cancelTransaction($invoice)
    {
        $sql = "
        UPDATE
            xsolla_billing
        SET
            canceled = '1'
        ,   date_cancel = NOW()
        WHERE
            invoice = ?
        ";

        $stmt = self::$connection->prepare($sql);
        $stmt->bind_param("i", $invoice);
        if (!$stmt->execute())
            throw new Exception ("Can't execute query: ".$sql.", error ".$stmt->errno.": ".$stmt->error, $stmt->errno);
    }

    public function getId($invoice)
    {
        $sql = "SELECT id FROM xsolla_billing WHERE invoice = ?";
        $stmt = self::$connection->prepare($sql);

        $stmt->bind_param("i", $invoice);
        if (!$stmt->execute())
            throw new Exception ("Can't execute query: ".$sql.", error ".$stmt->errno.": ".$stmt->error, $stmt->errno);

        $stmt->bind_result($id);
        if (!$stmt->fetch())
            $id = -1;

        return $id;
    }
}