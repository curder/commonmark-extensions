<?php

namespace Curder\CommonmarkExtensions\CustomContainers\Parser;

use League\CommonMark\Parser\Block\BlockStart;
use League\CommonMark\Parser\Block\BlockStartParserInterface;
use League\CommonMark\Parser\Cursor;
use League\CommonMark\Parser\MarkdownParserStateInterface;
use League\Config\ConfigurationInterface;

class CustomContainerStartParser implements BlockStartParserInterface
{
    public function __construct(private readonly ConfigurationInterface $config) {}

    public function tryStart(Cursor $cursor, MarkdownParserStateInterface $parserState): ?BlockStart
    {
        if ($cursor->isIndented() || ! $cursor->match('/^:::/')) {
            return BlockStart::none();
        }

        $type = $cursor->getRemainder();

        $matches = [];

        if (! preg_match('/^(details|tip|warning|danger|info)(?:\s+([^{]+?))?(?:\s+\{([^}]+)\})?(?:\s+([^{]+?))?$/', trim($type), $matches)) {
            return BlockStart::none();
        }

        $type = $matches[1];
        $title = $this->getTitle($type, $matches[2] ?? null);

        $isOpen = $this->getIsOpen($matches[3] ?? null);

        $container = $this->createContainer($type, $title, $isOpen);

        $cursor->advanceToEnd();

        return BlockStart::of($container)->at($cursor);
    }

    private function getTitle(string $type, ?string $matchTitle = null): string
    {
        $default = $this->config->get('custom_containers.labels.'.$type) ?? strtoupper($type);

        if ($matchTitle) {
            return $matchTitle;
        }

        return $default;
    }

    private function getIsOpen(?string $matchOpen = null): bool
    {
        $default = $this->config->get('custom_containers.details.open') ?? false;

        if ($matchOpen) {
            return $matchOpen === 'open';
        }

        return $default;
    }

    private function createContainer(string $type, string $title, bool $isOpen): CustomContainerParser
    {
        return new CustomContainerParser($type, $title, $isOpen, $this->config);
    }
}
