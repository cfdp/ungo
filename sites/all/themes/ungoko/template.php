<?php

/* We add the user role to body classes */
function ungoko_preprocess_html(&$vars) {
  $body_classes = array($vars['classes_array']);
      if ($vars['user']) {
        foreach($vars['user']->roles as $key => $role){
          $role_class = 'role-' . str_replace(' ', '-', $role);
          $vars['classes_array'][] = $role_class;
    }
  }
}

/**
 * Preprocess variables for node.tpl.php
 *
 * @see node.tpl.php
 */
function ungoko_preprocess_node(&$variables) {
  if ($variables['teaser']) {
    $variables['classes_array'][] = 'row-fluid';
  }
  if ($variables['display_submitted']){
    $variables['user_picture'] = cfdp_uf_get_user_picture($variables, null);
  }
  
}

/* Adding the "Well" bootstrap effecto to comments */
function ungoko_preprocess_comment(&$variables) {
  $comment = $variables['elements']['#comment'];
  $node = $variables['elements']['#node'];
  $variables['comment'] = $comment;
  $variables['node'] = $node;
  $variables['author'] = theme('username', array('account' => $comment));

  $variables['created'] = format_date($comment->created);

  // Avoid calling format_date() twice on the same timestamp.
  if ($comment->changed == $comment->created) {
    $variables['changed'] = $variables['created'];
  }
  else {
    $variables['changed'] = format_date($comment->changed);
  }

  $variables['new'] = !empty($comment->new) ? t('new') : '';
  //$variables['picture'] = theme_get_setting('toggle_comment_user_picture') ? theme('user_picture', array('account' => $comment)) : '';
  
  /* benjamin@cfdp.dk: We take the user picture from the profile2 field */
  if (theme_get_setting('toggle_comment_user_picture')){
    $uid = $comment->uid;
    $variables['picture'] = cfdp_uf_get_user_picture($variables, $uid);
  } 
  $variables['signature'] = $comment->signature;

  $uri = entity_uri('comment', $comment);
  $uri['options'] += array('attributes' => array(
    'class' => 'permalink',
    'rel' => 'bookmark',
  ));

  $variables['title'] = l($comment->subject, $uri['path'], $uri['options']);
  $variables['permalink'] = l(t('Permalink'), $uri['path'], $uri['options']);
  $variables['submitted'] = t('Submitted by !username on !datetime', array('!username' => $variables['author'], '!datetime' => $variables['created']));

  // Preprocess fields.
  field_attach_preprocess('comment', $comment, $variables['elements'], $variables);

  // Helpful $content variable for templates.
  foreach (element_children($variables['elements']) as $key) {
    $variables['content'][$key] = $variables['elements'][$key];
  }

  // Set status to a string representation of comment->status.
  if (isset($comment->in_preview)) {
    $variables['status'] = 'comment-preview';
  }
  else {
    $variables['status'] = ($comment->status == COMMENT_NOT_PUBLISHED) ? 'comment-unpublished' : 'comment-published';
  }
  
  /* Adding the well class to comments...*/
  $variables['classes_array'][] = "well";

  // Gather comment classes.
  // 'comment-published' class is not needed, it is either 'comment-preview' or
  // 'comment-unpublished'.
  if ($variables['status'] != 'comment-published') {
    $variables['classes_array'][] = $variables['status'];
  }
  if ($variables['new']) {
    $variables['classes_array'][] = 'comment-new';
  }
  if (!$comment->uid) {
    $variables['classes_array'][] = 'comment-by-anonymous';
  }
  else {
    if ($comment->uid == $variables['node']->uid) {
      $variables['classes_array'][] = 'comment-by-node-author';
    }
    if ($comment->uid == $variables['user']->uid) {
      $variables['classes_array'][] = 'comment-by-viewer';
    }
  }
}


// Add placeholder attributes to the login form fields 
// Push links to the end of the form
function ungoko_form_user_login_block_alter(&$form, &$form_state, $form_id) {
  
  $form['name']['#attributes'] = array('placeholder' => t("Email"));
  $form['pass']['#attributes'] = array('placeholder' => t("Password"));
  $form['links']['#weight'] = 100;

}

// Change labels in the comment form 
function ungoko_form_comment_form_alter(&$form, &$form_state, $form_id) {
  
  $label = t('Reply');
  $form['actions']['submit']['#value'] = $label;
  $form['actions']['submit']['#attributes']['class'][] = 'btn-primary';
  
}

function ungoko_form_question_node_form_alter(&$form, &$form_state, $form_id){
  $label = t('Send');
  $form['actions']['submit']['#value'] = $label;
  $form['actions']['submit']['#attributes']['class'][] = 'btn-primary';
}

/*  Overrides bootstrap theme.inc file 
    Loads specific local jQuery version 1.7.2 required by Bootstrap instead of default Drupal
    Provide local bootstrap library file when CDN option is unchecked. */

function bootstrap_js_alter(&$javascript) {
  $theme_path = drupal_get_path('theme', 'bootstrap');
  // Replace with current version.
  $jQuery_version = '1.7.2';
  $javascript['misc/jquery.js']['data'] = drupal_get_path('theme', 'bootstrap').'/js/jquery-1.7.2.min.js';
  $javascript['misc/jquery.js']['version'] = $jQuery_version;
  $files = array();
  
  if (theme_get_setting('cdn_bootstrap')) {
    $files[] = 'http://netdna.bootstrapcdn.com/twitter-bootstrap/2.2.1/js/bootstrap.min.js';
  }else{
    $files[] = $theme_path . '/js/bootstrap.min.js';
  }

  // Rearrange / Add JS
  $group = -50;
  $weight = -100;
  foreach ($files as $file) {
    if (!isset($javascript[$file])) {
      $javascript[$file] = drupal_js_defaults();
      $javascript[$file]['data'] = $file;
      $javascript[$file]['group'] = $group;
      $javascript[$file]['weight'] = $weight;
      $weight++;
    }
  }
}