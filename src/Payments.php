<?php

namespace jebanany\lvlup;

class Payments extends Partner
{

    public function paymentsList(int $limit = null, int $afterId = null, int $beforeId = null)
    {
        return $this->doRequest('GET', '/v4/payments', [
            'limit' => $limit, 'afterId' => $afterId, 'beforeId' => $beforeId
        ]);
    }

    public function paymentsBalance()
    {
        return $this->doRequest('GET', '/v4/wallet');
    }

    public function paymentsCreate(int $amount, string $redirectUrl = '', string $webhookUrl = '')
    {
        return $this->doRequest('POST', '/v4/wallet/up', [
            'amount' => $this->getValidAmount($amount), 'redirectUrl' => $redirectUrl, 'webhookUrl' => $webhookUrl
        ]);
    }
    public function paymentsStatus($paymentId)
    {

        $this->avalaibleExceptions = [404 => 'Pending payment doesn\'t exist or you don\'t have access to it.'];
        return $this->doRequest('GET', '/v4/wallet/up/' . $paymentId);

    }




}