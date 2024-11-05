<?php

namespace Curder\CommonmarkExtensions;

use Curder\CommonmarkExtensions\CodeGroups\Block\CodeGroupsBlock;
use Curder\CommonmarkExtensions\CodeGroups\Parser\CodeGroupsStartParser;
use Curder\CommonmarkExtensions\CodeGroups\Renderer\CodeGroupsRenderer;
use League\CommonMark\Environment\EnvironmentBuilderInterface;
use League\CommonMark\Extension\ConfigurableExtensionInterface;
use League\Config\ConfigurationBuilderInterface;
use Nette\Schema\Expect;

class CodeGroupsExtension implements ConfigurableExtensionInterface
{
    public function configureSchema(ConfigurationBuilderInterface $builder): void
    {
        $builder->addSchema('code_groups', Expect::structure([
            'enable' => Expect::bool(true),
            'class_name' => Expect::structure([
                // 容器类名
                'code-group' => Expect::string('code-group')->nullable(),
                'code-group-tabs' => Expect::string('code-group-tabs')->nullable(),
                'code-group-contents' => Expect::string('code-group-contents')->nullable(),

                // 选项卡类名
                'code-group-tab' => Expect::string('code-group-tab')->nullable(),
                'code-group-tab-active' => Expect::string('active')->nullable(),

                // 内容区域类名
                'code-group-content' => Expect::string('code-group-content')->nullable(),
                'code-group-content-active' => Expect::string('active')->nullable(),
            ]),
        ]));
    }

    public function register(EnvironmentBuilderInterface $environment): void
    {
        if ($environment->getConfiguration()->get('code_groups.enable')) {
            $environment
                ->addBlockStartParser(new CodeGroupsStartParser)
                ->addRenderer(CodeGroupsBlock::class, new CodeGroupsRenderer);
        }
    }
}
