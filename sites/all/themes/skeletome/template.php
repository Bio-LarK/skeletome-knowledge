<?php
/**
 * @file
 * Contains the theme's functions to manipulate Drupal's default markup.
 *
 * A QUICK OVERVIEW OF DRUPAL THEMING
 *
 *   The default HTML for all of Drupal's markup is specified by its modules.
 *   For example, the comment.module provides the default HTML markup and CSS
 *   styling that is wrapped around each comment. Fortunately, each piece of
 *   markup can optionally be overridden by the theme.
 *
 *   Drupal deals with each chunk of content using a "theme hook". The raw
 *   content is placed in PHP variables and passed through the theme hook, which
 *   can either be a template file (which you should already be familiary with)
 *   or a theme function. For example, the "comment" theme hook is implemented
 *   with a comment.tpl.php template file, but the "breadcrumb" theme hooks is
 *   implemented with a theme_breadcrumb() theme function. Regardless if the
 *   theme hook uses a template file or theme function, the template or function
 *   does the same kind of work; it takes the PHP variables passed to it and
 *   wraps the raw content with the desired HTML markup.
 *
 *   Most theme hooks are implemented with template files. Theme hooks that use
 *   theme functions do so for performance reasons - theme_field() is faster
 *   than a field.tpl.php - or for legacy reasons - theme_breadcrumb() has "been
 *   that way forever."
 *
 *   The variables used by theme functions or template files come from a handful
 *   of sources:
 *   - the contents of other theme hooks that have already been rendered into
 *     HTML. For example, the HTML from theme_breadcrumb() is put into the
 *     $breadcrumb variable of the page.tpl.php template file.
 *   - raw data provided directly by a module (often pulled from a database)
 *   - a "render element" provided directly by a module. A render element is a
 *     nested PHP array which contains both content and meta data with hints on
 *     how the content should be rendered. If a variable in a template file is a
 *     render element, it needs to be rendered with the render() function and
 *     then printed using:
 *       <?php print render($variable); ?>
 *
 * ABOUT THE TEMPLATE.PHP FILE
 *
 *   The template.php file is one of the most useful files when creating or
 *   modifying Drupal themes. With this file you can do three things:
 *   - Modify any theme hooks variables or add your own variables, using
 *     preprocess or process functions.
 *   - Override any theme function. That is, replace a module's default theme
 *     function with one you write.
 *   - Call hook_*_alter() functions which allow you to alter various parts of
 *     Drupal's internals, including the render elements in forms. The most
 *     useful of which include hook_form_alter(), hook_form_FORM_ID_alter(),
 *     and hook_page_alter(). See api.drupal.org for more information about
 *     _alter functions.
 *
 * OVERRIDING THEME FUNCTIONS
 *
 *   If a theme hook uses a theme function, Drupal will use the default theme
 *   function unless your theme overrides it. To override a theme function, you
 *   have to first find the theme function that generates the output. (The
 *   api.drupal.org website is a good place to find which file contains which
 *   function.) Then you can copy the original function in its entirety and
 *   paste it in this template.php file, changing the prefix from theme_ to
 *   STARTERKIT_. For example:
 *
 *     original, found in modules/field/field.module: theme_field()
 *     theme override, found in template.php: STARTERKIT_field()
 *
 *   where STARTERKIT is the name of your sub-theme. For example, the
 *   zen_classic theme would define a zen_classic_field() function.
 *
 *   Note that base themes can also override theme functions. And those
 *   overrides will be used by sub-themes unless the sub-theme chooses to
 *   override again.
 *
 *   Zen core only overrides one theme function. If you wish to override it, you
 *   should first look at how Zen core implements this function:
 *     theme_breadcrumbs()      in zen/template.php
 *
 *   For more information, please visit the Theme Developer's Guide on
 *   Drupal.org: http://drupal.org/node/173880
 *
 * CREATE OR MODIFY VARIABLES FOR YOUR THEME
 *
 *   Each tpl.php template file has several variables which hold various pieces
 *   of content. You can modify those variables (or add new ones) before they
 *   are used in the template files by using preprocess functions.
 *
 *   This makes THEME_preprocess_HOOK() functions the most powerful functions
 *   available to themers.
 *
 *   It works by having one preprocess function for each template file or its
 *   derivatives (called theme hook suggestions). For example:
 *     THEME_preprocess_page    alters the variables for page.tpl.php
 *     THEME_preprocess_node    alters the variables for node.tpl.php or
 *                              for node--forum.tpl.php
 *     THEME_preprocess_comment alters the variables for comment.tpl.php
 *     THEME_preprocess_block   alters the variables for block.tpl.php
 *
 *   For more information on preprocess functions and theme hook suggestions,
 *   please visit the Theme Developer's Guide on Drupal.org:
 *   http://drupal.org/node/223440 and http://drupal.org/node/1089656
 */


/**
 * Override or insert variables into the maintenance page template.
 *
 * @param $variables
 *   An array of variables to pass to the theme template.
 * @param $hook
 *   The name of the template being rendered ("maintenance_page" in this case.)
 */
