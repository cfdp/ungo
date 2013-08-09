<article id="node-<?php print $node->nid; ?>" class="<?php print $classes; ?> clearfix"<?php print $attributes; ?>>


  <header>
    <?php print render($title_prefix); ?>
    <?php if (!$page && $title): ?>
      <h2<?php print $title_attributes; ?>><a href="<?php print $node_url; ?>"><?php print $title; ?></a></h2>
    <?php endif; ?>
    <?php print render($title_suffix); ?>

  </header>

  <?php
    // Hide comments, tags, and links now so that we can render them later.
    hide($content['comments']);
    hide($content['links']);
    hide($content['field_tags']);
    print render($content['body']);
  ?>
    <?php if ($display_submitted): ?>
      <div class="submitted">
        <?php
        //Display the value of the status field if the user has the needed permission
         if(user_access('view question status')): ?> 
          <span class="question-status">
            <span class="question-status-label">Status:</span>
            <span class="question-status-value"><?php print render($content['field_status']['#items'][0]['value']); ?></span>
          </span>
        <?php endif;?>

        <?php
        //Display the button to allow users to archive questions
         if(user_access('archive question') && $content['field_status']['#items'][0]['value'] == 'pending'): ?>
          <span class="question-status-action"><?php print drupal_render(drupal_get_form('cfdp_uf_form')); ?></span>
        <?php endif;?>

        <?php
        // Display the button to allow users to reopen archived questions
         if(user_access('reopen question') && $content['field_status']['#items'][0]['value'] == 'archived'): ?>
          <span class="question-status-reopen"><?php print drupal_render(drupal_get_form('cfdp_uf_form')); ?></span>
        <?php endif;?>

        <?php //print $user_picture; ?>
        <span class="pull-right"><?php print $submitted; ?></span>
      </div>
    <?php endif; ?>
  
  <?php if (!empty($content['field_tags']) || !empty($content['links'])): ?>
    <footer>
      <?php print render($content['field_tags']); ?>
      <?php /* print render($content['links']); */ ?>
    </footer>
  <?php endif; ?>

  <?php print render($content['comments']); ?>

</article> <!-- /.node -->