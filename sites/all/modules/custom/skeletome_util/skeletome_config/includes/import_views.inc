<?php

function import_view($file_location, $region, $content_type_restriction, $user_restriction) {
  $view_name = do_import($file_location);
  $view = views_get_view($view_name);
  $blocks = _block_rehash('marinelli');
  
  $block_description = NULL;
  foreach ($view->display as $id => $display) {
    if ($display->display_plugin === 'block') {
      $block_description = $display->display_options['block_description'];
    }
  }

  if ($block_description !== NULL) {
    $delta = NULL;
    foreach ($blocks as $block) {
      if (($block['info'] === $block_description) && ($block['module'] === 'views')) {
	$delta = $block['delta'];
	break;
      }
    }

    if ($delta !== NULL) {
      $bid = get_block_id($delta);

      if ($region !== '-1') {
	db_delete('block')->condition('bid', $bid)->execute();
	db_insert('block')->fields(array( 
	  'bid'	=> $bid,
	  'module'	=> 'views',
	  'delta'	=> $delta,
	  'theme'	=> 'marinelli',
	  'status'	=> 1,
	  'weight'	=> 0,
	  'region'	=> $region,
	  'custom'	=> 0,
	  'visibility'	=> 0,
	  'pages'	=> '',
	  'title'	=> '',
	  'cache'	=> -1,  
	))->execute();
      }

      if ($content_type_restriction != NULL) {
	db_insert('block_node_type')->fields(array( 
	  'module'	=> 'views',
	  'delta'	=> $delta,
	  'type'	=> $content_type_restriction,
	))->execute();
      }

      if ($user_restriction != NULL) {
	db_insert('block_role')->fields(array( 
	  'module'	=> 'views',
	  'delta'	=> $delta,
	  'rid'		=> $user_restriction,
	))->execute();
      }
    }
  }
}

function do_import($file_location) {
  $view_content = file_get_contents($file_location);

  $view = '';
  views_include('view');
  ob_start();
  eval($view_content);
  ob_end_clean();

  if (!is_object($view)) {
    drupal_set_message('Unable to interpret code.', 'error');
  }

  $test = views_get_view($view->name);
  if ($test && $test->type != t('Default')) {
    drupal_set_message('A view by that name already exists; please choose a different name', 'error');
  }

  // Make sure base table gets set properly if it got moved.
  $view->update();

  $view->init_display();

  $broken = FALSE;
  // Make sure that all plugins and handlers needed by this view actually exist.
  foreach ($view->display as $id => $display) {
    if (empty($display->handler) || !empty($display->handler->broken)) {
      drupal_set_message(t('Display plugin @plugin is not available.', array('@plugin' => $display->display_plugin)), 'error');
      $broken = TRUE;
      continue;
    }

    $plugin = views_get_plugin('style', $display->handler->get_option('style_plugin'));
    if (!$plugin) {
      drupal_set_message(t('Style plugin @plugin is not available.', array('@plugin' => $display->handler->get_option('style_plugin'))), 'error');
      $broken = TRUE;
    }
    elseif ($plugin->uses_row_plugin()) {
      $plugin = views_get_plugin('row', $display->handler->get_option('row_plugin'));
      if (!$plugin) {
        drupal_set_message(t('Row plugin @plugin is not available.', array('@plugin' => $display->handler->get_option('row_plugin'))), 'error');
        $broken = TRUE;
      }
    }

    foreach (views_object_types() as $type => $info) {
      $handlers = $display->handler->get_handlers($type);
      if ($handlers) {
        foreach ($handlers as $id => $handler) {
          if ($handler->broken()) {
            drupal_set_message(t('@type handler @table.@field is not available.', array(
              '@type' => $info['stitle'],
              '@table' => $handler->table,
              '@field' => $handler->field,
            )), 'error');
            $broken = TRUE;
          }
        }
      }
    }
  }

  if ($broken) {
    drupal_set_message('Unable to import view.', 'error');
  }

  $view->save();
  drupal_set_message(t('The view %name has been saved.', array('%name' => $view->get_human_name())));

  // Make sure menu items get rebuilt as neces
  menu_rebuild();

  // Clear the views cache.
  cache_clear_all('*', 'cache_views');

  // Clear the page cache.
  cache_clear_all();

  return $view->name;
}

function get_block_id($delta) {
  $sql = 'SELECT bid FROM {block} b WHERE b.delta = :delta';
  $result = db_query($sql, array(':delta' => $delta));

  $bid = -1;
  foreach ($result as $row) {
    $bid = $row->bid;
  }
  return $bid;
}