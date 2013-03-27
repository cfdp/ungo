<?php

function ungoko_preprocess_html(&$vars) {
  $body_classes = array($vars['classes_array']);
      if ($vars['user']) {
        foreach($vars['user']->roles as $key => $role){
          $role_class = 'role-' . str_replace(' ', '-', $role);
          $vars['classes_array'][] = $role_class;
    }
  }
}

