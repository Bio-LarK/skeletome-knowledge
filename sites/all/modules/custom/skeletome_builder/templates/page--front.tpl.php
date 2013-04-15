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

<style type="text/css">
    .banner {
        background-color: #2e3033;
        color: white;
        text-align: center;
        padding: 21px;;
        margin-bottom: 42px;;

        -webkit-box-shadow: 0 -5px 20px -5px rgba(0, 0, 0, 1) inset;

    }

    .logo {
        font-size: 42px;
        font-weight: bold;;
        margin-bottom: 7px;;
    }

    .slogan {
        font-size: 28px;
        width: 600px;;
        margin: 0 auto;
        line-height: 35px;;
        font-family: "HelveticaNeue-UltraLight", "Helvetica Neue Ultra Light", Helvetica, Arial, san-serif;
        margin-bottom: 21px;;
    }

    .navbar-search {
        margin: 0 auto;
        display: block;
        float: none;
        margin-bottom: 14px;
    }

    .navbar-search input.search-query {
        padding: 14px;
        padding-left: 21px;;
        border-radius: 100px 0 0 100px;
        font-size: 14px;
    }

    .navbar-search a {
        padding: 13px;
        padding-right: 21px;;
        font-size: 14px;
    }

    .navbar-search input.search-query::-webkit-input-placeholder {
        color: rgba(255, 255, 255, 0.8) !important;
    }
    .navbar-search input.search-query:focus::-webkit-input-placeholder {
        color: rgba(0, 0, 0, 0.8) !important;
    }
    .navbar-search input.search-query:-moz-placeholder {
    color: rgba(255, 255, 255, 0.8) !important;
    }
    .navbar-search input.search-query:focus:-moz-placeholder {
    color: rgba(0, 0, 0, 0.8) !important;
    }
    .navbar-search input.search-query:-ms-input-placeholder {
    color: rgba(255, 255, 255, 0.8) !important;
    }
    .navbar-search input.search-query:focus:-ms-input-placeholder {
    color: rgba(0, 0, 0, 0.8) !important;
    }

