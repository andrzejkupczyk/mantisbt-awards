<tr class="bugnote-awards">
  <td colspan="2">
    <div
      class="padding-2"
      hx-boost="true"
      hx-target="closest .bugnote-awards"
      hx-swap="outerHTML"
    >
      <?php
      $defaults = ['voters_ids' => '', 'voters_usernames' => '', 'total' => 0];

      foreach (plugin_config_get('emojis') as $codePoint => $emoji):
        $castedVotes = $votes[$bugnoteId][$codePoint] ?? $defaults;
        $currentUserVoted = in_array(auth_get_current_user_id(), explode(',', $castedVotes['voters_ids']));
        ?>
        <a
          title="<?= string_html_entities($castedVotes['voters_usernames']) ?>"
          class="btn <?= $currentUserVoted ? 'btn-primary' : '' ?> btn-xs btn-white btn-round"
          <?php if (!current_user_is_anonymous()): ?>
          hx-post="<?= plugin_api_url('votes') ?>"
          hx-vals='<?= json_encode(['bugnote_id' => $bugnoteId, 'emoji' => $codePoint]) ?>'
          <?php endif; ?>
        >
          <?= $emoji ?>
          <span class="bugnote-award-emojis"><?= $castedVotes['total'] ?></span>
        </a>
      <?php endforeach; ?>
    </div>
  </td>
</tr>
