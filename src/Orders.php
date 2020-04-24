<?php

namespace Jebanany\Lvlup;
class Orders extends User
{
    public function ordersList(int $limit =null, int $afterId = null, int $beforeId = null)
    {
        return $this->doRequest('GET', '/v4/orders', [
            'limit' => $limit, 'afterId' => $afterId, 'beforeId' => $beforeId
        ]);
    }
}