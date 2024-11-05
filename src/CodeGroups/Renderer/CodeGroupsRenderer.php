<?php

namespace Curder\CommonmarkExtensions\CodeGroups\Renderer;

use Curder\CommonmarkExtensions\CodeGroups\Block\CodeGroupsBlock;
use InvalidArgumentException;
use League\CommonMark\Node\Node;
use League\CommonMark\Renderer\ChildNodeRendererInterface;
use League\CommonMark\Renderer\NodeRendererInterface;
use League\CommonMark\Util\HtmlElement;
use League\Config\ConfigurationAwareInterface;
use League\Config\ConfigurationInterface;

class CodeGroupsRenderer implements ConfigurationAwareInterface, NodeRendererInterface
{
    private ConfigurationInterface $config;

    public function setConfiguration(ConfigurationInterface $configuration): void
    {
        $this->config = $configuration;
    }

    public function render(Node $node, ChildNodeRendererInterface $childRenderer): string
    {
        if (! ($node instanceof CodeGroupsBlock)) {
            throw new InvalidArgumentException('错误的节点类型');
        }

        $codeBlocks = $node->getCodeBlocks();

        // 生成选项卡HTML
        $tabs = array_map(fn ($block, $index) => new HtmlElement('button',
            [
                'class' => $this->getClassName('code-group-tab').
                    ($index === 0 ? ' '.$this->getClassName('code-group-tab-active') : ''),
            ],
            $block['title'] ?: $block['language']
        ), $codeBlocks, array_keys($codeBlocks));

        // 生成代码块内容
        $contents = array_map(function ($block, $index) use ($childRenderer) {
            // 创建代码块容器
            $containerClasses = [
                $this->getClassName('code-group-content'),
            ];
            if ($index === 0) {
                $containerClasses[] = $this->getClassName('code-group-content-active');
            }

            // 使用 renderNodes 渲染代码块及其子节点
            $codeContent = $childRenderer->renderNodes([$block['code']]);

            return new HtmlElement(
                'div',
                [
                    'class' => implode(' ', $containerClasses),
                ],
                $codeContent
            );
        }, $codeBlocks, array_keys($codeBlocks));

        // 组合最终的HTML
        return new HtmlElement(
            'div',
            ['class' => $this->getClassName('code-group')],
            new HtmlElement(
                'div',
                ['class' => $this->getClassName('code-group-tabs')],
                implode('', $tabs)
            ).
            new HtmlElement(
                'div',
                ['class' => $this->getClassName('code-group-contents')],
                implode('', $contents)
            )
        );
    }

    private function getClassName(string $key): string
    {
        $className = $this->config->get("code_groups.class_name.{$key}");

        return $className ?? $key; // 如果配置值为 null，返回默认的键名
    }
}
