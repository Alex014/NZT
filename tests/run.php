<?php

use nzt\classes\BaseContainer;

require __DIR__ . '/../src/classes/Loader.php';
require __DIR__ . '/../src/exceptions/EloaderFileNotFound.php';
require __DIR__ . '/../src/exceptions/EloaderClassNotFound.php';
require __DIR__ . '/../src/exceptions/EloaderClassMethodNotFound.php';
require __DIR__ . '/../src/exceptions/EloaderModuleNotFound.php';

require __DIR__ . '/../src/classes/BaseContainer.php';
require __DIR__ . '/../src/exceptions/EContainerItemNotFound.php';

class Container1 extends BaseContainer 
{

}

class Container2 extends BaseContainer 
{

}


class x
{
    public function getValue()
    {
        return 'xxx';
    }
}

class y
{
    public function getValue()
    {
        return 'yyy';
    }
}

$c1 = Container1::getInstance();
$c2 = Container2::getInstance();
$x = new x();
$y = new y();

$c1->set('123', $x);
$c2->set('123', $y);

var_dump($c1->get('123')->getValue());
var_dump($c2->get('123')->getValue());

$c2->get('zxc');
