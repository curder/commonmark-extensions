<?php

namespace Curder\CommonmarkExtensions\CodeGroups\Block;

use League\CommonMark\Extension\CommonMark\Node\Block\FencedCode;
use League\CommonMark\Node\Block\AbstractBlock;

class CodeGroupsBlock extends AbstractBlock
{
    /**
     * @var array 存储代码块信息
     */
    private array $codeBlocks = [];

    /**
     * 添加代码块
     *
     * @param  string  $language  语言
     * @param  FencedCode  $code  代码块节点
     * @param  string  $title  标题
     * @param  array  $highlights  高亮行
     * @param  array  $focuses  聚焦行
     * @param  array  $additions  添加的行
     * @param  array  $deletions  删除的行
     */
    public function addCodeBlock(
        string $language,
        FencedCode $code,
        string $title = '',
        array $highlights = [],
        array $focuses = [],
        array $additions = [],
        array $deletions = []
    ): void {
        $this->codeBlocks[] = [
            'language' => $language,
            'code' => $code,
            'title' => $title,
            'highlights' => $highlights,
            'focuses' => $focuses,
            'additions' => $additions,
            'deletions' => $deletions,
        ];
    }

    /**
     * 获取所有代码块
     */
    public function getCodeBlocks(): array
    {
        return $this->codeBlocks;
    }
}
