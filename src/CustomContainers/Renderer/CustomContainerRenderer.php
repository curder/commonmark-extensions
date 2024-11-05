<?php

namespace Curder\CommonmarkExtensions\CustomContainers\Renderer;

use Curder\CommonmarkExtensions\CustomContainers\Block\CustomContainerBlock;
use League\CommonMark\Node\Node;
use League\CommonMark\Renderer\ChildNodeRendererInterface;
use League\CommonMark\Renderer\NodeRendererInterface;
use League\CommonMark\Util\HtmlElement;

class CustomContainerRenderer implements NodeRendererInterface
{
    public function render(Node $node, ChildNodeRendererInterface $childRenderer)
    {
        CustomContainerBlock::assertInstanceOf($node);

        /** @var CustomContainerBlock $node */
        $config = $node->getConfig();

        // 获取配置的类名，如果未配置则使用默认值
        $containerClass = $config->get('custom_containers.class_name.container');
        $prefixClass = $config->get('custom_containers.class_name.prefix');
        $titleClass = $config->get('custom_containers.class_name.title');
        $contentClass = $config->get('custom_containers.class_name.content');

        $type = $node->getType();
        $title = $node->getTitle();
        $isOpen = $node->isOpen();

        $innerContent = $childRenderer->renderNodes($node->children());

        $openAttribute = $isOpen ? ['open' => ''] : [];

        if ($type === 'details') {
            return new HtmlElement(
                'details',
                array_merge(['class' => "{$prefixClass}{$type} {$containerClass}"], $openAttribute),
                [
                    new HtmlElement('summary', ['class' => $titleClass], $title),
                    new HtmlElement('div', ['class' => $contentClass], $innerContent),
                ]
            );
        }

        return new HtmlElement(
            'div',
            ['class' => "{$prefixClass}{$type} {$containerClass}"],
            [
                new HtmlElement('div', ['class' => $titleClass], $title),
                new HtmlElement('div', ['class' => $contentClass], $innerContent),
            ]
        );
    }
}
