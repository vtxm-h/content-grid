<?php

use Vendor\ContentGridBundle\Dca\ContentGridOptions;

$GLOBALS['TL_DCA']['tl_content']['palettes']['vtxm_content_grid'] = '{type_legend},type,headline;{source_legend},cgPage,cgArticle;{grid_legend},cgColumns,cgGap,cgAlign,cgStackMobile;{protected_legend:hide},protected;{expert_legend:hide},guests,cssID,space;{invisible_legend:hide},invisible,start,stop';

$GLOBALS['TL_DCA']['tl_content']['fields']['cgPage'] = [
    'exclude' => true,
    'inputType' => 'pageTree',
    'eval' => [
        'fieldType' => 'radio',
        'submitOnChange' => true,
        'tl_class' => 'clr',
    ],
    'sql' => "int(10) unsigned NOT NULL default '0'",
];

$GLOBALS['TL_DCA']['tl_content']['fields']['cgArticle'] = [
    'exclude' => true,
    'foreignKey' => 'tl_article.title',
    'inputType' => 'select',
    'options_callback' => [ContentGridOptions::class, 'getArticles'],
    'eval' => [
        'includeBlankOption' => true,
        'chosen' => true,
        'tl_class' => 'w50',
    ],
    'sql' => "int(10) unsigned NOT NULL default '0'",
    'relation' => ['type' => 'hasOne', 'load' => 'lazy'],
];

$GLOBALS['TL_DCA']['tl_content']['fields']['cgColumns'] = [
    'exclude' => true,
    'default' => '3',
    'inputType' => 'select',
    'options' => ['2', '3', '4'],
    'reference' => &$GLOBALS['TL_LANG']['tl_content']['cgColumnsOptions'],
    'eval' => [
        'tl_class' => 'w50',
    ],
    'sql' => "varchar(1) NOT NULL default '3'",
];

$GLOBALS['TL_DCA']['tl_content']['fields']['cgGap'] = [
    'exclude' => true,
    'default' => 'medium',
    'inputType' => 'select',
    'options' => ['small', 'medium', 'large'],
    'reference' => &$GLOBALS['TL_LANG']['tl_content']['cgGapOptions'],
    'eval' => [
        'tl_class' => 'w50',
    ],
    'sql' => "varchar(16) NOT NULL default 'medium'",
];

$GLOBALS['TL_DCA']['tl_content']['fields']['cgAlign'] = [
    'exclude' => true,
    'default' => 'stretch',
    'inputType' => 'select',
    'options' => ['start', 'center', 'stretch'],
    'reference' => &$GLOBALS['TL_LANG']['tl_content']['cgAlignOptions'],
    'eval' => [
        'tl_class' => 'w50',
    ],
    'sql' => "varchar(16) NOT NULL default 'stretch'",
];

$GLOBALS['TL_DCA']['tl_content']['fields']['cgStackMobile'] = [
    'exclude' => true,
    'default' => '1',
    'inputType' => 'checkbox',
    'eval' => [
        'tl_class' => 'w50 m12',
    ],
    'sql' => "char(1) NOT NULL default '1'",
];
