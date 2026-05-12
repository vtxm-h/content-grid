<?php

declare(strict_types=1);

namespace Vendor\ContentGridBundle\Dca;

use Contao\ArticleModel;
use Contao\DataContainer;

class ContentGridOptions
{
    public static function getArticles(?DataContainer $dc = null): array
    {
        $pageId = 0;

        if (null !== $dc && null !== $dc->activeRecord) {
            $pageId = (int) $dc->activeRecord->cgPage;
        }

        if ($pageId < 1) {
            return [];
        }

        $articles = ArticleModel::findBy('pid', $pageId, ['order' => 'sorting']);

        if (null === $articles) {
            return [];
        }

        $options = [];

        foreach ($articles as $article) {
            $title = trim((string) $article->title);

            if ('' === $title) {
                $title = sprintf('Article ID %d', (int) $article->id);
            }

            $options[(int) $article->id] = sprintf('%s (ID %d)', $title, (int) $article->id);
        }

        return $options;
    }
}
