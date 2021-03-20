<?php
require_once __DIR__ . '/../src/classes/Loader.php';
use nzt\classes\Loader;

Loader::$baseFileName = __DIR__ . '/../src/';
Loader::requireFiles(['classes/BaseContainer','exceptions/EContainerItemNotFound']);

use PHPUnit\Framework\TestCase;
use nzt\classes\BaseContainer;

use nzt\exceptions\EContainerItemNotFound;

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

class ContainerTest extends TestCase
{
    public function testReadWriteValue()
    {
        $c1 = Container1::getInstance();
        $c2 = Container2::getInstance();
        $x = new x();
        $y = new y();
        
        $c1->set('123', $x);
        $c2->set('123', $y);
        
        $this->assertTrue($c1->get('123')->getValue() == 'xxx');
        $this->assertTrue($c2->get('123')->getValue() == 'yyy');
    }

    public function testNoValue()
    {
        $this->expectException(EContainerItemNotFound::class);

        $c2 = Container2::getInstance();
        $c2->get('zxc');
    }

    public function testRemoveValue()
    {
        $c1 = Container1::getInstance();
        $x = new x();
        
        $c1->set('789', $x);
        
        $this->assertTrue($c1->get('789')->getValue() == 'xxx');

        $c1->remove('789');

        $this->assertTrue($c1->has('789') === false);
    }

}