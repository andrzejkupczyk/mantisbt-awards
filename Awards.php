<?php

declare(strict_types=1);

use JetBrains\PhpStorm\ArrayShape;
use WebGarden\Termite\TermitePlugin;

require_once __DIR__ . '/../../vendor/autoload.php';

final class AwardsPlugin extends TermitePlugin
{
    /**
     * @var array
     */
    protected $emojis = [
        0x1F44D => '👍',
        0x1F44E => '👎',
    ];

    /**
     * @var array|null
     */
    protected $votes = null;

    public function register()
    {
        parent::register();

        $this->version = '1.0.0';
        $this->author = 'Andrzej Kupczyk';
        $this->contact = 'kontakt@andrzejkupczyk.pl';
        $this->url = 'https://github.com/andrzejkupczyk/mantisbt-awards';
    }

    public function hooks(): array
    {
        return [
            'EVENT_BUGNOTE_DELETED' => 'handleBugnoteDeleted',
            'EVENT_LAYOUT_RESOURCES' => 'handleLayoutResources',
            'EVENT_REST_API_ROUTES' => 'handleRestApiRoutes',
            'EVENT_VIEW_BUGNOTE' => 'handleViewBugnote',
            'EVENT_VIEW_BUGNOTES_START' => 'handleViewBugnotesStart',
        ];
    }

    public function schema(): array
    {
        return [
            ['CreateTableSQL', [
                plugin_table('bugnote_vote'),
                'id I UNSIGNED PRIMARY NOTNULL AUTOINCREMENT,
                bugnote_id I UNSIGNED NOTNULL,
                voter_id I UNSIGNED NOTNULL,
                emoji I UNSIGNED NOTNULL',
                ['mysql' => 'ENGINE=MyISAM DEFAULT CHARSET=utf8', 'pgsql' => 'WITHOUT OIDS'],
            ]],
            ['CreateIndexSQL', [
                'idx_bugnote_vote_bugnote_id',
                plugin_table('bugnote_vote'),
                'bugnote_id',
            ]],
            ['CreateIndexSQL', [
                'idx_bugnote_vote_voter_id_emoji',
                plugin_table('bugnote_vote'),
                'voter_id,emoji',
            ]],
        ];
    }

    public function config(): array
    {
        return [
            'emojis' => $this->emojis,
        ];
    }

    /**
     * @param int $bugnoteId
     *
     * @return void
     */
    public function displayBugnoteAwards($bugnoteId)
    {
        $votes = is_array($this->votes)
            ? $this->votes
            : fetch_bugnote_votes($bugnoteId);

        include __DIR__ . '/components/list.php';
    }

    /**
     * @param string $event
     * @param int $bugId
     * @param int $bugnoteId
     *
     * @return void
     */
    public function handleBugnoteDeleted($event, $bugId, $bugnoteId)
    {
        reject_bugnote_votes($bugnoteId);
    }

    /**
     * @return void
     */
    public function handleLayoutResources()
    {
        if (is_page_name('view.php')) {
            printf("\t<script src=\"%s\"></script>\n", plugin_file('htmx.min.js'));
        }
    }

    /**
     * @param array $payload
     * @param string $event
     *
     * @return void
     */
    public function handleRestApiRoutes(
        $event,
        #[ArrayShape(['app' => App::class])] $payload
    ) {
        $plugin = $this;

        $payload['app']->group(
            plugin_route_group(),
            function (App $app) use ($plugin) {
                $app->post('/votes', [$plugin, 'castVote']);
            }
        );
    }

    /**
     * @param string $event
     * @param int $bugId
     * @param int $bugnoteId
     * @param bool $isPrivate
     *
     * @return void
     */
    public function handleViewBugnote($event, $bugId, $bugnoteId, $isPrivate)
    {
        $this->displayBugnoteAwards($bugnoteId);
    }

    /**
     * @param string $event
     * @param int $bugId
     * @param \BugnoteData[] $bugnotes
     *
     * @return void
     */
    public function handleViewBugnotesStart($event, $bugId, $bugnotes)
    {
        $this->votes = fetch_bugnote_votes(array_column($bugnotes, 'id'));
    }

    /**
     * @param \Slim\Http\Request $request
     */
    public function castVote($request)
    {
        plugin_push_current($this->basename);
        $parameters = array_map('intval', $request->getParams(['bugnote_id', 'emoji']));

        if (!current_user_is_anonymous()) {
            cast_bugnote_vote(...$parameters);
        }

        $this->displayBugnoteAwards($parameters['bugnote_id']);
    }
}
