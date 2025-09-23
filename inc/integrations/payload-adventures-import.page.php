<?php
// Admin page template for Adventures Import
if (!defined('ABSPATH')) { exit; }
?>
<div class="wrap">
  <h1><?php echo esc_html__('Import Adventures (Payload → WP)', 'lovetravel-child'); ?></h1>
  <p class="description"><?php echo esc_html__('Import adventures from Payload CMS into the Adventure CPT. Uses paged API requests and applies taxonomy mappings (Duration, Difficulty, Month).', 'lovetravel-child'); ?></p>

  <div id="adventures-notices" aria-live="polite"></div>

  <div id="poststuff">
    <div id="post-body" class="metabox-holder columns-2">
      <div id="post-body-content">
        <div class="postbox">
          <h2 class="hndle"><span><?php echo esc_html__('Import Settings', 'lovetravel-child'); ?></span></h2>
          <div class="inside">
            <form id="adventures-import-form">
              <table class="form-table" role="presentation">
                <tbody>
                  <tr>
                    <th scope="row"><label for="limit"><?php echo esc_html__('Items per page', 'lovetravel-child'); ?></label></th>
                    <td>
                      <input type="number" min="1" max="100" id="limit" value="10" class="small-text" />
                      <p class="description"><?php echo esc_html__('How many adventures to process per page.', 'lovetravel-child'); ?></p>
                    </td>
                  </tr>
                  <tr>
                    <th scope="row">&nbsp;</th>
                    <td>
                      <label><input type="checkbox" id="overwrite-existing" /> <?php echo esc_html__('Overwrite existing posts', 'lovetravel-child'); ?></label><br />
                      <label><input type="checkbox" id="dry-run" /> <?php echo esc_html__('Dry-run (no writes, log only)', 'lovetravel-child'); ?></label>
                    </td>
                  </tr>
                </tbody>
              </table>
              <p class="submit">
                <button type="submit" class="button button-primary" id="start-import"><?php echo esc_html__('Start Import', 'lovetravel-child'); ?></button>
                <button type="button" class="button" id="stop-import" disabled><?php echo esc_html__('Stop', 'lovetravel-child'); ?></button>
              </p>
            </form>
          </div>
        </div>

        <div class="postbox">
          <h2 class="hndle"><span><?php echo esc_html__('Progress & Log', 'lovetravel-child'); ?></span></h2>
          <div class="inside">
            <div class="progress-bar" aria-hidden="true"><div id="progress-fill" style="width:0%"></div></div>
            <p>
              <strong><?php echo esc_html__('Pages done:', 'lovetravel-child'); ?></strong> <span id="pages-done">0</span> / <span id="pages-total">0</span>
              &nbsp;|&nbsp;
              <strong><?php echo esc_html__('Imported:', 'lovetravel-child'); ?></strong> <span id="count-imported">0</span>
              &nbsp;|&nbsp;
              <strong><?php echo esc_html__('Updated:', 'lovetravel-child'); ?></strong> <span id="count-updated">0</span>
              &nbsp;|&nbsp;
              <strong><?php echo esc_html__('Skipped:', 'lovetravel-child'); ?></strong> <span id="count-skipped">0</span>
              &nbsp;|&nbsp;
              <strong><?php echo esc_html__('Failed:', 'lovetravel-child'); ?></strong> <span id="count-failed">0</span>
            </p>
            <div id="import-log" class="import-log-box" aria-live="polite"></div>
            <p id="import-log-legend" class="description">
              Legend: 🚀 start · ✅ imported · ♻️ updated · ⏭️ skipped · ❌ failed · 🧪 dry-run
            </p>
          </div>
        </div>
      </div>

      <div id="postbox-container-1" class="postbox-container">
        <div class="postbox">
          <h2 class="hndle"><span><?php echo esc_html__('API Stats', 'lovetravel-child'); ?></span></h2>
          <div class="inside">
            <p>
              <strong><?php echo esc_html__('Total documents:', 'lovetravel-child'); ?></strong> <span id="total-docs">0</span><br/>
              <strong><?php echo esc_html__('Total pages:', 'lovetravel-child'); ?></strong> <span id="total-pages">0</span><br/>
              <strong><?php echo esc_html__('Current page:', 'lovetravel-child'); ?></strong> <span id="current-page">0</span>
            </p>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
