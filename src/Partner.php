<?php

namespace Jebanany\Lvlup;
class Partner extends Orders
{

    public function partnerIpInfo(int $id)
    {
        return $this->doRequest('GET', '/v4/partner/ip/' . $id );
    }

}