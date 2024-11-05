<?php

namespace Curder\CommonmarkExtensions\HeadAnchors\Event;

use League\CommonMark\Event\DocumentParsedEvent;
use League\CommonMark\Extension\CommonMark\Node\Block\Heading;
use League\CommonMark\Extension\CommonMark\Node\Inline\HtmlInline;
use League\CommonMark\Node\Inline\Text;

/**
 * 自定义锚点
 * `# Heading 1 {#heading-1}` => `<h1 id="heading-1"><a href="#heading-1">Heading 1</a></h1>`
 */
class CustomAnchors
{
    public function __invoke(DocumentParsedEvent $event): void
    {
        $document = $event->getDocument();
        $walker = $document->walker();

        while ($event = $walker->next()) {
            $node = $event->getNode();

            if (! $event->isEntering() || ! ($node instanceof Heading)) {
                continue;
            }

            // 获取标题内容
            $content = '';
            $children = [];
            foreach ($node->children() as $child) {
                if ($child instanceof Text) {
                    $content .= $child->getLiteral();
                }
                $children[] = $child;
            }

            // 检查是否包含自定义 ID，使用非贪婪匹配
            if (preg_match('/^(.+?)\s*\{#([^}]+)\}$/', trim($content), $matches)) {
                $text = trim($matches[1]);
                $id = trim($matches[2]);

                // 清除原有内容
                foreach ($children as $child) {
                    $child->detach();
                }

                // 设置 id 属性
                $node->data->set('attributes/id', $id);

                // 创建链接结构
                $link = new HtmlInline('<a href="#'.$id.'">');
                $textNode = new Text($text);
                $linkEnd = new HtmlInline('</a>');

                // 按顺序添加节点
                $node->appendChild($link);
                $node->appendChild($textNode);
                $node->appendChild($linkEnd);

                // 跳过已处理的节点
                $walker->resumeAt($node);
            }
        }
    }
}
