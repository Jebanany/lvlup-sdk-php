<?php

namespace jebanany\lvlup;
class Services extends Payments
{
    public function servicesList()
    {
        return $this->doRequest('GET', '/v4/services');
    }

    public function servicesAttacksList(int $vpsIds, int $limit = null, int $afterId = null, int $beforeId = null)
    {
        return $this->doRequest('GET', '/v4/services/vps/' . $vpsIds . '/attacks', [
            'limit' => $limit, 'afterId' => $afterId, 'beforeId' => $beforeId
        ]);
    }

    public function servicesUdpFilterStatus(int $vpsId)
    {
        return $this->doRequest('GET', '/v4/services/vps/' . $vpsId . '/filtering');
    }

    public function servicesUdpFilterStatusSet(int $vpsId, bool $changeTo)
    {
        return $this->doRequest('PUT', '/v4/services/vps/' . $vpsId . '/filtering', ['filteringEnabled' => $changeTo]);
    }

    public function servicesUdpFilterWhitelist(int $vpsId)
    {
        return $this->doRequest('GET', '/v4/services/vps/' . $vpsId . '/filtering/whitelist');
    }

    public function servicesUdpFilterWhitelistRuleAdd(int $vpsId, int $portFrom, int $portTo, string $protocol)
    {
        return $this->doRequest('POST', '/v4/services/vps/' . $vpsId . '/filtering/whitelist', [
            'id' => $vpsId, 'porst' => [
                'from' => $this->validatePort($portFrom), 'to' => $this->validatePort($portTo)
            ], 'protocol' => $this->validateUdpProtocol($protocol)
        ]);
    }

    public function servicesUdpFilterWhitelistRuleDel(int $vpsId, int $ruleId)
    {
        return $this->doRequest('DELETE', '/v4/services/vps/' . $vpsId . '/filtering/whitelist/' . $ruleId);
    }

    public function servicesProxmoxGenerateCredentials(int $vpsId)
    {
        return $this->doRequest('POST', '/v4/services/vps/' . $vpsId . '/proxmox');
    }

    public function servicesVpsStart(int $vpsId)
    {
        return $this->doRequest('POST', '/v4/services/vps/' . $vpsId . '/start');
    }

    public function servicesVpsState(int $vpsId)
    {
        return $this->doRequest('GET', '/v4/services/vps/' . $vpsId . '/stats');
    }

    public function servicesVpsStop(int $vpsId)
    {
        return $this->doRequest('POST', '/v4/services/vps/' . $vpsId . '/stop');
    }
}