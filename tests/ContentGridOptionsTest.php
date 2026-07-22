<?php

namespace Contao {
    class DataContainer
    {
        public $id;
        public $activeRecord;

        public function __construct(int $id = 0, $activeRecord = null)
        {
            $this->id = $id;
            $this->activeRecord = $activeRecord;
        }
    }

    class TestState
    {
        public static array $post = [];
        public static array $get = [];
        public static array $pages = [];
        public static array $articlesByPage = [];
        public static array $contentRows = [];
        public static ?string $lastArticleOrder = null;

        public static function reset(): void
        {
            self::$post = [];
            self::$get = [];
            self::$pages = [
                10 => ['title' => 'Page Ten', 'alias' => 'page-ten'],
                20 => ['title' => 'Page Twenty', 'alias' => 'page-twenty'],
                30 => ['title' => 'Page Thirty', 'alias' => 'page-thirty'],
            ];
            self::$articlesByPage = [
                10 => [
                    ['id' => 102, 'title' => '', 'sorting' => 10],
                    ['id' => 101, 'title' => 'First Grid Article', 'sorting' => 10],
                    ['id' => 301, 'title' => 'Later Grid Article', 'sorting' => 20],
                ],
                20 => [
                    ['id' => 201, 'title' => 'Changed Grid Article', 'sorting' => 10],
                ],
                30 => [
                    ['id' => 401, 'title' => 'Database Grid Article', 'sorting' => 10],
                ],
            ];
            self::$contentRows = [
                55 => ['cgPage' => 30],
            ];
            self::$lastArticleOrder = null;
        }
    }

    class Input
    {
        public static function post(string $key)
        {
            return TestState::$post[$key] ?? null;
        }

        public static function get(string $key)
        {
            return TestState::$get[$key] ?? null;
        }
    }

    class Database
    {
        public static function getInstance(): self
        {
            return new self();
        }

        public function prepare(string $query): DatabaseStatement
        {
            return new DatabaseStatement($query);
        }
    }

    class DatabaseStatement
    {
        private string $query;

        public function __construct(string $query)
        {
            $this->query = $query;
        }

        public function limit(int $limit): self
        {
            return $this;
        }

        public function execute(int $id): DatabaseResult
        {
            if ('SELECT cgPage FROM tl_content WHERE id=?' !== $this->query) {
                return new DatabaseResult(null);
            }

            return new DatabaseResult(TestState::$contentRows[$id] ?? null);
        }
    }

    class DatabaseResult
    {
        public int $numRows = 0;
        public $cgPage;

        public function __construct(?array $row)
        {
            if (null === $row || !array_key_exists('cgPage', $row)) {
                return;
            }

            $this->numRows = 1;
            $this->cgPage = $row['cgPage'];
        }
    }

    class PageModel
    {
        public static function findByPk(int $id)
        {
            if (!isset(TestState::$pages[$id])) {
                return null;
            }

            return (object) TestState::$pages[$id];
        }
    }

    class ArticleModel
    {
        public static function findBy(string $field, int $pageId, array $options = [])
        {
            TestState::$lastArticleOrder = $options['order'] ?? null;

            if ('pid' !== $field || empty(TestState::$articlesByPage[$pageId])) {
                return null;
            }

            $rows = TestState::$articlesByPage[$pageId];

            usort(
                $rows,
                static fn (array $a, array $b): int => [$a['sorting'], $a['id']] <=> [$b['sorting'], $b['id']]
            );

            return array_map(static fn (array $row): object => (object) $row, $rows);
        }
    }
}

namespace {
    require_once __DIR__ . '/../../contao-dca-helpers/src/Dca/CurrentFieldValueResolver.php';
    require_once __DIR__ . '/../../contao-dca-helpers/src/Dca/ArticleOptionsProvider.php';
    require_once __DIR__ . '/../src/Dca/ContentGridOptions.php';

    use Contao\DataContainer;
    use Contao\TestState;
    use Vendor\ContentGridBundle\Dca\ContentGridOptions;

    function assertSameContentGridValue($expected, $actual, string $message): void
    {
        if ($expected !== $actual) {
            fwrite(STDERR, $message . PHP_EOL);
            fwrite(STDERR, 'Expected: ' . var_export($expected, true) . PHP_EOL);
            fwrite(STDERR, 'Actual:   ' . var_export($actual, true) . PHP_EOL);
            exit(1);
        }
    }

