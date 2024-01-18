<?php

use Shibanashiqc\AmazonPay\Request;
require_once __DIR__ . '/../vendor/autoload.php';


$amazon = new Request('CAPTURE', 'TV66eGKlmBiZY8UF5QUf', 'sOmBsoLR');
$amazon->setSHARequestPassphrase('4745buB4VYzg9.M9cFEmMC$&');
$amazon->setCallback('https://earneasy.ospo.in/api/callback');
$amazon->setSHAType('sha256');
$amazon->setCurrency('OMR');
// $amazon->setRedirectUrl('https://checkout.payfort.com/FortAPI/paymentPage');
// $amazon->setPaymentApi('https://paymentservices.payfort.com/FortAPI/paymentApi');
echo json_encode($amazon->generateFormForResponse('23', 1000,'test d.escription', '169996200013782784'));
