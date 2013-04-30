<?php

/**
 * @file
 * Default theme implementation to display a term.
 *
 * Available variables:
 * - $name: the (sanitized) name of the term.
 * - $content: An array of items for the content of the term (fields and
 *   description). Use render($content) to print them all, or print a subset
 *   such as render($content['field_example']). Use
 *   hide($content['field_example']) to temporarily suppress the printing of a
 *   given element.
 * - $term_url: Direct URL of the current term.
 * - $term_name: Name of the current term.
 * - $classes: String of classes that can be used to style contextually through
 *   CSS. It can be manipulated through the variable $classes_array from
 *   preprocess functions. The default values can be one or more of the following:
 *   - taxonomy-term: The current template type, i.e., "theming hook".
 *   - vocabulary-[vocabulary-name]: The vocabulary to which the term belongs to.
 *     For example, if the term is a "Tag" it would result in "vocabulary-tag".
 *
 * Other variables:
 * - $term: Full term object. Contains data that may not be safe.
 * - $view_mode: View mode, e.g. 'full', 'teaser'...
 * - $page: Flag for the full page state.
 * - $classes_array: Array of html class attribute values. It is flattened
 *   into a string within the variable $classes.
 * - $zebra: Outputs either "even" or "odd". Useful for zebra striping in
 *   teaser listings.
 * - $id: Position of the term. Increments each time it's output.
 * - $is_front: Flags true when presented in the front page.
 * - $logged_in: Flags true when the current user is a logged-in member.
 * - $is_admin: Flags true when the current user is an administrator.
 *
 * @see template_preprocess()
 * @see template_preprocess_taxonomy_term()
 * @see template_process()
 *
 * @ingroup themeable
 */
?>
<div id="taxonomy-term-<?php print $term->tid; ?>" class="<?php print $classes; ?>" ng-controller="SourceReleaseCtrl" ng-init="init()">

    <?php if (!$page): ?>
    <h2><a href="<?php print $term_url; ?>"><?php print $term_name; ?></a></h2>
    <?php endif; ?>


    <div class="container-fluid">
        <div class="row-fluid">
            <div class="span12">
                <div class="page-heading" >
                    <h1 ><img src="<?php echo base_path() . drupal_get_path('module', 'skeletome_builder'); ?>/images/logo-large-bone-dysplasia-group.png"/> <?php print $name; ?></h1>
                </div>
            </div>
        </div>

        <div class="row-fluid">
            <div class="span7">
                <section>
                    <div class="section-segment">
                        <h3>{{ release.name }}</h3>
                    </div>

                    <div class="section-segment">
                        <search placeholder="Find a Group" model="findGroup">
                        </search>
                    </div>

                    <div ng-repeat="tag in release.tags | filter:findGroup">

                        <a class="section-segment" name="{{tag.tid}}" ng-click="getBoneDysplasiasForTag(tag)" href>

                            <i ng-show="!tag.showBoneDysplasias" class="icon-chevron-down"></i>
                            <i ng-show="!tag.showBoneDysplasias" class="icon-chevron-down icon-white"></i>
                            <i ng-show="tag.showBoneDysplasias" class="icon-chevron-up"></i>
                            <i ng-show="tag.showBoneDysplasias" class="icon-chevron-up icon-white"></i>
                            {{ tag.sk_gt_field_group_name.name }}
                        </a>

                        <div ng-show="tag.showBoneDysplasias">
                            <div class="section-segment section-segment-inner-tabbed" ng-show="!tag.boneDysplasias">
                                <i class="icon-refresh icon-refreshing"></i>
                            </div>
                            <div ng-repeat="boneDysplasia in tag.boneDysplasias | filter:findGroup">
                                <a class="section-segment section-segment-inner-tabbed" href="?q=node/{{ boneDysplasia.nid }}">
                                    {{ boneDysplasia.title }}
                                </a>
                            </div>
                        </div>
                    </div>
                </section>
            </div>

            <div class="span5">
                <section>
                    <div class="section-segment section-segment-header">
                        <h3>Releases</h3>
                    </div>

                    <div ng-repeat="release in releases">

                        <a class="section-segment" href ng-click="toggleShowRelease(release)">
                            <i class="icon-chevron-left"></i>
                            <i class="icon-chevron-left icon-white"></i>
                            {{ release.name }}
                        </a>
                    </div>
                </section>
            </div>

        </div>

    </div>

</div>
