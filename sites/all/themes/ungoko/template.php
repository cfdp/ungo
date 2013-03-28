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
    $variables['user_picture'] = ungoko_get_user_picture($variables, null);
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
    $variables['picture'] = ungoko_get_user_picture($variables, $uid);
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

/* Returns the relevant profile2 user picture, $uid is sent along from comments */
function ungoko_get_user_picture(&$variables, $uid){
 
  // get user id from current node if it is not passed as parameter
  if (arg(0) == 'node' && is_numeric(arg(1)) && !$uid ) {
    // Get the nid
    $nid = arg(1);
    $node = node_load($nid);
    $uid = $node->uid;
  }
  
  $user = user_load($uid);

  if (in_array('client', $user->roles)) {
    $profile = profile2_load_by_user($uid, 'client');
  }
  else if (in_array('counselor', $user->roles)) {
    $profile = profile2_load_by_user($uid, 'counselor');
  }
  else if (in_array('authenticated user', $user->roles)) {
    $profile = profile2_load_by_user($uid, 'counselor');
    debug('good god');
  }
  else {
    //user is anonymous @todo: handle this case!

  }

  $display = array(
                'type' => 'image',
                'label'=> 'hidden',// inline, above
                'settings'=>array(
                            'image_style'=> 'ungo_profile_img',
                            'image_link'=> 'content',
                ));
                
  return drupal_render(field_view_field('profile2', $profile, 'field_profile_picture', $display));
}