    TestState::reset();
    TestState::$post = ['FORM_SUBMIT' => 'tl_content', 'cgPage' => '10'];

    assertSameContentGridValue(
        [
            101 => 'First Grid Article (ID 101)',
            102 => 'Article ID 102 (ID 102)',
            301 => 'Later Grid Article (ID 301)',
        ],
        ContentGridOptions::getArticles(new DataContainer(0)),
        'A new unsaved tl_content record must populate grid articles from the submitted page.'
    );

    assertSameContentGridValue(
        'sorting ASC, id ASC',
        TestState::$lastArticleOrder,
        'Content Grid must use deterministic shared article sorting.'
    );

    TestState::reset();

    assertSameContentGridValue(
        [
            101 => 'First Grid Article (ID 101)',
            102 => 'Article ID 102 (ID 102)',
            301 => 'Later Grid Article (ID 301)',
        ],
        ContentGridOptions::getArticles(new DataContainer(44, (object) ['cgPage' => 10])),
        'An existing saved tl_content record must continue to use activeRecord fallback.'
    );

    TestState::reset();
    TestState::$post = ['FORM_SUBMIT' => 'tl_content', 'cgPage' => '20'];

    assertSameContentGridValue(
        [
            201 => 'Changed Grid Article (ID 201)',
        ],
        ContentGridOptions::getArticles(new DataContainer(44, (object) ['cgPage' => 10])),
        'Changing cgPage before saving must refresh cgArticle options from the submitted page.'
    );

    TestState::reset();
    TestState::$post = ['FORM_SUBMIT' => 'tl_content', 'cgPage_44' => '20'];

    assertSameContentGridValue(
        [
            201 => 'Changed Grid Article (ID 201)',
        ],
        ContentGridOptions::getArticles(new DataContainer(44, (object) ['cgPage' => 10])),
        'Suffixed Contao cgPage fields must refresh article options when unsuffixed cgPage is absent.'
    );

    TestState::reset();

    assertSameContentGridValue(
        [
            401 => 'Database Grid Article (ID 401)',
        ],
        ContentGridOptions::getArticles(new DataContainer(55)),
        'The saved tl_content database row must be used when activeRecord is unavailable.'
    );

    TestState::reset();
    TestState::$post = ['FORM_SUBMIT' => 'tl_module', 'cgPage' => '20'];

    assertSameContentGridValue(
        [
            101 => 'First Grid Article (ID 101)',
            102 => 'Article ID 102 (ID 102)',
            301 => 'Later Grid Article (ID 301)',
        ],
        ContentGridOptions::getArticles(new DataContainer(44, (object) ['cgPage' => 10])),
        'Unrelated POST requests must not affect tl_content grid article options.'
    );

    TestState::reset();
    TestState::$post = ['FORM_SUBMIT' => 'tl_content', 'cgPage' => ''];

    assertSameContentGridValue(
        [],
        ContentGridOptions::getArticles(new DataContainer(0)),
        'An empty page ID must return no grid article options.'
    );

    TestState::reset();
    TestState::$post = ['FORM_SUBMIT' => 'tl_content', 'cgPage' => '99'];

    assertSameContentGridValue(
        [],
        ContentGridOptions::getArticles(new DataContainer(0)),
        'An invalid page ID must return no grid article options.'
    );

    TestState::reset();
    TestState::$post = [
        'FORM_SUBMIT' => 'tl_content',
        'cgPage_11' => '10',
        'cgPage_12' => '20',
    ];

    assertSameContentGridValue(
        [
            101 => 'First Grid Article (ID 101)',
            102 => 'Article ID 102 (ID 102)',
            301 => 'Later Grid Article (ID 301)',
        ],
        ContentGridOptions::getArticles(new DataContainer(11)),
        'Multiple tl_content records in one request must use the current record cgPage.'
    );

    assertSameContentGridValue(
        [
            201 => 'Changed Grid Article (ID 201)',
        ],
        ContentGridOptions::getArticles(new DataContainer(12)),
        'Multiple callbacks in one request must resolve independently.'
    );

    assertSameContentGridValue(
        [],
        ContentGridOptions::getArticles(null),
        'A missing DataContainer must still return no options.'
    );

    echo 'ContentGridOptionsTest passed.' . PHP_EOL;
}
