<?php

namespace Jebanany\Lvlup;
class Report extends Sandbox
{
    public function reportPerformanceCreate($description = '')
    {
        return $this->doRequest('POST', '/v4/report/performance', [
            'description' => $description
        ]);
    }
}