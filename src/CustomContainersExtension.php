<?php

namespace Curder\CommonmarkExtensions;

use Curder\CommonmarkExtensions\CustomContainers\Block\CustomContainerBlock;
use Curder\CommonmarkExtensions\CustomContainers\Parser\CustomContainerStartParser;
use Curder\CommonmarkExtensions\CustomContainers\Renderer\CustomContainerRenderer;
use League\CommonMark\Environment\EnvironmentBuilderInterface;
use League\CommonMark\Extension\ConfigurableExtensionInterface;
use League\Config\ConfigurationBuilderInterface;
use Nette\Schema\Expect;

class CustomContainersExtension implements ConfigurableExtensionInterface
{
    public function configureSchema(ConfigurationBuilderInterface $builder): void
    {
        $builder->addSchema('custom_containers', Expect::structure([
            'enabled' => Expect::bool()->default(true),
            'labels' => Expect::structure([
                'tip' => Expect::string()->default('TIP'),
                'warning' => Expect::string()->default('WARNING'),
                'danger' => Expect::string()->default('DANGER'),
                'info' => Expect::string()->default('INFO'),
                'details' => Expect::string()->default('Details'),
            ]),
            'details' => Expect::structure([
                'open' => Expect::bool()->default(false),
            ]),
            'class_name' => Expect::structure([
                'container' => Expect::string()->default('custom-block'),
                'prefix' => Expect::string()->default(''),
                'title' => Expect::string()->default('custom-block-title'),
                'content' => Expect::string()->default('custom-block-content'),
            ]),
        ]));
    }

    public function register(EnvironmentBuilderInterface $environment): void
    {
        if ($environment->getConfiguration()->get('custom_containers.enabled')) {
            $environment
                ->addBlockStartParser(
                    new CustomContainerStartParser($environment->getConfiguration())
                )
                ->addRenderer(CustomContainerBlock::class, new CustomContainerRenderer);
        }
    }
}
