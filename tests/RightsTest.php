<?php declare(strict_types=1);

namespace Parable\Rights\Tests;

use Parable\Rights\Exception;
use Parable\Rights\Rights;

class RightsTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @var Rights
     */
    protected $rights;

    public function setUp()
    {
        parent::setUp();

        $this->rights = new Rights();

        $this->rights->add('create', 'read', 'update', 'delete');

    }

    public function testAddAndGetRight()
    {
        $rights = new Rights();

        self::assertNull($rights->get('create'));

        $rights->add('create');

        self::assertSame(1, $rights->get('create'));
    }

    public function testCannotAddSameRightTwice()
    {
        $this->expectException(Exception::class);
        $this->expectExceptionMessage("Cannot redefine right with name 'create'");

        $rights = new Rights();
        $rights->add('create');
        $rights->add('create');
    }

    public function testAddAndGetMultipleRights()
    {
        $rights = new Rights();

        self::assertNull($rights->get('create'));
        self::assertNull($rights->get('read'));
        self::assertNull($rights->get('update'));
        self::assertNull($rights->get('delete'));

        $rights->add('create', 'read', 'update', 'delete');

        self::assertSame(1, $rights->get('create'));
        self::assertSame(2, $rights->get('read'));
        self::assertSame(4, $rights->get('update'));
        self::assertSame(8, $rights->get('delete'));
    }

    public function testGetAll()
    {
        self::assertSame(
            [
                'create' => 1,
                'read' => 2,
                'update' => 4,
                'delete' => 8,
            ],
            $this->rights->getAll()
        );
    }

    public function testCheck()
    {
        self::assertTrue($this->rights->can('1111', 'create'));
        self::assertTrue($this->rights->can('1111', 'read'));
        self::assertTrue($this->rights->can('1111', 'update'));
        self::assertTrue($this->rights->can('1111', 'delete'));

        self::assertTrue($this->rights->can('0001', 'create'));
        self::assertTrue($this->rights->can('0010', 'read'));
        self::assertTrue($this->rights->can('0100', 'update'));
        self::assertTrue($this->rights->can('1000', 'delete'));

        self::assertFalse($this->rights->can('0000', 'create'));
        self::assertFalse($this->rights->can('0000', 'read'));
        self::assertFalse($this->rights->can('0000', 'update'));
        self::assertFalse($this->rights->can('0000', 'delete'));

        self::assertTrue($this->rights->can('1001', 'create'));
        self::assertFalse($this->rights->can('1001', 'read'));
        self::assertFalse($this->rights->can('1001', 'update'));
        self::assertTrue($this->rights->can('1001', 'delete'));
    }

    public function testCombine()
    {
        self::assertSame('10101', $this->rights->combine('00001', '10000', '00100'));
    }

    public function testGetNames()
    {
        self::assertSame(
            ['create', 'read', 'update', 'delete'],
            $this->rights->getNames()
        );
    }

    public function testGetRightsFromNames()
    {
        self::assertSame('0011', $this->rights->getRightsFromNames('read', 'create'));
        self::assertSame('1010', $this->rights->getRightsFromNames('read', 'delete'));
    }

    public function testGetNamesFromRights()
    {
        self::assertSame(['create', 'read'], $this->rights->getNamesFromRights('0011'));
        self::assertSame(['read', 'delete'], $this->rights->getNamesFromRights('1010'));
    }
}