/* -- Delete this line if you want to use this function
function STARTERKIT_preprocess_maintenance_page(&$variables, $hook) {
  // When a variable is manipulated or added in preprocess_html or
  // preprocess_page, that same work is probably needed for the maintenance page
  // as well, so we can just re-use those functions to do that work here.
  STARTERKIT_preprocess_html($variables, $hook);
  STARTERKIT_preprocess_page($variables, $hook);
}
// */

/**
 * Override or insert variables into the html templates.
 *
 * @param $variables
 *   An array of variables to pass to the theme template.
 * @param $hook
 *   The name of the template being rendered ("html" in this case.)
 */
/* -- Delete this line if you want to use this function
function STARTERKIT_preprocess_html(&$variables, $hook) {
  $variables['sample_variable'] = t('Lorem ipsum.');

  // The body tag's classes are controlled by the $classes_array variable. To
  // remove a class from $classes_array, use array_diff().
  //$variables['classes_array'] = array_diff($variables['classes_array'], array('class-to-remove'));
}
// */

/**
 * Override or insert variables into the page templates.
 *
 * @param $variables
 *   An array of variables to pass to the theme template.
 * @param $hook
 *   The name of the template being rendered ("page" in this case.)
 */
/* -- Delete this line if you want to use this function
function STARTERKIT_preprocess_page(&$variables, $hook) {
  $variables['sample_variable'] = t('Lorem ipsum.');
}
// */

/**
 * Override or insert variables into the node templates.
 *
 * @param $variables
 *   An array of variables to pass to the theme template.
 * @param $hook
 *   The name of the template being rendered ("node" in this case.)
 */
/* -- Delete this line if you want to use this function
function STARTERKIT_preprocess_node(&$variables, $hook) {
  $variables['sample_variable'] = t('Lorem ipsum.');

  // Optionally, run node-type-specific preprocess functions, like
  // STARTERKIT_preprocess_node_page() or STARTERKIT_preprocess_node_story().
  $function = __FUNCTION__ . '_' . $variables['node']->type;
  if (function_exists($function)) {
    $function($variables, $hook);
  }
}
// */

/**
 * Override or insert variables into the comment templates.
 *
 * @param $variables
 *   An array of variables to pass to the theme template.
 * @param $hook
 *   The name of the template being rendered ("comment" in this case.)
 */
/* -- Delete this line if you want to use this function
function STARTERKIT_preprocess_comment(&$variables, $hook) {
  $variables['sample_variable'] = t('Lorem ipsum.');
}
// */

/**
 * Override or insert variables into the region templates.
 *
 * @param $variables
 *   An array of variables to pass to the theme template.
 * @param $hook
 *   The name of the template being rendered ("region" in this case.)
 */
/* -- Delete this line if you want to use this function
function STARTERKIT_preprocess_region(&$variables, $hook) {
  // Don't use Zen's region--sidebar.tpl.php template for sidebars.
  //if (strpos($variables['region'], 'sidebar_') === 0) {
  //  $variables['theme_hook_suggestions'] = array_diff($variables['theme_hook_suggestions'], array('region__sidebar'));
  //}
}
// */

/**
 * Override or insert variables into the block templates.
 *
 * @param $variables
 *   An array of variables to pass to the theme template.
 * @param $hook
 *   The name of the template being rendered ("block" in this case.)
 */
/* -- Delete this line if you want to use this function
function STARTERKIT_preprocess_block(&$variables, $hook) {
  // Add a count to all the blocks in the region.
  // $variables['classes_array'][] = 'count-' . $variables['block_id'];

  // By default, Zen will use the block--no-wrapper.tpl.php for the main
  // content. This optional bit of code undoes that:
  //if ($variables['block_html_id'] == 'block-system-main') {
  //  $variables['theme_hook_suggestions'] = array_diff($variables['theme_hook_suggestions'], array('block__no_wrapper'));
  //}
}
// */


function skeletome_preprocess_page(&$variables) {
    if((arg(0) == "taxonomy" && arg(1) == "term") || (arg(0) == "node" && arg(2) == "clinical-feature")) {

        $variables['page']['content']['system_main']['no_content'] = array();

        $variables['tabs'] = array();
        $variables['page']['content']['system_main']['nodes'] = null;
        $variables['page']['content']['system_main']['pager'] = array();

//        echo "<pre>";
//        print_r($variables['page']['content']['system_main']['pager']);
//        echo "</pre>";

    }
}

/**
 * Overrides Apache Solr Search Facet Api Holder
 * @param $vars
 * @return string
 */
function skeletome_search_browse_blocks($vars)
{
    $html = "";
    if ($vars['content']['#children']) {
        $html = '<div class="span4">
                    <section>
                        <div class="section-segment section-segment-header"><h3>Filters</h3></div>';

        $html .= $vars['content']['#children'];

        $html .= '</section></div>';
    }

    return $html;
}



/**
 * Overrides faceapi display for counts
 * @param $variables
 * @return stringO
 */
