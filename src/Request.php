<?php

namespace Shibanashiqc\AmazonPay;

class Request
{
    private $command = 'AUTHORIZATION';
    private $access_code;
    private $merchant_identifier;
    private $redirectUrl = 'https://sbcheckout.payfort.com/FortAPI/paymentPage';
    private $paymentApi = 'https://sbpaymentservices.payfort.com/FortAPI/paymentApi';
    
    private $SHA_REQUEST_PASSPHRASE;
    private $SHA_TYPE;
    
    private $currency = 'OMR';
    
    private $callbackUrl;

    public function __construct($command, $access_code, $merchant_identifier)
    {
        $this->command = $command;
        $this->access_code = $access_code;
        $this->merchant_identifier = $merchant_identifier;
    }
    
    public function setSHARequestPassphrase($SHA_REQUEST_PASSPHRASE)
    {
        $this->SHA_REQUEST_PASSPHRASE = $SHA_REQUEST_PASSPHRASE;
    }
    
    public function setCallback($url)
    {
        $this->callbackUrl = $url;
    }
    
    public function getCallback()
    {
        return $this->callbackUrl;
    }

    public function setSHAType($SHA_TYPE)
    {
        $this->SHA_TYPE = $SHA_TYPE;
    }
   
    public function setCurrency($currency)
    {
        $this->currency = $currency;
    }
    
    public function setRedirectUrl($redirectUrl)
    {
        $this->redirectUrl = $redirectUrl;
    }
    
    public function getPaymentApi()
    {
        return $this->paymentApi;
    }
    
    public function setPaymentApi($paymentApi)
    {
         $this->paymentApi = $paymentApi;
    }
    
    public function getRedirectUrl()
    {
        return $this->redirectUrl;
    }
        
    /**
     * generateSignature
     *
     * @param  mixed $merchant_reference
     * @param  mixed $amount
     * @param  mixed $email
     * @param  mixed $des
     * @return string
     */
    public function generateSignature($params) : string
    {
        $shaString = '';
        ksort($params);
        foreach ($params as $k => $v) {
            $shaString .= "$k=$v";
        }
        $shaString = $this->SHA_REQUEST_PASSPHRASE . $shaString . $this->SHA_REQUEST_PASSPHRASE;
        $signature = hash($this->SHA_TYPE, $shaString);
        return $signature;
    }
    
    /**
     * generateForm
     *
     * @param  mixed $merchant_reference
     * @param  mixed $amount
     * @param  mixed $email
     * @param  mixed $des
     * @return string
     */
    
    public function generateForm($merchant_reference, $amount, $email, $des) 
    {   
            $requestParams = array(
                'command' => $this->command,
                'access_code' => $this->access_code,
                'merchant_identifier' => $this->merchant_identifier,
                'merchant_reference' => $merchant_reference,
                'amount' =>  $amount,
                'currency' => $this->currency,
                'language' => 'en',
                'customer_email' => $email,
                'return_url' => $this->getCallback(),
                'order_description' => $des,
                );
            
            $signature = $this->generateSignature($requestParams);
            $requestParams['signature'] = $signature;
            
            $redirectUrl = $this->redirectUrl;
            $html = "<html xmlns='https://www.w3.org/1999/xhtml'>\n<head></head>\n<body>\n";
            $html .= "<form action='$redirectUrl' method='post' name='frm'>\n";
            foreach ($requestParams as $a => $b) {
                $html .= "\t<input type='hidden' name='".htmlentities($a)."' value='".htmlentities($b)."'>\n";
            }
            $html .= "\t<script type='text/javascript'>\n";
            $html .= "\t\tdocument.frm.submit();\n";
            $html .= "\t</script>\n";
            $html .= "</form>\n</body>\n</html>";
            
            // $dom = new \DOMDocument();
            // $dom->loadHTML($html);
            // $dom->saveHTMLFile('form.html');
            return $html;
    }
    
    /**
     * generateSignature
     *
     * @param  mixed $merchant_reference
     * @param  mixed $amount
     * @param  mixed $email
     * @param  mixed $des
     * @return string
     */
    
    public function generateSignatureForResponse($params) : string
    {
        $shaString = '';
        ksort($params);
        foreach ($params as $k => $v) {
            $shaString .= "$k=$v";
        }
        $shaString = $this->SHA_REQUEST_PASSPHRASE . $shaString . $this->SHA_REQUEST_PASSPHRASE;
        $signature = hash($this->SHA_TYPE, $shaString);
        return $signature;
    }
    
    /**
     * generateForm
     *
     * @param  mixed $merchant_reference
     * @param  mixed $amount
     * @param  mixed $email
     * @param  mixed $des
     * @return object
     */
    
    public function generateFormForResponse($merchant_reference, $amount, $des, $fort_id)
    {
        $url = $this->getPaymentApi();
        $arrData = array(
            'command' => $this->command,
            'access_code' => $this->access_code,
            'merchant_identifier' => $this->merchant_identifier,
            'merchant_reference' => $merchant_reference,
            'amount' => $amount,
            'currency' => $this->currency,
            'language' => 'en',
            'fort_id' => $fort_id,
            'order_description' => $des,
            );
        
            $signature = $this->generateSignature($arrData);
            $arrData['signature'] = $signature;
            
            $ch = curl_init( $url );
            $data = json_encode($arrData);
            curl_setopt( $ch, CURLOPT_POSTFIELDS, $data);
            curl_setopt( $ch, CURLOPT_HTTPHEADER, array('Content-Type:application/json'));
            curl_setopt( $ch, CURLOPT_RETURNTRANSFER, true );
            $response = curl_exec($ch);
            curl_close($ch);
            return json_decode($response);
    }
}