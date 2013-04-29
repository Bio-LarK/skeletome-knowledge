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
                <div class="page-heading" >

                    <div ng-show="bone_dysplasia" class="muted">
                        From <a href="?q=node/{{ bone_dysplasia.nid }}">{{ bone_dysplasia.title }}</a>
                    </div>






                    <h1>
                        <svg class="type-logo" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" version="1.1" id="Layer_1" x="0px" y="0px" width="100px" height="100px" viewBox="0 0 100 100" enable-background="new 0 0 100 100" xml:space="preserve">
                            <path d="M61.658,86.166c2.844,0.877,4.909,3.523,4.909,6.654c0,3.842-3.115,6.957-6.959,6.957c-3.843,0-6.958-3.115-6.958-6.957  c0-2.609,1.436-4.883,3.561-6.075l0.002-0.002c-0.382-5.43-1.973-7.751-3.918-10.592c-2.688-3.923-5.718-8.345-6.01-20.389h-0.001  c-13.459-2.667-17.751-13.083-20.798-21.812c-0.174-0.498,0.575-0.991,0.425-1.477c-1.35-4.348-2.693-10.851-2.693-12.771  c0-8.573,8.115-12.408,8.115-12.408c-0.084-0.404-0.129-0.821-0.129-1.25C31.204,2.707,33.911,0,37.25,0  c3.338,0,6.046,2.707,6.046,6.046s-2.708,6.047-6.046,6.047c-1.552,0-2.968-0.585-4.038-1.547  c-6.499,4.137-7.009,8.163-5.963,12.551c0.169,0.709,1.735,6.829,2.172,8.336c0.12,0.413,1.157,0.835,1.286,1.258  c0.567,1.877,1.193,3.749,1.627,4.693C38.271,50.292,49,50.58,49,50.58s10.729-0.288,16.667-13.197  c0.434-0.944,1.06-2.816,1.627-4.693c0.129-0.422,1.166-0.845,1.285-1.258c0.437-1.507,2.004-7.626,2.173-8.336  c1.046-4.388,0.535-8.414-5.964-12.551c-1.068,0.961-2.486,1.547-4.037,1.547c-3.34,0-6.047-2.708-6.047-6.047S57.41,0,60.75,0  c3.338,0,6.045,2.707,6.045,6.046c0,0.429-0.043,0.846-0.129,1.25c0,0,8.115,3.834,8.115,12.408c0,1.92-1.344,8.423-2.693,12.771  c-0.15,0.486,0.6,0.979,0.425,1.477c-3.04,8.708-7.319,19.102-20.711,21.795l-0.04,0.008c0.268,10.32,2.697,13.867,5.051,17.303  C58.94,76.163,61.131,79.36,61.658,86.166z"/>
                        </svg>

                        {{ clinicalFeature.name }}</h1>
                </div>
            </div>
            <?php endif; ?>
        </div>

        <div class="row">


            <div class="span8">

                <section>
                    <div class="section-segment section-segment-header">
                        <h2>Bone Dysplasias</h2>
                    </div>

                    <div class="section-segment muted">
                        {{ clinicalFeature.name }} occurs in {{boneDysplasias.length}} bone dysplasias.
                    </div>

                    <div ng-repeat="boneDysplasia in boneDysplasias | orderBy:'title'">
                        <a class="section-segment" href="?q=node/{{ boneDysplasia.nid }}">
                            <i class="icon-chevron-right pull-right"></i>
                            <i class="icon-chevron-right icon-white pull-right"></i>

                            {{ boneDysplasia.title }}
                        </a>
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

            <div class="span4">
                <section>
                    <div class="section-segment section-segment-header">
                        <h3>Information Content</h3>
                    </div>
                    <div class="section-segment" ng-show="boneDysplasias.length">
                        <div class="progress">
                            <div class="bar" style="width: {{informationContent}}%"></div>
                        </div>
                    </div>
                    <div class="section-segment muted" ng-show="!boneDysplasias.length">
                        No information content.
                    </div>
                </section>

            </div>

        </div>
    </div>

</div>
