<?php

namespace Curder\CommonmarkExtensions\CodeGroups\Parser;

use Curder\CommonmarkExtensions\CodeGroups\Block\CodeGroupsBlock;
use League\CommonMark\Extension\CommonMark\Node\Block\FencedCode;
use League\CommonMark\Node\Block\AbstractBlock;
use League\CommonMark\Parser\Block\AbstractBlockContinueParser;
use League\CommonMark\Parser\Block\BlockContinue;
use League\CommonMark\Parser\Block\BlockContinueParserInterface;
use League\CommonMark\Parser\Cursor;

class CodeGroupsParser extends AbstractBlockContinueParser
{
    private const FENCE_CHAR = '`';

    private const FENCE_LENGTH = 3;

    private const FENCE_OFFSET = 0;

    private const CODE_GROUP_START = ':::';

    private const CODE_BLOCK_PATTERN = '/^```(\w+)(?:{([^}]*)})?(?:{([^}]*)})?(?:\s+\[(.*?)\])?$/';

    private CodeGroupsBlock $block;

    private bool $inCodeBlock = false;

    private ?string $currentLanguage = null;

    private ?string $currentTitle = null;

    private string $currentCode = '';

    private array $currentHighlights = [];

    private array $currentFocuses = [];

    private array $currentAdditions = [];

    private array $currentDeletions = [];

    public function __construct()
    {
        $this->block = new CodeGroupsBlock();
    }

    public function getBlock(): AbstractBlock
    {
        return $this->block;
    }

    public function tryContinue(Cursor $cursor, BlockContinueParserInterface $activeBlockParser): ?BlockContinue
    {
        $line = $cursor->getLine();
        $trimmedLine = trim($line);

        if ($this->isCodeGroupEnd($trimmedLine)) {
            $this->finishCurrentCodeBlockIfNeeded();

            return BlockContinue::finished();
        }

        if ($this->tryStartCodeBlock($trimmedLine)) {
            return BlockContinue::at($cursor);
        }

        if ($this->isCodeBlockEnd($trimmedLine)) {
            $this->finishCurrentCodeBlock();

            return BlockContinue::at($cursor);
        }

        if ($this->inCodeBlock) {
            $this->currentCode .= $line."\n";
        }

        return BlockContinue::at($cursor);
    }

    private function isCodeGroupEnd(string $line): bool
    {
        return $line === self::CODE_GROUP_START;
    }

    private function isCodeBlockEnd(string $line): bool
    {
        return $this->inCodeBlock && $line === str_repeat(self::FENCE_CHAR, self::FENCE_LENGTH);
    }

    private function tryStartCodeBlock(string $line): bool
    {
        if (! preg_match(self::CODE_BLOCK_PATTERN, $line, $matches)) {
            return false;
        }

        $this->finishCurrentCodeBlockIfNeeded();
        $this->startNewCodeBlock($matches);

        return true;
    }

    private function finishCurrentCodeBlockIfNeeded(): void
    {
        if ($this->inCodeBlock) {
            $this->finishCurrentCodeBlock();
        }
    }

    private function startNewCodeBlock(array $matches): void
    {
        $this->inCodeBlock = true;
        $this->currentLanguage = $matches[1];
        $this->currentTitle = isset($matches[4]) ? htmlspecialchars($matches[4], ENT_QUOTES | ENT_HTML5) : null;
        $this->currentCode = '';

        // 解析高亮行（第一个花括号）
        $this->currentHighlights = isset($matches[2])
            ? $this->parseLineMarkers($matches[2])
            : [];

        // 解析聚焦行（第二个花括号）
        $this->currentFocuses = isset($matches[3])
            ? $this->parseLineMarkers($matches[3])
            : [];
    }

    private function finishCurrentCodeBlock(): void
    {
        if ($this->currentLanguage === null) {
            return;
        }

        $codeBlock = $this->createCodeBlock();
        $this->addCodeBlockToGroup($codeBlock);
        $this->resetCurrentState();
    }

    private function createCodeBlock(): FencedCode
    {
        $codeBlock = new FencedCode(self::FENCE_LENGTH, self::FENCE_CHAR, self::FENCE_OFFSET);
        $codeBlock->setInfo($this->buildLanguageInfo());
        $codeBlock->setLiteral(trim($this->currentCode));

        return $codeBlock;
    }

    private function buildLanguageInfo(): string
    {
        $parts = [$this->currentLanguage];

        // 只在有高亮行时添加花括号
        if (! empty($this->currentHighlights)) {
            $parts[] = '{'.$this->formatLineNumbers($this->currentHighlights).'}';
        }

        // 只在有聚焦行时添加花括号
        if (! empty($this->currentFocuses)) {
            $parts[] = '{'.$this->formatLineNumbers($this->currentFocuses).'}';
        }

        // 添加新增行
        if (! empty($this->currentAdditions)) {
            $parts[] = '{'.$this->formatLineNumbers($this->currentAdditions).'}[a]';
        }

        // 添加删除行
        if (! empty($this->currentDeletions)) {
            $parts[] = '{'.$this->formatLineNumbers($this->currentDeletions).'}[d]';
        }

        return implode('', $parts);
    }

    private function addCodeBlockToGroup(FencedCode $codeBlock): void
    {
        $this->block->addCodeBlock(
            $this->currentLanguage,
            $codeBlock,
            $this->currentTitle ?? '',
            $this->currentHighlights,
            $this->currentFocuses,
            $this->currentAdditions,
            $this->currentDeletions
        );
        $this->block->appendChild($codeBlock);
    }

    private function resetCurrentState(): void
    {
        $this->inCodeBlock = false;
        $this->currentLanguage = null;
        $this->currentTitle = null;
        $this->currentCode = '';
        $this->currentHighlights = [];
        $this->currentFocuses = [];
        $this->currentAdditions = [];
        $this->currentDeletions = [];
    }

    private function parseLineMarkers(string $input): array
    {
        return trim($input) !== '' ? $this->parseLineNumbers($input) : [];
    }

    private function formatLineNumbers(array $numbers): string
    {
        if (empty($numbers)) {
            return '';
        }

        sort($numbers);

        return implode(',', $this->buildRanges($numbers));
    }

    private function buildRanges(array $numbers): array
    {
        $ranges = [];
        $start = $end = $numbers[0];

        for ($i = 1; $i < count($numbers); $i++) {
            if ($numbers[$i] == $end + 1) {
                $end = $numbers[$i];
            } else {
                $ranges[] = $this->formatRange($start, $end);
                $start = $end = $numbers[$i];
            }
        }

        $ranges[] = $this->formatRange($start, $end);

        return $ranges;
    }

    private function formatRange(int $start, int $end): string
    {
        return $start === $end ? (string) $start : "{$start}-{$end}";
    }

    private function parseLineNumbers(string $input): array
    {
        if (empty($input)) {
            return [];
        }

        $numbers = [];
        foreach (explode(',', $input) as $part) {
            $part = trim($part);
            if (preg_match('/^(\d+)-(\d+)$/', $part, $matches)) {
                $numbers = array_merge(
                    $numbers,
                    range(
                        min((int) $matches[1], (int) $matches[2]),
                        max((int) $matches[1], (int) $matches[2])
                    )
                );
            } elseif (is_numeric($part)) {
                $numbers[] = (int) $part;
            }
        }

        return array_unique($numbers);
    }
}
