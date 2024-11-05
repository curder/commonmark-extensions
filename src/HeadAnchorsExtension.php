<?php

namespace Curder\CommonmarkExtensions;

use Curder\CommonmarkExtensions\HeadAnchors\Event\ConfigureHeadingLinks;
use Curder\CommonmarkExtensions\HeadAnchors\Event\CustomAnchors;
use League\CommonMark\Environment\EnvironmentBuilderInterface;
use League\CommonMark\Event\DocumentParsedEvent;
use League\CommonMark\Extension\ConfigurableExtensionInterface;
use League\Config\ConfigurationBuilderInterface;
use Nette\Schema\Expect;

class HeadAnchorsExtension implements ConfigurableExtensionInterface
{
    public function configureSchema(ConfigurationBuilderInterface $builder): void
    {
        $builder->addSchema('head_anchors', Expect::structure([
            'enabled' => Expect::bool()->default(true),
        ]));
    }

    public function register(EnvironmentBuilderInterface $environment): void
    {
        if ($environment->getConfiguration()->get('head_anchors.enabled')) {
            $environment
                ->addEventListener(DocumentParsedEvent::class, new CustomAnchors)
                ->addEventListener(DocumentParsedEvent::class, new ConfigureHeadingLinks);
        }
    }
}
