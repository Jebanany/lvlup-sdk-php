# lvlup-php-sdk
Biblioteka PHP obsługująca https://api.lvlup.pro/v4 oraz  https://sandbox-api.lvlup.pro/v4

## Wymagania
* PHP 7+
* curl (opcjonalnie)
## Instalacja
Aby zainstalować bibliotekę należy użyć Composera:
```bash
composer require jebanany/lvlup
```

## Jak stosować
### Implementacja
```php
use Jebanany\Lvlup\ApiClient;
require_once('vendor/autoload.php');

// Normalne używanie
$lvlup = new ApiClient('apikey');
// bez używania cURL
$lvlup = new ApiClient('apikey', false, false);

// Sandbox
$lvlup = new ApiClient('sandboxapikey', false);
// bez używania cURL
$lvlup = new ApiClient('sandboxapikey', true, false);
```
### Przykłady
**1. Korzystanie z Sandboxa**

Generowanie nowego konta w Sandboxie, tworzenie płatności, sprawdzenie statusu, zmiana statusu, ponowne sprawdzenie statusu.

```php
use Jebanany\Lvlup\ApiClient;

require_once('vendor/autoload.php');

try {
    // Pobieramy nowe dane do konta sandbox
    // zachowaj te dane do swoich następnych testów; nie generuj ich za każdym razem!
    $lvlupSandbox = new ApiClient(false, true);
    $sanboxUser = $lvlupSandbox->sandboxAccountCreate();

    echo "<pre>";
    echo "Dane dostępowe";
    echo "\n ID: " . $sanboxUser->id;
    echo "\n Username: " . $sanboxUser->username;
    echo "\n Password: " . $sanboxUser->password;
    echo "\n Email: " . $sanboxUser->email;
    echo "\n APIkey: " . $sanboxUser->apiKey;
    // zachowaj te dane do swoich następnych testów; nie generuj ich za każdym razem!

    // Mając dane konta sandbox tworzymy nową instancję klasy, tym razem z APIkey
    $lvlupSandbox = new ApiClient($sanboxUser->apiKey, true);

    // Generujemy nową płatność
    $payment = $lvlupSandbox->paymentsCreate(25.00);

    echo "\nDane o wygenerowanej płatności";
    echo "\n url: " . $payment->url;
    echo "\n id: " . $payment->id;

    // Sprawdzamy status płatności
    $status = $lvlupSandbox->paymentsStatus($payment->id);

    echo "\nStatus płatności przed opłaceniem";
    echo "\n payed: " . ($status->payed ? 'true' : 'false'); // false
    echo "\n amountStr: " . $status->amountStr;
    echo "\n amountInt: " . $status->amountInt;
    echo "\n amountWithFeeStr: " . $status->amountWithFeeStr;
    echo "\n amountWithFeeInt: " . $status->amountWithFeeInt;

    // Zmieniamy status płatności - opłacamy / ustawiamy jako opłacone
    $lvlupSandbox->sandboxPaymentAccept($payment->id);

    // Sprawdzamy status płatności ponownie
    $status = $lvlupSandbox->paymentsStatus($payment->id);
    echo "\nStatus płatności po opłaceniu";
    echo "\n payed: " . ($status->payed ? 'true' : 'false'); // true
    echo "\n amountStr: " . $status->amountStr;
    echo "\n amountInt: " . $status->amountInt;
    echo "\n amountWithFeeStr: " . $status->amountWithFeeStr;
    echo "\n amountWithFeeInt: " . $status->amountWithFeeInt;
    echo "</pre>";

} catch (Exception $e) {
    echo 'Kod błędu: <b>' . $e->getCode() . '</b> treść błędu: <b>' . $e->getMessage() . '</b>';
} catch (TypeError $e) {
    echo 'Kod błędu: <b>' . $e->getCode() . '</b> <br>treść błędu: <b>' . $e->getMessage() . '</b>';
}

```
**2. Używanie bloków catch i try**

