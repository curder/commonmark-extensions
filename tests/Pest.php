<?php

use Curder\CommonmarkExtensions\CodeGroupsExtension;
use League\CommonMark\Environment\Environment;
use League\CommonMark\Extension\CommonMark\CommonMarkCoreExtension;
use League\CommonMark\MarkdownConverter;

function getCodeGroupsConverter(array $config = []): MarkdownConverter
{
    return getConverter([CodeGroupsExtension::class], $config);
}

function getConverter(array $extensions = [], array $config = []): MarkdownConverter
{
    $environment = new Environment($config);
    $environment->addExtension(new CommonMarkCoreExtension);
    foreach ($extensions as $extension) {
        $environment->addExtension(new $extension);
    }

    return new MarkdownConverter($environment);
}
