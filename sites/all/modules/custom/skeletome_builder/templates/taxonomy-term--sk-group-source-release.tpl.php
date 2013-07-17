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
                    <div class="breadcrumbs">
                        <span><a href="{{ baseUrl }}">Home</a> &#187; </span>
                    </div>
                    <h1 >
                        <span class="type-logo"><i class="icon-group"></i></span>
                        <!--<span ng-bind-html-unsafe="release.sk_gsr_field_group_source.und[0].taxonomy_term.name + ' Nosology'"><?php print $name; ?></span>-->
                        <span>Bone Dysplasia Ontology</span>
                    </h1>
                </div>
            </div>
        </div>

        <div class="row-fluid">
            <div class="span7">
                <section>
                    <div class="section-segment">
                        <h3>BDO</h3>
                    </div>

                    <div class="section-segment" lock-to-top>
                        <search placeholder="Find a Group" model="findGroup">
                        </search>
                    </div>

                    <div ng-repeat="tag in release.tags | filter:findGroup">

                        <a class="section-segment" name="{{tag.tid}}" ng-click="getBoneDysplasiasForTag(tag)" href short-highlight="{{ hashId == tag.tid }}">

                            <i ng-show="!tag.showBoneDysplasias" class="ficon-angle-right"></i>
                            <i ng-show="tag.showBoneDysplasias" class="ficon-angle-up"></i>

                            <span ng-bind-html-unsafe="tag.sk_gt_field_group_name.name | highlight:findGroup"></span>
                        </a>

                        <div ng-show="tag.showBoneDysplasias">
                            <div ng-show="!tag.boneDysplasias"> <!--  class="section-segment section-segment-inner-tabbed" -->
                                <refresh-box></refresh-box>
                            </div>
                            <div ng-repeat="boneDysplasia in tag.boneDysplasias | filter:findGroup">
                                <a class="section-segment section-segment-inner-tabbed" href="?q=node/{{ boneDysplasia.nid }}">

                                    <i class="ficon-angle-right pull-right"></i>
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
                            <i class="ficon-angle-left"></i>
                            <!--{{ release.name }}-->
                            BDO 2013
                        </a>
                    </div>
                </section>
            </div>

        </div>

    </div>

</div>