function skeletome_facetapi_count($variables) {
    return ' <span class="badge badge-light">' . $variables['count'] . '</span>';
}

function skeletome_facetapi_link_inactive($variables) {
    // Builds accessible markup.
    // @see http://drupal.org/node/1316580
    $accessible_vars = array(
        'text' => $variables['text'],
        'active' => FALSE,
    );
    $accessible_markup = theme('facetapi_accessible_markup', $accessible_vars);

    // Sanitizes the link text if necessary.
    $sanitize = empty($variables['options']['html']);
//    $variables['text'] = ($sanitize) ? check_plain($variables['text']) : $variables['text'];


    $link_text = ($sanitize) ? check_plain($variables['text']) : $variables['text'];
    $link_text = '<span class="facetapi-list-item-name">' . $link_text . theme('facetapi_count', $variables) . '</span>';

    // Adds count to link if one was passed.
    if (isset($variables['count'])) {
//        $variables['text'] .= ' ' . theme('facetapi_count', $variables);
    }

    // Resets link text, sets to options to HTML since we already sanitized the
    // link text and are providing additional markup for accessibility.
//    $variables['text'] .= $accessible_markup;

    $variables['text'] = "<i class='icon-white icon-plus'></i> Add";
    $variables['options']['html'] = TRUE;

    // setup the class
    $variables['options']['attributes']['class'][] = 'btn';
    $variables['options']['attributes']['class'][] = 'btn-success';
    $variables['options']['attributes']['class'][] = 'pull-right';



    return "<div class='section-segment'>" . theme_link($variables) . $link_text . "</div>";
}

function skeletome_facetapi_title($variables) {
    if($variables['title'] == "Skeletome Tags") {
        $variables['title'] = "Clinical Features";
    }
    return t('Filter by Cat @title:', array('@title' => $variables['title']));
}


function skeletome_facetapi_link_active($variables) {

    // Sanitizes the link text if necessary.
    $sanitize = empty($variables['options']['html']);
    $link_text = ($sanitize) ? check_plain($variables['text']) : $variables['text'];
    $link_text = '<span class="facetapi-list-item-name">' . $link_text . '</span>';

    // Theme function variables fro accessible markup.
    // @see http://drupal.org/node/1316580
    $accessible_vars = array(
        'text' => $variables['text'],
        'active' => TRUE,
    );

    // Builds link, passes through t() which gives us the ability to change the
    // position of the widget on a per-language basis.
//    $replacements = array(
//        '!facetapi_deactivate_widget' => theme('facetapi_deactivate_widget', $variables),
//        '!facetapi_accessible_markup' => theme('facetapi_accessible_markup', $accessible_vars),
//    );
    $variables['text'] = '<i class="icon-white icon-minus"></i> Remove'; //t('!facetapi_deactivate_widget !facetapi_accessible_markup', $replacements);
    $variables['options']['html'] = TRUE;

    $variables['options']['attributes']['class'][] = 'btn';
    $variables['options']['attributes']['class'][] = 'btn-danger';
    $variables['options']['attributes']['class'][] = 'pull-right';



    return "<div class='section-segment'>" . theme_link($variables) . $link_text . "</div>";
}



function skeletome_theme(&$existing, $type, $theme, $path) {
    $hooks = zen_theme($existing, $type, $theme, $path);

    $hooks['user_login'] = array(
        'render element' => 'form',
        'path' => drupal_get_path('theme', 'skeletome') . '/templates',
        'template' => 'user-login',
        'preprocess functions' => array(
            'skeletome_preprocess_user_login'
        ),
    );
    $hooks['user_register_form'] = array(
        'render element' => 'form',
        'path' => drupal_get_path('theme', 'skeletome') . '/templates',
        'template' => 'user-register-form',
        'preprocess functions' => array(
            'skeletome_preprocess_user_register_form'
        ),
    );

    $items['user_pass'] = array(
        'render element' => 'form',
        'path' => drupal_get_path('theme', 'skeletome') . '/templates',
        'template' => 'user-pass',
        'preprocess functions' => array(
            'skeletome_preprocess_user_pass'
        ),
    );


    return $hooks;
}


function skeletome_preprocess_user_login(&$vars) {
    $vars['intro_text'] = t('This is my awesome login form');

    $vars['form']['actions']['submit']['#attributes'] = array(
        'class' => array(
            'btn', 'btn-primary'
        )
    );

    $vars['form']['actions']['#attributes'] = array();

}

function skeletome_preprocess_user_register_form(&$vars) {

    $vars['intro_text'] = t('This is my super awesome reg form');

    $vars['form']['actions']['submit']['#attributes'] = array(
        'class' => array(
            'btn', 'btn-primary'
        )
    );

    $vars['form']['actions']['#attributes'] = array();
}

function skeletome_preprocess_user_pass(&$vars) {
    echo "HELLO";
    $vars['form']['actions']['submit']['#attributes'] = array(
        'class' => array(
            'btn', 'btn-primary'
        )
    );

    $vars['form']['actions']['#attributes'] = array();
}

function skeletome_preprocess_comment(&$variables) {

}