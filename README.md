# Description
Maintained by [Exonet](https://www.exonet.nl). This package started as a fork of `digicatech/resellerclub-php-api`, which is no longer available.

This client is used to interact with one of the following APIs:
 * ResellerClub ([Docs](https://resellerclub.webpropanel.com/kb/answer/751))
 
Available API requests: 
* Actions
* Billing
* Contacts
* Customers
* Dns
* Domains
* Orders
* Products
* Sslcert


## Installation
`composer.json`:
```
"repositories": [
    {
        "type": "vcs",
        "url": "https://github.com/exonet/resellerclub-php-api"
    }
],
"require": {
    "exonet/resellerclub-php-api": "^2.0"
}
```


## Usage Example
`live mode`:
```php
use Exonet\ResellerClub\ResellerClub;

$resellerClub = new ResellerClub('<userId>', '<apiKey>');
$resellerClub->domains()->available(['google', 'example'], ['com', 'net']);
```


`test mode`:
```php
use Exonet\ResellerClub\ResellerClub;

$resellerClub = new ResellerClub('<userId>', '<apiKey>',true);
$resellerClub->domains()->available(['google', 'example'], ['com', 'net']);
```
Note: All functions return a raw response from API.


### Disclaimer
Many thanks to [Ahmet Bora](https://github.com/afbora "Ahmet Bora"). This repository based on his [ResellerClub PHP SDK](https://github.com/afbora/resellerclub-php-sdk "ResellerClub PHP SDK") repository.
