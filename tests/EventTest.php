<?php
require_once __DIR__ . '/../src/classes/Loader.php';
use nzt\classes\Loader;

Loader::$baseFileName = __DIR__ . '/../src/traits/';
Loader::requireFiles(['EventDispatcher']);

use nzt\traits\EventDispatcher;
use PHPUnit\Framework\TestCase;

// class storage
// {
//     public static array $data = [];
// }

class event123
{
    private string $a;
    private string $b;

    public function __construct(string $a, $b)
    {
        $this->a = $a;
        $this->b = $b;
    }

    public function getA()
    {
        return $this->a;
    }

    public function getB()
    {
        return $this->b;
    }
}

class class123
{
    use EventDispatcher;

    public function go()
    {
        $event = new event123('AAAAAAAAAAAAA', 'BBBBBBBBB');
        $this->dispath($event);
    }
}

class controller123
{
    public function xxx(event123 $event)
    {
        storage::$data['a'] = $event->getA();
        storage::$data['b'] = $event->getB();
    }
}

class EventTest extends TestCase
{
    public function testEventDispath()
    {
        $class123 = new class123();

        $class123->addListener(event123::class, function(event123 $event)
            {
                storage::$data['a'] = $event->getA();
                storage::$data['b'] = $event->getB();
            }
        );

        $class123->go();

        $this->assertTrue( storage::$data['a'] === 'AAAAAAAAAAAAA' );

        $this->assertTrue( storage::$data['b'] === 'BBBBBBBBB' );

        storage::$data['a'] = '';
        storage::$data['b'] = '';

        $class123->addListener(event123::class, Loader::getFunction(controller123::class, 'xxx'));

        $class123->go();

        $this->assertTrue( storage::$data['a'] === 'AAAAAAAAAAAAA' );

        $this->assertTrue( storage::$data['b'] === 'BBBBBBBBB' );
    }
}