</style>
<div id="page" style="position:relative;" ng-controller="FrontPageCtrl">

    <div ng-controller="PageCtrl">
        <header role="banner">
            <div class="banner">

                <div class="container">
                    <div class="row">
                        <div class="span12" style="position: relative;">


                            <h1 class="logo">
                                <img style="height:40px; position: relative; top: -5px; left:-5px"
                                     src="<?php echo base_path() . drupal_get_path('module', 'skeletome_builder'); ?>/images/bone-logo.png"
                                     alt="Spine"/>Skeletome
                            </h1>

                           <div class="btn-group" style="position: absolute;top: 0px; right: 0px">
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


                            <h2 class="slogan">
                                <?php print $site_slogan; ?>
                            </h2>

                            <form class="navbar-search">
                                <div class="input-append">
                                    <input cm-focus="true" autocomplete="?q=ajax/autocomplete/all/" type="text"
                                           class="search-query"
                                           ng-model="searchTerm"
                                           ng-init="searchTerm='<?php if (arg(0) == "search") echo arg(1); ?>'"
                                           placeholder="Search for Bone Dysplasias, Groups or Genes"
                                           cm-return="globalSearch(searchTerm)">
                                    <a ng-click="globalSearch(searchTerm)" class="btn btn-success" href><i
                                            class="icon-search icon-white"></i> Search</a>
                                </div>
                            </form>




                        </div>
                    </div>
                </div>
            </div>
            <div style="display: none;" navbar navbar-inverse navbar-dark navbar-static-top
            ">
            <div class="navbar-inner">
                <div class="container">
                    <?php if ($logo): ?>
                        <a class="brand" href="<?php print $front_page; ?>" title="<?php print t('Home'); ?>" rel="home"
                           id="logo"><img
                                src="<?php print $logo; ?>" alt="<?php print t('Home'); ?>"/></a>
                    <?php endif; ?>

                    <form class="navbar-search pull-left">
                        <div class="input-append">
                            <input cm-focus="true" autocomplete="?q=ajax/autocomplete/all/" type="text" class="search-query"
                                   ng-model="searchTerm"
                                   ng-init="searchTerm='<?php if (arg(0) == "search") echo arg(1); ?>'"
                                   placeholder="Search for Bone Dysplasias, Groups, Genes or Clinical Features"
                                   cm-return="globalSearch(searchTerm)">
                            <a ng-click="globalSearch(searchTerm)" class="btn btn-success" href><i
                                    class="icon-search icon-white"></i> Full Search</a>
                        </div>
                    </form>

                    <div class="jumbotron">
                        <?php print $site_slogan; ?>
                    </div>


                    <!--<ul class="nav nav-pills">
                        <?php foreach ($main_menu as $item) : ?>
                            <?php $url = "";
                            if ($item['href'] == "<front>") {
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
                        <?php if (isset($user->name)): ?>
                            <a class="btn btn-primary"
                               href><?php echo $user->name; ?>
                            </a>
                            <a class="btn btn-primary" href="?q=user/logout">Logout</a>
                        <?php else: ?>
                            <a class="btn btn-primary" cm-popover cm-popover-content="loginForm" href id="login_button">Log
                                In</a>
                            <a class="btn btn-primary" href="?q=user/register">Register</a>
                        <?php endif; ?>
                    </div>

                </div>
            </div>
    </div>

    </header>


    <div class="container">
        <div class="row">
            <div class="span12">
                <?php print $messages; ?>
            </div>
        </div>
    </div>

    <div class="container">
        <div class="row">
            <div class="span8">
                <section class="">
                    <!-- Active Pages -->
                    <h3>Recently Updated</h3>
                    <div class="row">
                        <div class="span5">
                            <h4><img style="position: relative; top:-2px" src="<?php echo base_path() . drupal_get_path('module', 'skeletome_builder'); ?>/images/logo-small-bone-dysplasia.png" alt=""/> Bone Dysplasias</h4>
                            <ul ng-cloak>
                                <li ng-repeat="boneDysplasia in allBoneDysplasias"><a
                                        ng-href="?q=node/{{ boneDysplasia.nid }}">{{ boneDysplasia.title }}</a></li>
                            </ul>
                        </div>
                        <div class="span2">
                            <h4><img style="position: relative; top:-2px" src="<?php echo base_path() . drupal_get_path('module', 'skeletome_builder'); ?>/images/logo-small-gene.png" alt=""/> Genes</h4>
                            <ul ng-cloak>
                                <li ng-repeat="gene in allGenes | limitTo:5"><a ng-href="?q=node/{{ gene.nid }}">{{ gene.title }}</a>
                                </li>
                            </ul>
                        </div>
                    </div>
                </section>
            </div>
            <div class="span4">
                <section class="">
                    <h4>Bone Dysplasia Classifications</h4>
                    <ul>
                        <li ng-repeat="source in allSources">
                            <a href="?q=taxonomy/term/{{ source.tid }}">{{ source.name }}</a>
                        </li>
                    </ul>
                </section>
            </div>
            <div class="span4">
                <section>
                    <h4>Top Contributors</h4>
                    <ul>
                        <li ng-repeat="user in topContributors">
                            {{ user.name | capitalize }}
                        </li>
                    </ul>
                </section>
            </div>
        </div>
    </div>

    <div class="page-footer">
        <div class="container">
            <div class="row">
                <div class="span12">

                    <?php if ($logo): ?>
                        <a href="<?php print $front_page; ?>" title="<?php print t('Home'); ?>" rel="home"
                           id="logo"><img
                                style="width: 100px"
                                src="<?php echo base_path() . drupal_get_path('theme', 'skeletome'); ?>/img/logo-dark.png"
                                alt="<?php print t('Home'); ?>"/></a>
                    <?php endif; ?>
                    <a class="pull-right contact-us-link" href="?q=contact">Contact</a>

                    <?php //print render($page['footer']); ?>

                    <?php // print $feed_icons; ?>
                </div>
            </div>
        </div>
    </div>

    <!--<div>
        <div class="container" >
            <div class="row">

                <div class="span12 section-top">

                </div>

                <section>
                    <div class="row">
                        <div class="span12">

                        </div>
                        <div class="span12">
                            <h1>Knowledge Base</h1>
                        </div>
                        <div class="span35">
                            <h3>Bone Dysplasias</h3>


                            <form>
                                <div class="search-input">
                                    <i class="icon-search"></i>
                                    <input ng-model="boneDysplasiaSearch"
                                           autocomplete="?q=ajax/autocomplete/bone-dysplasias/"
                                           class="search-query full-width"
                                           type="text" placeholder="Search for a Bone Dysplasia"/>
                                    <a class="close" href="" ng-show="boneDysplasiaSearch.length"
                                       ng-click="boneDysplasiaSearch = '';">&times;</a>
                                </div>


                            </form>

                        </div>
                        <div class="span35">

                            <h3>Groups</h3>

                            <form>
                                <div class="search-input">
                                    <i class="icon-search"></i>
                                    <input ng-model="groupSearch" autocomplete="?q=ajax/autocomplete/bone-dysplasia-groups/"
                                           class="search-query full-width" type="text" placeholder="Search for a Group"/>

                                    <a class="close" href="" ng-show="groupSearch.length"
                                       ng-click="groupSearch = '';">&times;</a>
                                </div>
                            </form>


                        </div>
                        <div class="span3">
                            <h3>Clinical Features</h3>
                            <form>
                                <input autocomplete="?q=ajax/autocomplete/clinical-feature/" class="search-query full-width" type="text" placeholder="Search for a Clinical Features"/>
                            </form>
                            <ul>
                                <li ng-repeat="clinicalFeature in allClinicalFeatures | limitTo:14"><a ng-href="?q=taxonomy/term/{{ clinicalFeature.tid }}">{{ clinicalFeature.name }}</a></li>
                                <li><a href="?q=search/clinical-features/">More</a></li>
                            </ul>
                        </div>
                        <div class="span35">

                            <h3>Genes</h3>

                            <form>
                                <div class="search-input">
                                    <i class="icon-search"></i>
                                    <input ng-model="geneSearch" autocomplete="?q=ajax/autocomplete/genes/"
                                           class="search-query full-width" type="text"
                                           placeholder="Search for a Genes"/>
                                    <a class="close" href="" ng-show="geneSearch.length"
                                       ng-click="geneSearch = '';">&times;</a>
                                </div>
                            </form>
                            <ul>
                                <li ng-repeat="gene in allGenes"><a ng-href="?q=node/{{ gene.nid }}">{{ gene.title }}</a>
                                </li>
                                <li><a href="?q=search/site/&f[0]=bundle%gene">More</a></li>
                            </ul>

                        </div>
                    </div>
                </section>


            </div>
        </div>


        <div id="main">

            <div id="content" class="column container" role="main">

                <div class="row">

                    <div>
                        <?php //print render($tabs); ?>
                    </div>
                    <?php print render($page['help']); ?>
                    <?php if ($action_links): ?>
                        <ul class="action-links"><?php print render($action_links); ?></ul>
                    <?php endif; ?>

                    <?php print $feed_icons; ?>
                </div>

                <div class="row">
                    <?php print render($title_prefix); ?>

                    <?php print render($title_suffix); ?>
                    <div class="span12" style="display: none">
                        <?php print $breadcrumb; ?>
                    </div>


                </div>

                <div class="clear"></div>
            </div>
            <div class="container">
                <a id="main-content"></a>
                <?php //print render($page['content']); ?>

            </div>

            <div class="container">
                <div class="row">
                    <div class="span12">
                        <?php print render($page['highlighted']); ?>
                    </div>
                </div>
            </div>

        </div>


    </div>-->
</div>
<!-- /#page -->
