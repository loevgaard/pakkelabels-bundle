<?php

namespace Loevgaard\PakkelabelsBundle\Tests\DependencyInjection;

use Loevgaard\PakkelabelsBundle\DependencyInjection\LoevgaardPakkelabelsExtension;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Config\Definition\Exception\InvalidConfigurationException;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Yaml\Parser;

class LoevgaardPakkelabelsExtensionTest extends TestCase
{
    public function testThrowsExceptionUnlessApiUsernameSet()
    {
        $this->expectException(InvalidConfigurationException::class);

        $loader = new LoevgaardPakkelabelsExtension();
        $config = $this->getEmptyConfig();
        unset($config['api_username']);
        $loader->load([$config], new ContainerBuilder());
    }

    public function testThrowsExceptionUnlessApiPasswordSet()
    {
        $this->expectException(InvalidConfigurationException::class);

        $loader = new LoevgaardPakkelabelsExtension();
        $config = $this->getEmptyConfig();
        unset($config['api_password']);
        $loader->load([$config], new ContainerBuilder());
    }

    public function testGettersSetters()
    {
        $loader = new LoevgaardPakkelabelsExtension();
        $config = $this->getEmptyConfig();
        $container = new ContainerBuilder();
        $loader->load([$config], $container);

        $this->assertSame($config['api_username'], $container->getParameter('loevgaard_pakkelabels.api_username'));
        $this->assertSame($config['api_password'], $container->getParameter('loevgaard_pakkelabels.api_password'));
    }

    /**
     * @return array
     */
    protected function getEmptyConfig()
    {
        $yaml = <<<EOF
api_username: username
api_password: password
EOF;
        $parser = new Parser();

        return $parser->parse($yaml);
    }
}
