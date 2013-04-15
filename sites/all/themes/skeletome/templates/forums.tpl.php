<?php

/**
 * @file
 * Displays a forum.
 *
 * May contain forum containers as well as forum topics.
 *
 * Available variables:
 * - $forums: The forums to display (as processed by forum-list.tpl.php).
 * - $topics: The topics to display (as processed by forum-topic-list.tpl.php).
 * - $forums_defined: A flag to indicate that the forums are configured.
 *
 * @see template_preprocess_forums()
 *
 * @ingroup themeable
 */
?>
<style>
    .action-links {
        display: none;
    }
</style>
<?php if ($forums_defined): ?>
    <div class="container">
        <div class="row">
            <div class="span12">
                <div id="forum">
                    <?php print $forums; ?>
                    <?php print $topics; ?>
                </div>
            </div>
        </div>
    </div>
<?php endif; ?>
