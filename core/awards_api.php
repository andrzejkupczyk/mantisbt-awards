<?php

/**
 * @return void
 */
function cast_bugnote_vote(int $bugnote_id, int $emoji, int $voter_id = null)
{
    $table = plugin_table('bugnote_vote');
    $voter_id = $voter_id ?: auth_get_current_user_id();

    $currentVoteQuery = db_query(
        "SELECT id FROM {$table} WHERE bugnote_id = ? AND voter_id = ? AND emoji = ?",
        [$bugnote_id, $voter_id, $emoji]
    );

    if ($currentVote = db_fetch_array($currentVoteQuery)) {
        db_query("DELETE FROM {$table} WHERE id = ?", [$currentVote['id']]);

        return;
    }

    db_query(
        "INSERT INTO {$table} (bugnote_id, voter_id, emoji) VALUES (?, ?, ?)",
        [$bugnote_id, $voter_id, $emoji]
    );
}

/**
 * @param int|string|array $bugnote_ids
 */
function fetch_bugnote_votes($bugnote_ids, int $emoji = null): array
{
    if (empty($bugnote_ids)) {
        return [];
    }

    $table = plugin_table('bugnote_vote');
    $sql = <<<SQL
SELECT
    bugnote_id,
    emoji,
    count(voter_id) AS total,
    GROUP_CONCAT({user}.id) AS voters_ids,
    GROUP_CONCAT({user}.username ORDER BY {user}.username ASC SEPARATOR ', ') AS voters_usernames
FROM {$table}
LEFT JOIN {user} ON {user}.id = voter_id 
WHERE bugnote_id IN :bugnote_id
SQL;

    if ($emoji) {
        $sql .= ' AND emoji = :emoji';
    }

    $sql .= ' GROUP BY bugnote_id, emoji';

    $query = new DBQuery($sql, [
        'bugnote_id' => array_map('intval', (array) $bugnote_ids),
        'emoji' => $emoji,
    ]);

    $results = [];
    foreach ($query->fetch_all() ?: [] as $result) {
        $bugnote_id = $result['bugnote_id'];
        $bugnote_vote = $result['emoji'];
        $results[$bugnote_id][$bugnote_vote] = [
            'total' => $results[$bugnote_id][$bugnote_vote] ?? (int) $result['total'],
            'voters_ids' => $result['voters_ids'],
            'voters_usernames' => $result['voters_usernames'],
        ];
    }

    return $results;
}

/**
 * @return void
 */
function reject_bugnote_votes(int $bugnote_id)
{
    db_query(
        'DELETE FROM ' . plugin_table('bugnote_vote') . ' WHERE bugnote_id = ?',
        [$bugnote_id]
    );
}
