<?php

use Shibanashiqc\AmazonPay\Request;
require_once __DIR__ . '/../vendor/autoload.php';


$amazon = new Request('AUTHORIZATION', 'TV66eGKlmBiZY8UF5QUf', 'sOmBsoLR');
$amazon->setSHARequestPassphrase('4745buB4VYzg9.M9cFEmMC$&');
$amazon->setCallback('https://earneasy.ospo.in/api/callback');
$amazon->setSHAType('sha256');
$amazon->setCurrency('OMR');
// $amazon->setRedirectUrl('https://checkout.payfort.com/FortAPI/paymentPage');
// $amazon->setPaymentApi('https://paymentservices.payfort.com/FortAPI/paymentApi');
echo $amazon->generateForm(rand(1,99), 1000, 'test@gmail.com', 'test description');
