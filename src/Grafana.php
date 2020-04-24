<?php

namespace Jebanany\Lvlup;
class Grafana extends Report
{
    public function grafanaPing()
    {
        return $this->doRequest('GET', '/grafana', false, 'text/plain');
    }

    public function grafanaRawQuery(array $rawDataQuery)
    {
        return $this->doRequest('POST', '/grafana/query', $rawDataQuery);
    }

    public function grafanaTimeseriesList()
    {
        return $this->doRequest('POST', '/grafana/search', ['type' => 'timeseries', 'target' => '']);
    }

    public function grafanaTablesList()
    {
        return $this->doRequest('POST', '/grafana/search', ['type' => 'tables', 'target' => '']);
    }

}