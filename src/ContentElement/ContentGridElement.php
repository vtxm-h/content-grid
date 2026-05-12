<?php

declare(strict_types=1);

namespace Vendor\ContentGridBundle\ContentElement;

use Contao\ContentElement;
use Contao\ContentModel;
use Contao\Controller;
use Contao\StringUtil;

class ContentGridElement extends ContentElement
{
    protected $strTemplate = 'content_grid';

    /**
     * @var array<int, true>
     */
    private static $articleStack = [];

    public function generate()
    {
        $articleId = (int) ($this->cgArticle ?: 0);

        if ($articleId > 0 && isset(self::$articleStack[$articleId])) {
            return '';
        }

        return parent::generate();
    }

    protected function compile()
    {
        $this->setTemplateOptions();

        $articleId = (int) ($this->cgArticle ?: 0);

        $this->Template->items = [];

        if ($articleId < 1 || isset(self::$articleStack[$articleId])) {
            return;
        }

        self::$articleStack[$articleId] = true;

        try {
            $elements = ContentModel::findPublishedByPidAndTable($articleId, 'tl_article');

            if (null === $elements) {
                return;
            }

            $items = [];

            foreach ($elements as $element) {
                $content = (string) Controller::getContentElement($element->id);

                if ('' === trim($content)) {
                    continue;
                }

                $items[] = $content;
            }

            $this->Template->items = $items;
        } finally {
            unset(self::$articleStack[$articleId]);
        }
    }

    private function setTemplateOptions(): void
    {
        [$elementId, $elementClass] = $this->getCssIdParts();

        $columns = $this->normalizeOption((string) ($this->cgColumns ?: '3'), ['2', '3', '4'], '3');
        $gap = $this->normalizeOption((string) ($this->cgGap ?: 'medium'), ['small', 'medium', 'large'], 'medium');
        $align = $this->normalizeOption((string) ($this->cgAlign ?: 'stretch'), ['start', 'center', 'stretch'], 'stretch');

        $classes = [
            'cg--cols-'.$columns,
            'cg--gap-'.$gap,
            'cg--align-'.$align,
        ];

        if ('1' === (string) $this->cgStackMobile) {
            $classes[] = 'cg--stack-mobile';
        }

        $headline = StringUtil::deserialize($this->headline, true);
        $headlineUnit = (string) ($headline['unit'] ?? 'h2');

        if (!\in_array($headlineUnit, ['h1', 'h2', 'h3', 'h4', 'h5', 'h6'], true)) {
            $headlineUnit = 'h2';
        }

        $this->Template->elementId = $elementId;
        $this->Template->elementClass = $elementClass;
        $this->Template->gridClasses = implode(' ', $classes);
        $this->Template->headlineText = trim((string) ($headline['value'] ?? ''));
        $this->Template->headlineUnit = $headlineUnit;
    }

    /**
     * @return array{0: string, 1: string}
     */
    private function getCssIdParts(): array
    {
        $cssId = $this->arrData['cssID'] ?? '';

        if (!\is_array($cssId)) {
            $cssId = StringUtil::deserialize($cssId, true);
        }

        return [
            trim((string) ($cssId[0] ?? '')),
            trim((string) ($cssId[1] ?? '')),
        ];
    }

    /**
     * @param list<string> $allowed
     */
    private function normalizeOption(string $value, array $allowed, string $default): string
    {
        return \in_array($value, $allowed, true) ? $value : $default;
    }
}
