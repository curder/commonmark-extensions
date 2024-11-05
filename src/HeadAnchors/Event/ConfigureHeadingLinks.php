<?php

namespace Curder\CommonmarkExtensions\HeadAnchors\Event;

use League\CommonMark\Event\DocumentParsedEvent;
use League\CommonMark\Extension\CommonMark\Node\Block\Heading;
use League\CommonMark\Extension\CommonMark\Node\Inline\HtmlInline;
use League\CommonMark\Node\Block\Paragraph;

/**
 * `<a name="heading-1"><a>\n# Heading 1` => `<h1 id="heading-1"><a href="#heading-1">Heading 1</a></h1>`
 */
class ConfigureHeadingLinks
{
    public function __invoke(DocumentParsedEvent $documentParsedEvent): void
    {
        $walker = $documentParsedEvent->getDocument()->walker();

        while ($event = $walker->next()) {
            [$paragraph, $heading, $linkOpener, $linkCloser] = [
                $event->getNode(),
                $event->getNode()->next(),
                $event->getNode()->firstChild(),
                $event->getNode()->lastChild(),
            ];

            if (
                ! $heading instanceof Heading ||
                ! $paragraph instanceof Paragraph ||
                count($paragraph->children()) !== 2 ||
                ! $linkOpener instanceof HtmlInline ||
                ! $linkCloser instanceof HtmlInline ||
                ! str_starts_with($linkOpener->getLiteral(), '<a name="') ||
                ! str_ends_with($linkOpener->getLiteral(), '">') ||
                substr_count($linkOpener->getLiteral(), '"') !== 2 ||
                $paragraph->firstChild()->next() !== $paragraph->lastChild() ||
                ! str_starts_with($linkCloser->getLiteral(), '</a>') ||
                ! $heading->next()
            ) {
                continue;
            }

            $literal = $linkOpener->getLiteral();
            $afterPrefix = substr($literal, strpos($literal, '<a name="') + strlen('<a name="'));
            $link = substr($afterPrefix, 0, strrpos($afterPrefix, '">'));

            $heading->data->set('attributes.id', $link);
            $heading->prependChild(new HtmlInline('<a href="#'.$link.'">'));
            $heading->appendChild(new HtmlInline('</a>'));

            $paragraph->detach();

            $walker->resumeAt($heading->next());
        }
    }
}
