<?php

declare(strict_types=1);

namespace Vendor\ContentGridBundle\Dca;

use Contao\DataContainer;
use VtxmH\ContaoDcaHelpers\Dca\ArticleOptionsProvider;
use VtxmH\ContaoDcaHelpers\Dca\CurrentFieldValueResolver;

class ContentGridOptions
{
    public static function getArticles(?DataContainer $dc = null): array
    {
        if (null === $dc) {
            return [];
        }

        return ArticleOptionsProvider::getArticlesForPage(
            CurrentFieldValueResolver::resolveInt($dc, 'tl_content', 'cgPage')
        );
    }
}
