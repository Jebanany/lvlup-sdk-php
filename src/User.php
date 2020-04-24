<?php

namespace Jebanany\Lvlup;
class User extends Grafana
{

    public function userMe()
    {
        return $this->doRequest('GET', '/v4/me');
    }

    public function userLogList(int $limit = null, int $afterId = null, int $beforeId = null)
    {
        return $this->doRequest('GET', '/v4/me/log', [
            'limit' => $limit, 'afterId' => $afterId, 'beforeId' => $beforeId
        ]);
    }

    public function userReferralList()
    {
        return $this->doRequest('GET', '/v4/me/referral');
    }

    public function userReferralCreate()
    {
        $this->avalaibleExceptions = [403 => 'You have at least one promo code already.'];
        return $this->doRequest('POST', '/v4/me/referral/generic');
    }

}