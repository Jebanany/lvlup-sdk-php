<?php

namespace Jebanany\Lvlup;
class Sandbox
{
    public function sandboxAccountCreate()
    {
        return $this->doRequest('POST', '/v4/sandbox/account/new');
    }

    public function sandboxPaymentAccept($paymentId)
    {
        $this->avalaibleExceptions = [400 => 'Payment already accepted.', 404 => 'Pending payment doesn\'t exist or you don\'t have access to it.'];
        return $this->doRequest('POST', '/v4/sandbox/wallet/up/' . $paymentId . '/ok');
    }
}