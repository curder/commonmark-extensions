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
        $iconEnabled = $config->get('custom_containers.icons.enabled');

        $type = $node->getType();
        $title = $node->getTitle();
        $isOpen = $node->isOpen();

        $innerContent = $childRenderer->renderNodes($node->children());

        $isDetails = $type === 'details';

        $detailsOpenAttribute = ($isDetails && $isOpen) ? ['open' => ''] : [];

        $titleElement = $iconEnabled ?
            new HtmlElement($isDetails ? 'summary' : 'div', ['class' => $titleClass], [
                new HtmlElement('div', [], $config->get('custom_containers.icons.'.$type)),
                new HtmlElement('span', [], $title),
            ])
            : new HtmlElement($isDetails ? 'summary' : 'div', ['class' => $titleClass], $title);

        return new HtmlElement(
            $isDetails ? 'details' : 'div',
            array_merge(['class' => "{$prefixClass}{$type} {$containerClass}"], $detailsOpenAttribute),
            [
                $titleElement,
                new HtmlElement('div', ['class' => $contentClass], $innerContent),
            ]
        );
    }
}
