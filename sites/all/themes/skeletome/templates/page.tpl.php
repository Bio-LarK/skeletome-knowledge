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

<div id="page" style="position:relative;" ng-controller="PageCtrl">

    <header id="header" role="banner">
        <div class="navbar navbar-inverse navbar-dark navbar-static-top">
            <div class="navbar-inner">
                <div class="container">
                    <?php if ($logo): ?>
                    <a class="brand" href="<?php print $front_page; ?>" title="<?php print t('Home'); ?>" rel="home" id="logo"><img
                            src="<?php print $logo; ?>" alt="<?php print t('Home'); ?>"/></a>
                    <?php endif; ?>

                    <form class="navbar-search pull-left">
                        <div class="input-append">
                            <input autocomplete="?q=ajax/autocomplete/all/" type="text" class="search-query" ng-model="searchTerm" ng-init="searchTerm='<?php if(arg(0)=="search") echo arg(2); ?>'" placeholder="Search for Bone Dysplasias, Groups, Genes or Clinical Features" cm-return="globalSearch(searchTerm)">
                            <a ng-click="globalSearch(searchTerm)" class="btn btn-success" href><i class="icon-search icon-white"></i> Full Search</a>
                        </div>
                    </form>


                    <!--<ul class="nav nav-pills">
                        <?php foreach($main_menu as $item) : ?>
                            <?php $url = "";
                            if($item['href'] == "<front>") {
                                $url = base_path();
                            } else {
                                $url = "?q=" . $item['href'];
                            } ?>
                            <li>
                                <a href="<?php echo $url; ?>">
                                    <?php echo $item['title']; ?>
                                </a>
                            </li>


                        <?php endforeach; ?>

                    </ul>-->

                    <div class="btn-group pull-right">
                        <?php global $user; ?>
                        <?php if(isset($user->name)):?>
                            <a class="btn btn-primary" href>
                                <?php echo $user->name; ?>
                            </a>
                            <a class="btn btn-primary" href="?q=user/logout">Logout</a>
                        <?php else: ?>
                            <a class="btn btn-primary" cm-popover cm-popover-content="loginForm" href id="login_button">Log In</a>
                            <a class="btn btn-primary" href="?q=user/register">Register</a>
                        <?php endif; ?>
                    </div>

                </div>
            </div>
        </div>

        <div class="container">
            <div class="row">
                <div class="span12">

                    <!-- Site Logo -->

                    <!-- /#logo -->

                    <!-- Navigation -->
                    <div id="main_menu" class="group">
                        <?php //print render($page['main_menu']); ?>
                    </div>
                    <!-- /navigation -->

                    <!-- Global Search -->
                    <!--<div id="global_search">
                        <?php // print render($page['global_search']); ?>
                    </div>-->
                    <!-- /Global Search -->

                    <!-- User Login -->
                    <div id="current_user">
                        <?php
                        // Display login in if not logged in
                        if($user->uid) :
                            ?>
<!--                            <a href="#" class="nav_button">-->
<!--                                --><?php //echo $user->name; ?>
<!--                            </a>-->
                            <?php else:
                            // Not logged in ?>
<!--                            <div class="group">-->
<!--                                <a href="#" class="nav_button">-->
<!--                                    Login-->
<!--                                </a>-->
<!--                            </div>-->
                            <?php endif; ?>

                        <div class="current_user_menu">
                            <?php //print render($page['user_login']); ?>
                            <?php //print render($page['user_menu']); ?>
                        </div>

                    </div>
                    <!-- /User Login -->

                    <!-- Add the rest of the header content -->
                    <?php //print render($page['header']); ?>

                </div>
            </div>

        </div>
    </header>

    <div id="main">

        <div id="content" class="column container" role="main">

            <div class="row">
                <div class="span12">
                    <?php print $messages; ?>
                </div>


                    <div>
                        <?php //print render($tabs); ?>
                    </div>



                    <?php if ($action_links): ?>
                        <ul class="action-links"><?php //print render($action_links); ?></ul>
                    <?php endif; ?>

                <?php print render($page['help']); ?>


                <?php print $feed_icons; ?>
            </div>

            <div class="row">
                <?php print render($title_prefix); ?>

                <div class="span12">
                    <?php print $breadcrumb; ?>
                </div>



                <?php print render($title_suffix); ?>



            </div>

            <div class="clear"></div>
        </div>
        <div class="container">
            <!-- class="container" -->
            <a id="main-content"></a>
            <div>
                <?php print render($page['content']); ?>
            </div>

        </div>

        <div class="container">
            <div class="row">
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
            <div class="row">
                <div class="span12 ">
                    <!-- Site Logo -->

                    <?php if ($logo): ?>
                        <a href="<?php print $front_page; ?>" title="<?php print t('Home'); ?>" rel="home" id="logo"><img style="width: 100px"
                                src="<?php echo base_path() . drupal_get_path('theme', 'skeletome'); ?>/img/logo-dark.png" alt="<?php print t('Home'); ?>"/></a>
                    <?php endif; ?>
                    <!-- /#logo -->
                    <a class="pull-right contact-us-link" href="?q=contact">Contact</a>

                    <?php //print render($page['footer']); ?>

                    <?php // print $feed_icons; ?>
                </div>
            </div>
        </div>
    </div>

    <!-- /#Footer-->

</div><!-- /#page -->
