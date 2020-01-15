# qovery-php-client

Get Qovery instance

```php
require 'vendor/autoload.php';
$qovery = new Qovery();

$db = $qovery->getDatabaseByName("my-pql");

$host = $db->host;
$port = $db->port;
$name = $db->name;
$username = $db->username;
$password = $db->password;

echo "Qovery DB $name running on $host:$port - authentication: $username:$password";
```