W przypadku niepowodzenia zostaną wygenerowane `Exception` lub `TypeError` (inne niż wymagane parametry funkcji np. `string` zamiast `int`)
W przypadku błędów `Exception` dostępna jest pomocnicza wiadomość (`getMessage()`) i kod odpowiedzi HTTP (`getCode()`) - jeśli zapytanie w ogóle wystąpiło, jeśli nie, `getCode()` zwróci 0.
```php
use Jebanany\Lvlup\ApiClient;

require_once('vendor/autoload.php');

try {
    // Bez APIkey
    $lvlup = new ApiClient(false);
    $sanboxUser = $lvlup->userMe(); // generuje Exception

} catch (Exception $e) {
    echo 'Kod błędu: <b>' . $e->getCode() . '</b> treść błędu: <b>' . $e->getMessage() . '</b>';
   // 
   // Kod błędu: 401 treść błędu: Unauthorized Error (Probably invalid API key)
   // 
} catch (TypeError $e) {
    echo 'Kod błędu: <b>' . $e->getCode() . '</b> <br>treść błędu: <b>' . $e->getMessage() . '</b>';
}
```
## Dostępne funkcje
```bash
ordersList(int $limit = null, int $afterId = null, int $beforeId = null)
partnerIpInfo(int $id)
paymentsBalance()
paymentsCreate(int $amount, string $redirectUrl = '', string $webhookUrl = '')
paymentsList(int $limit = null, int $afterId = null, int $beforeId = null)
paymentsStatus($paymentId)
reportPerformanceCreate($description = '')
sandboxAccountCreate()
sandboxPaymentAccept($paymentId)
servicesAttacksList(int $vpsIds, int $limit = null, int $afterId = null, int $beforeId = null)
servicesList()
servicesProxmoxGenerateCredentials(int $vpsId)
servicesUdpFilterStatus(int $vpsId)
servicesUdpFilterStatusSet(int $vpsId, bool $changeTo)
servicesUdpFilterWhitelist(int $vpsId)
servicesUdpFilterWhitelistRuleAdd(int $vpsId, int $portFrom, int $portTo, string $protocol)
servicesUdpFilterWhitelistRuleDel(int $vpsId, int $ruleId)
servicesVpsStart(int $vpsId)
servicesVpsState(int $vpsId)
servicesVpsStop(int $vpsId)
userMe()
userLogList(int $limit = null, int $afterId = null, int $beforeId = null)
userReferralCreate()
userReferralList()
```
## Opis metod

**Orders**

* ordersList
*Lista zamówień z paginacją*

```php
// ordersList(int $limit =null, int $afterId = null, int $beforeId = null)
// return object
$lvlup->orderList(); //return object
```

**Partner**


* partnerIpInfo
```php
// partnerIpInfo(int $id)
// return object
$lvlup->partnerIpInfo(1234); 
```

**Payments**

* paymentsBalance
*Ilość wirtualnych środków zgromadzona w portfelu*
```php
// paymentsBalance()
// return object
$lvlup->partnerIpInfo(); //object
```
* paymentsCreate
*Wygenerowanie nowej płatności*
```php
// paymentsCreate(int $amount, string $redirectUrl = '', string $webhookUrl = '')
// return object
$lvlup->paymentsCreate(1.50); 
$lvlup->paymentsCreate('1.50'); // string jako kwota również działa
$lvlup->paymentsCreate(17, 'http://example.com/redirect', 'http://example.com/webhook'); 
```
* paymentsList
*Lista przyjętych płatności*
```php
// paymentsList(int $limit = null, int $afterId = null, int $beforeId = null)
// return object
$lvlup->paymentsList(); 
$lvlup->paymentsList(50, 5); 
$lvlup->paymentsList(50, null, 5); 
```

