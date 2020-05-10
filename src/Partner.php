<?php

namespace Jebanany\Lvlup;
class Partner extends Orders
{

    public function partnerIpInfo($ip)
    {
        return $this->doRequest('GET', '/v4/partner/ip/' . $ip );
    }

}