<?php

namespace Curder\CommonmarkExtensions\CodeGroups\Parser;

use League\CommonMark\Parser\Block\BlockStart;
use League\CommonMark\Parser\Block\BlockStartParserInterface;
use League\CommonMark\Parser\Cursor;
use League\CommonMark\Parser\MarkdownParserStateInterface;

class CodeGroupsStartParser implements BlockStartParserInterface
{
    public function tryStart(Cursor $cursor, MarkdownParserStateInterface $parserState): ?BlockStart
    {
        // 检查是否匹配代码组的开始标记 ":::"
        if ($cursor->match('/^:::\s*code-group/')) {
            return BlockStart::of(new CodeGroupsParser)->at($cursor);
        }

        return null;
    }
}
