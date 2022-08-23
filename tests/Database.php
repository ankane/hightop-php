<?php

use Illuminate\Database\Capsule\Manager as Capsule;
use Illuminate\Database\Eloquent\Model;

$capsule = new Capsule();

$adapter = getenv('ADAPTER');
if ($adapter == false) {
    $adapter = 'sqlite';
}

if ($adapter == 'pgsql') {
    $capsule->addConnection([
        'driver' => 'pgsql',
        'database' => 'hightop_php_test'
    ]);
} elseif ($adapter == 'mysql') {
    $capsule->addConnection([
        'driver' => 'mysql',
        'database' => 'hightop_php_test',
        'host' => 'localhost',
        'username' => get_current_user()
    ]);
} elseif ($adapter == 'sqlite') {
    $capsule->addConnection([
        'driver' => 'sqlite',
        'database' => ':memory:'
    ]);
} else {
    throw new Exception('Invalid adapter');
}

echo "Using $adapter\n";

if (getenv('VERBOSE')) {
    $capsule->getConnection()->enableQueryLog();
    $capsule->getConnection()->setEventDispatcher(new \Illuminate\Events\Dispatcher());
    $capsule->getConnection()->listen(function ($query) {
        echo '[' . $query->time . '] ' . $query->sql . "\n";
    });
}

$capsule->setAsGlobal();
$capsule->bootEloquent();

Capsule::schema()->dropIfExists('visits');
Capsule::schema()->create('visits', function ($table) {
    $table->increments('id');
    $table->string('city')->nullable();
    $table->string('user_id')->nullable();
});

class Visit extends Model
{
    public $timestamps = false;
    protected $fillable = ['city', 'user_id'];
}

Hightop\Builder::register();
