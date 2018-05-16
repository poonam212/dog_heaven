<?php
/*
Description: API Braintree Payment.
Author: Ziad Tamim
Version: 1.0
Author URI: https://intensifystudio.com
*/

require 'vendor/autoload.php';

Braintree_Configuration::environment('sandbox');
Braintree_Configuration::merchantId('jsb9zjpjcfb2q25y');
Braintree_Configuration::publicKey('g5f7cj2sq8nqrf4r');
Braintree_Configuration::privateKey('2e6627129ef8b7122074da098cbcf901');

// Get the credit card details submitted by the form

$paymentMethodNonce =  $_POST['payment_method_nonce'];
$amount = $_POST['amount'];
$result = Braintree_Transaction::sale([
  'amount' => $amount,
  'paymentMethodNonce' => $paymentMethodNonce,
  'options' => [
  'submitForSettlement' => True
  ]
]);
echo json_encode($result);

?>
