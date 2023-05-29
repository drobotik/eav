### Connection
Library uses Laravel Capsule for connections.
For Laravel apps, it's trying to use default connection and can be used as is.
For other cases, initialize a Capsule instance:
```php
$capsule = new Capsule;
$capsule->addConnection([
    // connection settings
]);
$this->capsule = $capsule;
$capsule->setAsGlobal();
$capsule->bootEloquent();
```

### Work with migrations, cli-application
CLI app relies on a database connection. It uses [Doctrine DBAL](https://www.doctrine-project.org/projects/doctrine-dbal/en/latest/reference/configuration.html#configuration) to perform connections.<br />
There isn't any pre-configured connection information. For non-docker environments is required to set up a connection.
As an option, make a new file connection.php in root folder. This file will be automatically included.
```php
#connection.php
use Drobotik\Eav\Database\Connection;
$config = [
    // connection settings
]
$connection = Connection::get($config)
```
Note use a [driver](https://www.doctrine-project.org/projects/doctrine-dbal/en/latest/reference/configuration.html#driver) that suits your needs.

Migrate:
```bash
$ php eav migrations:migrate latest
```

### Laravel migrations support
It also contains laravel migrations for publishing on Laravel app. Add EavServiceProvider to
providers config array.
```php
\Drobotik\Eav\Database\Support\Laravel\EavServiceProvider::class
```
Publish migrations by vendor:publish.

## Example