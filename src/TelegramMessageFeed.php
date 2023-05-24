<?php

namespace App;

libxml_use_internal_errors(true);

class TelegramMessageFeed
{
    private \DOMXPath $domXpath;

    private \DOMDocument $domDocument;

    public function __construct(string $html)
    {
        $this->domDocument = new \DOMDocument();
        $this->domDocument->loadHTML(
            mb_convert_encoding($html, 'HTML-ENTITIES', 'UTF-8')
        );
        $this->domXpath = new \DOMXPath(
            $this->domDocument
        );
    }

    private function parseId(\DOMNode $node): int
    {
        $id = $this->domXpath->evaluate(
            'string(.//@data-post)', $node
        );
        return filter_var(
            $id, FILTER_SANITIZE_NUMBER_FLOAT
        );
    }

    private function parsePublishedAt(\DOMNode $node): \DateTimeImmutable
    {
        $datetime = $this->domXpath->evaluate(
            'string(.//time/@datetime)', $node
        );

        return new \DateTimeImmutable($datetime);
    }

    private function parseText(\DOMNode $node): string
    {
        $node = $this->domXpath->evaluate(
            './/*[contains(concat(" ", @class, " "), " tgme_widget_message_text ")]', $node
        )->item(0);

        $html = $node->ownerDocument->saveHTML($node);

        return strip_tags($html, '<b><i><a><br>');
    }

    private function parseKeyboards(\DOMNode $node): array
    {
        $nodes = $this->domXpath->evaluate(
            './/*[contains(concat(" ", @class, " "), " tgme_widget_message_inline_button ")]', $node
        );

        $keyboards = [];
        foreach ($nodes as $node) {
            ;
            $keyboards[] = [
                'text' => $this->domXpath->evaluate('string(.)', $node),
                'link' => $this->domXpath->evaluate('string(./@href)', $node),
            ];
        }
        return $keyboards;
    }

    private function parseViewsCount(\DOMNode $node): int
    {
        $viewsCount = $this->domXpath->evaluate(
            'string(.//*[contains(concat(" ", @class, " "), " tgme_widget_message_views ")])', $node
        );

        if (str_ends_with($viewsCount, 'K')) {
            $viewsCount = filter_var(
                $viewsCount, FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION
            );
            $viewsCount = $viewsCount * 1000;
        }

        return $viewsCount ?: 0;
    }

    private function parsePreviewLink(\DOMNode $node): string
    {
        return $this->domXpath->evaluate(
            'string(.//*[contains(concat(" ", @class, " "), " tgme_widget_message_link_preview ")]/@href)', $node
        );
    }

    public function getPreviousPageUrl(): string
    {
        return $this->domXpath->evaluate(
            'string(//*[contains(concat(" ", @class, " "), " tme_messages_more ")]/@href)'
        );
    }

    public function getMessages(): \Generator
    {
        $nodes = $this->domXpath->query(
            '//*[contains(concat(" ", @class, " "), " tgme_widget_message_wrap ")]'
        );
        foreach ($nodes as $node) {
            yield new TelegramMessage(
                id: $this->parseId($node),
                text: $this->parseText($node),
                viewsCount: $this->parseViewsCount($node),
                previewLink: $this->parsePreviewLink($node),
                keyboards: $this->parseKeyboards($node),
                publishedAt: $this->parsePublishedAt($node),
            );
        }
    }
}