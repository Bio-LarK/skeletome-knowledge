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
<div id="taxonomy-term-<?php print $term->tid; ?>" class="<?php print $classes; ?>" ng-controller="ClinicalFeatureCtrl">

    <div class="container">
        <div class="row">
            <?php if ($name): ?>
            <div class="span12">
                <div class="page-header" >

                    <ul class="breadcrumbs" >
                        <li>Clinical Features </li>
                        <li ng-show="bone_dysplasia">
                            <a href="?q=node/{{ bone_dysplasia.nid }}">{{ bone_dysplasia.title }}</a>
                        </li>
                    </ul>

                    <h1><img src="<?php echo base_path() . drupal_get_path('module', 'skeletome_builder'); ?>/images/logo-large-clinical-feature.png"/> {{ clinicalFeature.name }} <small>Clinical Feature</small></h1>
                </div>
            </div>
            <?php endif; ?>
        </div>

        <div class="row">


            <div class="span8">
                <!--<section class="section-large">
                    <h2>Description</h2>
                    <div class="description-text">
                        {{ clinicalFeature }}
                        <p class="muted" ng-show="clinicalFeature.body.und[0].value.length == 0">There is currently no description of '{{ clinicalFeature.title | capitalize }}'.</p>

                        <div ng-show="clinicalFeature.body.und[0].value.length > 0" >
                            <div ng-bind-html-unsafe="clinicalFeature.body.und[0].value | truncate:descriptionLength">
                            </div>
                            <div class="clearfix">
                                <a href ng-show="clinicalFeature.body.und[0].value.length > descriptionLength" class="btn btn-primary pull-right" ng-click="descriptionLength = clinicalFeature.body.und[0].value.length"><i class="icon-chevron-down icon-white"></i> Show All</a>
                                <a href ng-show="descriptionLength == clinicalFeature.body.und[0].value.length" class="btn btn-primary pull-right" ng-click="descriptionLength=1000"><i class="icon-chevron-up icon-white"></i> Hide</a>
                            </div>
                        </div>
                    </div>
                </section>-->
                <section class="section-large section-large-noborder section-more">
                    <h2>Bone Dysplasias</h2>
                    <p>{{ clinicalFeature.name }} occurs in {{boneDysplasias.length}} bone dysplasias. </p>
                    <table ng-show="boneDysplasias.length > 0" class="table table-bordered table-striped table-dark">
                        <tr>
                            <th>Bone Dysplasia</th>
                        </tr>
                        <tr ng-repeat="boneDysplasia in boneDysplasias | orderBy:'title' | limitTo:boneDysplasiaDisplayLimit">
                            <td><a href="?q=node/{{ boneDysplasia.nid }}">{{ boneDysplasia.title }}</a></td>
                        </tr>
                    </table>
                    <div class="clearfix" ng-show="boneDysplasiaDisplayLimit < boneDysplasias.length" >
                        <a href class="btn btn-more pull-right" ng-click="boneDysplasiaDisplayLimit=boneDysplasiaDisplayLimit+10"><i class="icon-chevron-down icon-white"></i> Show More</a>
                    </div>
                </section>
                <!--<section class="section-large">
                    <h2>Genetic Information</h2>
                    <p>{{ clinicalFeature.name }} is associated with {{genes.length}} genes. </p>
                    <table ng-show="boneDysplasias.length > 0" class="table table-striped table-bordered table-dark">
                        <tr>
                            <th>Gene</th>
                            <th>Gene Mutation</th>
                            <th>Mutation Type</th>
                        </tr>
                        <tbody ng-repeat="gene in genes">
                            <tr ng-repeat="geneMutation in gene.field_gene_gene_mutation">

                                <td><a ng-href="?q=node/{{gene.nid}}">{{ gene.title}}</a></td>
                                <td>{{ geneMutation.title}}</td>
                                <td>{{ geneMutation.field_gm_mutation_type.title }}</td>
                            </tr>
                        </tbody>
                    </table>
                </section>-->

            </div>

            <div class="span4" ng-show="boneDysplasias.length">
                <section>
                    <h3>Information Content</h3>
                    <!--                <p>{{ clinicalFeature.name }} occurs in {{boneDysplasias.length}} disorders.</p>-->
                    <div class="progress">
                        <div class="bar" style="width: {{informationContent}}%"></div>
                    </div>
                </section>

            </div>

        </div>
    </div>

</div>
