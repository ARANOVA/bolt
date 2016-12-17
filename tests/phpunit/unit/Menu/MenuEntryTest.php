<?php

namespace Bolt\Tests\Menu;

use Bolt\Menu\MenuEntry;
use Bolt\Tests\BoltUnitTest;
use Symfony\Component\Routing\Generator\UrlGenerator;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Routing\RequestContext;
use Symfony\Component\Routing\RouteCollection;

/**
 * Class to test src/Menu/MenuEntry.
 *
 * @author Gawain Lynch <gawain.lynch@gmail.com>
 */
class MenuEntryTest extends BoltUnitTest
{
    public function testCreateRoot()
    {
        $rootEntry = $this->createRoot()
            ->setLabel('Root Entry')
            ->setIcon('fa:koala')
        ;

        $this->assertInstanceOf(MenuEntry::class, $rootEntry);
        $this->assertSame('/bolt', $rootEntry->getUri());
        $this->assertSame('root', $rootEntry->getName());
        $this->assertSame('Root Entry', $rootEntry->getLabel());
        $this->assertSame('fa:koala', $rootEntry->getIcon());
        $this->assertSame('everyone', $rootEntry->getPermission());

        $rootEntry->setPermission('strict');
        $this->assertSame('strict', $rootEntry->getPermission());
    }

    public function testCreateChild()
    {
        $rootEntry = $this->createRoot();

        $extendEntry = $rootEntry->add(
            (new MenuEntry('dropbear', 'drop-bears'))
        );

        $this->assertSame('/bolt/drop-bears', $extendEntry->getUri());

        $this->assertSame($extendEntry, $rootEntry->get('dropbear'));
        $this->assertSame($extendEntry, $rootEntry->children()['dropbear']);
    }

    public function testRoute()
    {
        /** @var UrlGeneratorInterface|\PHPUnit_Framework_MockObject_MockObject $urlGenerator */
        $urlGenerator = $this->getMock(UrlGeneratorInterface::class);
        $urlGenerator->expects($this->once())
            ->method('generate')
            ->with('route', ['foo' => 'bar'])
            ->willReturn('/bolt/derp')
        ;

        $rootEntry = MenuEntry::createRoot($urlGenerator, '');

        $sub = $rootEntry->add(new MenuEntry('sub'))
            ->setRoute('route', ['foo' => 'bar'])
        ;

        $this->assertSame('/bolt/derp', $sub->getUri());
        $this->assertSame('/bolt/derp', $sub->getUri()); // assert generator only called once
    }

    private function createRoot()
    {
        $urlGenerator = new UrlGenerator(new RouteCollection(), new RequestContext());
        $rootEntry = MenuEntry::createRoot($urlGenerator, '/bolt');

        return $rootEntry;
    }
}
