<?php

namespace Curder\CommonmarkExtensions\CustomContainers\Block;

use League\CommonMark\Node\Block\AbstractBlock;
use League\Config\ConfigurationInterface;
use RuntimeException;

class CustomContainerBlock extends AbstractBlock
{
    public function __construct(
        private readonly string $type,
        private readonly string $title,
        private readonly ?bool $open,
        private readonly ?ConfigurationInterface $config = null
    ) {
        parent::__construct();
    }

    public function getConfig(): ConfigurationInterface
    {
        if (! $this->config) {
            throw new RuntimeException('Configuration is not set');
        }

        return $this->config;
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function isOpen(): bool
    {
        return $this->open ?? false;
    }
}
