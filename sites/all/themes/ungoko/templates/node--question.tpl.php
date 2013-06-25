<article id="node-<?php print $node->nid; ?>" class="<?php print $classes; ?> clearfix"<?php print $attributes; ?>>


  <header>
    <?php print render($title_prefix); ?>
    <?php if (!$page && $title): ?>
      <h2<?php print $title_attributes; ?>><a href="<?php print $node_url; ?>"><?php print $title; ?></a></h2>
    <?php endif; ?>
    <?php print render($title_suffix); ?>

    <?php if ($display_submitted): ?>
      <span class="submitted">
        <?php /*print $user_picture;*/ ?>
        <?php print $submitted; ?>
      </span>
    <?php endif; ?>
  </header>

  <?php
    // Hide comments, tags, and links now so that we can render them later.
    hide($content['comments']);
    hide($content['links']);
    hide($content['field_tags']);
    print render($content['body']);
  ?>
  
  <?php if (!empty($content['field_tags']) || !empty($content['links'])): ?>
    <footer>
      <?php print render($content['field_tags']); ?>
      <?php /* print render($content['links']); */ ?>
    </footer>
  <?php endif; ?>

  <?php print render($content['comments']); ?>

  <?php
  //Display the value of the status field if the user has the needed permission
   if(user_access('view question status')): ?> 
    <?php print render($content['field_status']['#items'][0]['value']); ?>
  <?php endif;?>

  <?php
  //Display the button to allow users to archive questions
   if(user_access('archive question') && $content['field_status']['#items'][0]['value'] == 'pending'): ?>
    <?php  
        print drupal_render(drupal_get_form('cfdp_uf_form'));
    ?>
  <?php endif;?>

  <?php
  // Display the button to allow users to reopen archived questions
   if(user_access('reopen question') && $content['field_status']['#items'][0]['value'] == 'archived'): ?>
    <?php  
        print drupal_render(drupal_get_form('cfdp_uf_form'));
    ?>
  <?php endif;?>

</article> <!-- /.node -->