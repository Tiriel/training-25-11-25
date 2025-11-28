<?php

namespace App\Tests\Smoke;

use App\Factory\UserFactory;
use App\Repository\UserRepository;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Group;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\Routing\Route;
use Symfony\Component\Routing\RouterInterface;
use Zenstruck\Foundry\Test\Factories;

class SmokeTest extends WebTestCase
{
    use Factories;

    private static KernelBrowser $client;

    public static function setUpBeforeClass(): void
    {
    }

    #[Group('smoke')]
    #[DataProvider('provideMethodAndStaticUrl')]
    public function testPublicUrlIsSuccessful(string $method, string $url): void
    {
        static::bootKernel();
        $user = UserFactory::randomOrCreate();
        static::ensureKernelShutdown();

        $client = static::createClient();
        $client->loginUser($user->_real());
        $client->request($method, $url);
        if (\in_array($client->getResponse()->getStatusCode(), [301, 302, 307, 308])) {
            $client->followRedirect();
            $this->assertStringNotContainsString('/login', $client->getRequest()->getUri());
        }

        $this->assertSame(200, $client->getResponse()->getStatusCode());
    }

    public static function provideMethodAndStaticUrl(): \Generator
    {
        /** @var RouterInterface $router */
        $router = static::getContainer()->get(RouterInterface::class);
        $collection = $router->getRouteCollection();
        static::ensureKernelShutdown();

        foreach ($collection as $routeName => $route) {
            /** @var Route $route */
            $variable = $route->compile()->getVariables();
            if (count(array_diff($variable, array_keys($route->getDefaults()))) > 0) {
                continue;
            }
            if ([] === $methods = $route->getMethods()) {
                $methods[] = 'GET';
            }

            foreach ($methods as $method) {
                yield "$method $routeName" => [$method, $router->generate($routeName)];
            }
        }
    }
}
