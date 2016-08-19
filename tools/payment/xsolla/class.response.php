<?php

class XsollaStandartProtocolResponse
{
    public static $codeSuccess = 0;

    public static $codeTemporaryError = 1;

    public static $codeIncorrectUser = 2;

    public static $codeIncorrectSignature = 3;

    public static $codeIncorrectRequestFormat = 4;

    public static $codeOtherError = 5;

    public static $codePaymentCannotBeProcessed = 7;

    public static $codeCancelSuccess = 0;

    public static $codeCancelNotFound = 2;

    public static $codeCancelFailed = 7;
}