<?php

namespace Curder\CommonmarkExtensions\CustomContainers\Parser;

use Curder\CommonmarkExtensions\CustomContainers\Block\CustomContainerBlock;
use League\CommonMark\Node\Block\AbstractBlock;
use League\CommonMark\Parser\Block\AbstractBlockContinueParser;
use League\CommonMark\Parser\Block\BlockContinue;
use League\CommonMark\Parser\Block\BlockContinueParserInterface;
use League\CommonMark\Parser\Cursor;
use League\Config\ConfigurationInterface;

class CustomContainerParser extends AbstractBlockContinueParser implements BlockContinueParserInterface
{
    private CustomContainerBlock $block;

    public function __construct(
        private readonly string $type,
        private readonly string $title,
        private readonly ?bool $open,
        private readonly ConfigurationInterface $config,
    ) {
        $this->block = new CustomContainerBlock($this->type, $this->title, $this->open, $this->config);
    }

    public function tryContinue(Cursor $cursor, BlockContinueParserInterface $activeBlockParser): ?BlockContinue
    {
        if ($cursor->match('/^:::$/')) {
            return BlockContinue::finished();
        }

        return BlockContinue::at($cursor);
    }

    public function getBlock(): AbstractBlock
    {
        return $this->block;
    }

    public function isContainer(): bool
    {
        return true;
    }

    public function canContain($childBlock): bool
    {
        return true;
    }
}
