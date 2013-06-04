<?php
/**
 * @file
 * Zen theme's implementation to display a single Drupal page.
 *
 * Available variables:
 *
 * General utility variables:
 * - $base_path: The base URL path of the Drupal installation. At the very
 *   least, this will always default to /.
 * - $directory: The directory the template is located in, e.g. modules/system
 *   or themes/bartik.
 * - $is_front: TRUE if the current page is the front page.
 * - $logged_in: TRUE if the user is registered and signed in.
 * - $is_admin: TRUE if the user has permission to access administration pages.
 *
 * Site identity:
 * - $front_page: The URL of the front page. Use this instead of $base_path,
 *   when linking to the front page. This includes the language domain or
 *   prefix.
 * - $logo: The path to the logo image, as defined in theme configuration.
 * - $site_name: The name of the site, empty when display has been disabled
 *   in theme settings.
 * - $site_slogan: The slogan of the site, empty when display has been disabled
 *   in theme settings.
 *
 * Navigation:
 * - $main_menu (array): An array containing the Main menu links for the
 *   site, if they have been configured.
 * - $secondary_menu (array): An array containing the Secondary menu links for
 *   the site, if they have been configured.
 * - $secondary_menu_heading: The title of the menu used by the secondary links.
 * - $breadcrumb: The breadcrumb trail for the current page.
 *
 * Page content (in order of occurrence in the default page.tpl.php):
 * - $title_prefix (array): An array containing additional output populated by
 *   modules, intended to be displayed in front of the main title tag that
 *   appears in the template.
 * - $title: The page title, for use in the actual HTML content.
 * - $title_suffix (array): An array containing additional output populated by
 *   modules, intended to be displayed after the main title tag that appears in
 *   the template.
 * - $messages: HTML for status and error messages. Should be displayed
 *   prominently.
 * - $tabs (array): Tabs linking to any sub-pages beneath the current page
 *   (e.g., the view and edit tabs when displaying a node).
 * - $action_links (array): Actions local to the page, such as 'Add menu' on the
 *   menu administration interface.
 * - $feed_icons: A string of all feed icons for the current page.
 * - $node: The node object, if there is an automatically-loaded node
 *   associated with the page, and the node ID is the second argument
 *   in the page's path (e.g. node/12345 and node/12345/revisions, but not
 *   comment/reply/12345).
 *
 * Regions:
 * - $page['header']: Items for the header region.
 * - $page['navigation']: Items for the navigation region, below the main menu (if any).
 * - $page['help']: Dynamic help text, mostly for admin pages.
 * - $page['highlighted']: Items for the highlighted content region.
 * - $page['content']: The main content of the current page.
 * - $page['sidebar_first']: Items for the first sidebar.
 * - $page['sidebar_second']: Items for the second sidebar.
 * - $page['footer']: Items for the footer region.
 * - $page['bottom']: Items to appear at the bottom of the page below the footer.
 *
 * @see template_preprocess()
 * @see template_preprocess_page()
 * @see zen_preprocess_page()
 * @see template_process()
 */
?>

<?php
    // Create some user access variables
    $isRegistered = isset($user->uid);
    $isModerator = is_array($user->roles) && in_array('sk_moderator', $user->roles);
    $isEditor = is_array($user->roles) && in_array('sk_editor', $user->roles);
    $isAdmin = user_access('administer site configuration');
?>


<h1>
    Trying to deploy with git.
</h1>
<div id="page" style="position:relative;" ng-controller="PageCtrl">

<header id="header" role="banner">
    <div class="navbar navbar-inverse navbar-dark navbar-static-top">
        <div class="navbar-inner">
            <div class="container">

                <div class="navbar-inner-table">

                    <?php if ($logo): ?>
                    <div class="navbar-inner-table-cell navbar-inner-table-cell-edge">
                        <a href="<?php print $front_page; ?>" title="<?php print t('Home'); ?>" rel="home" id="logo">
                            <img src="<?php print $logo; ?>" alt="<?php print t('Home'); ?>"/>
                        </a>
                    </div>
                    <?php endif; ?>

                    <div class="navbar-inner-table-cell">
                        <nav-search model="model.navSearchModel"></nav-search>
                    </div>

                    <div class="navbar-inner-table-cell navbar-inner-table-cell-edge navbar-inner-table-cell-browse">
                        <a href="?q=taxonomy/term/{{ browseTid }}">Browse</a>
                    </div>


                    <div class="navbar-inner-table-cell navbar-inner-table-cell-edge navbar-inner-table-cell-login">
                        <?php global $user; ?>
                        <div class="btn-group">
                        <?php if(isset($user->name)):?>
                            <a class="btn btn-dark-navbar" href="?q=profile-page/<?php echo $user->uid; ?>">
                                {{ user.name | truncate:30 }}
                            </a>
                            <a class="btn btn-dark-navbar" href="?q=user/logout">Logout</a>
                        <?php else: ?>
                            <a class="btn btn-dark-navbar" cm-popover cm-popover-content="loginForm" href id="login_button">Log In</a>
                            <a class="btn btn-dark-navbar" href="?q=user/register">Register</a>
                        <?php endif; ?>
                        </div>
                    </div>
                </div>




                <!--<div class="navsearch-small">
                    <?php  //get the query string if there is one
                        $query = "";
                        if(arg(0) == "search" && arg(1) == "site") $query = arg(2); ?>

                    <!---->

                <!--</div>-->


            </div>
        </div>
    </div>
</header>

<div id="main">

    <div id="content" class="container" role="main">
        <div class="row">
            <div class="span12">
                <?php print $messages; ?>
                <!--<?php print render($tabs); ?>
                <?php print render($action_links); ?>-->
            </div>
            <?php print render($page['help']); ?>
        </div>
    </div>

    <div class="container">
        <!-- class="container" -->
        <a id="main-content"></a>
        <div>
            <?php print render($page['content']); ?>
        </div>

    </div>

    <div class="container">
        <div class="row-fluid">
            <div class="span12">
                <?php print render($page['highlighted']); ?>
            </div>
        </div>
    </div>
    <!-- /#content -->

</div>
<!-- /#main -->

<!-- #Footer -->
<div class="page-footer">
    <div class="container">
        <div class="row-fluid">
            <div class="span12 ">
                <!-- Site Logo -->

                <?php if ($logo): ?>
                    <a href="<?php print $front_page; ?>" title="<?php print t('Home'); ?>" rel="home" id="logo"><img style="width: 100px"
                                                                                                                      src="<?php echo base_path() . drupal_get_path('theme', 'skeletome'); ?>/img/logo-dark.png" alt="<?php print t('Home'); ?>"/></a>
                <?php endif; ?>
                <!-- /#logo -->
                <style type="text/css">
                    .footer-links {
                        margin-top: 6px;;
                    }
                    .footer-links li {
                        float: left;
                        list-style-type: none;
                        margin-right: 10px;;
                    }
                </style>
                <ul class="footer-links pull-right">
                    <li><a class="contact-us-link" href="<?php print $front_page; ?>">Home</a></li>
                    <li><a class="contact-us-link" href="?q=about">About</a></li>
                    <li><a class="contact-us-link" href="?q=team">Team</a></li>
                    <li><a class="contact-us-link" href="?q=contact">Contact</a></li>
                </ul>


                <?php //print render($page['footer']); ?>

                <?php // print $feed_icons; ?>
            </div>
        </div>
    </div>
</div>

<!-- /#Footer-->

</div><!-- /#page -->