* paymentsStatus
*Status istniejącej płatności*
```php
// paymentsStatus($paymentId)
// return object
$lvlup->paymentsStatus('paymentId'); 
```
**Report**
* reportPerformanceCreate
*Wysłanie raportu o nieprawidłowym działaniu usługi*
```php
// reportPerformanceCreate($description = '')
// return NULL
$lvlup->reportPerformanceCreate('Problems with MC server. TPS: 9/20'); 
$lvlup->reportPerformanceCreate('TS packetloss: 24%'); 
```
**Sandbox**
* sandboxAccountCreate
*Utworzenie nowego konta sandbox*
```php
// sandboxAccountCreate()
// return object
$lvlup->sandboxAccountCreate(); 
```
* sandboxPaymentAccept
*Zmiana statusu płatności na opłacone*
```php
// sandboxPaymentAccept($paymentId)
// return NULL
$lvlup->sandboxPaymentAccept('paymentId'); 
```
**Services**
* servicesAttacksList
*Lista ataków*
```php
// servicesAttacksList(int $vpsIds, int $limit = null, int $afterId = null, int $beforeId = null)
// return object
$lvlup->servicesAttacksList(123); 
$lvlup->servicesAttacksList(123, 50); 
// etc
```
* servicesList
*Lista usług*
```php
// servicesList()
// return object
$lvlup->servicesList();
```
* servicesProxmoxGenerateCredentials
*Generowanie danych dostępowych do panelu Proxmox*
```php
// servicesProxmoxGenerateCredentials($vpsId)
// return object
$lvlup->servicesProxmoxGenerateCredentials(123);
```
* servicesUdpFilterStatus
*Aktualny status filtrowania UDP*
```php
// servicesUdpFilterStatus($vpsId)
// return object
$lvlup->servicesUdpFilterStatus(123);
```
* servicesUdpFilterStatusSet
*Ustawienie aktualnego statusu filtrowania UDP*
```php
// servicesUdpFilterStatusSet(int $vpsId, bool $changeTo)
// return object
$lvlup->servicesUdpFilterStatusSet(123, true); //true - on; false - off
```
* servicesUdpFilterWhitelist
*Wyjątki filtrowania UDP*
```php
// servicesUdpFilterWhitelist(int $vpsId)
// return object
$lvlup->servicesUdpFilterWhitelist(123);
```
* servicesUdpFilterWhitelistRuleAdd
*Nowy wyjątek filtrowania UDP*
```php
// servicesUdpFilterWhitelistRuleAdd(int $vpsId, int $portFrom, int $portTo, string $protocol)
// $allowedProtocols = ['arkSurvivalEvolved', 'arma', 'gtaMultiTheftAutoSanAndreas', 'gtaSanAndreasMultiplayerMod', 'hl2Source', 'minecraftPocketEdition', 'minecraftQuery', 'mumble', 'rust', 'teamspeak2', 'teamspeak3', 'trackmaniaShootmania', 'other'];
// return object
$lvlup->servicesUdpFilterWhitelistRuleAdd(123, 9987, 9987, 'teamspeak3');
$lvlup->servicesUdpFilterWhitelistRuleAdd(123, 9526, 10465, 'other');
```
* servicesUdpFilterWhitelistRuleDel
*Usunięcie wyjątku filtrowania UDP*
```php
// servicesUdpFilterWhitelistRuleDel(int $vpsId, int $ruleId)
// return object
$lvlup->servicesUdpFilterWhitelist(123, 456);
```
* servicesVpsStart
*Wystartuj VPS*
```php
// servicesVpsStart(int $vpsId)
// return object
$lvlup->servicesVpsStart(123);
```
* servicesVpsState
*Aktualny status VPS*
```php
// servicesVpsState(int $vpsId)
// return object
$lvlup->servicesVpsState(123);
```
* servicesVpsStop
*Zatrzymaj VPS*
```php
// servicesVpsStop(int $vpsId)
// return object
$lvlup->servicesVpsStop(123);
```

**User**
* userMe
*Informacja o aktualnym użytkowniku APIkey*
```php
// userMe()
// return object
$lvlup->userMe();
```
* userLogList
```php
// userLogList(int $limit = null, int $afterId = null, int $beforeId = null)
// return object
$lvlup->userLogList();
$lvlup->userLogList(50);
// etc
```
* userReferralCreate
*Wygenerowanie nowego kodu polecającego*
```php
// userReferralCreate()
// return object
$lvlup->userReferralCreate();
```
* userReferralList
*Lista kodów polecających*
```php
// userReferralList()
// return object
$lvlup->userReferralList();
```