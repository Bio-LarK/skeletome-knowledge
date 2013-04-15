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

<div id="taxonomy-term-<?php print $term->tid; ?>" class="<?php print $classes; ?>" ng-controller="GroupCtrl">

    <?php if (!$page): ?>
    <h2><a href="<?php print $term_url; ?>"><?php print $term_name; ?></a></h2>
    <?php endif; ?>

    <div class="content">
        <?php //print render($content); ?>
    </div>

    <div class="container">
        <div class="row">
            <div class="span12">
                <div class="page-header" >

                    <ul class="breadcrumbs">
                        <li>
                            <a href="?q=taxonomy/term/{{ source.tid }}">{{ sourceRelease.name }}</a>
                        </li>
                    </ul>

                    <h1><img src="<?php echo base_path() . drupal_get_path('module', 'skeletome_builder'); ?>/images/logo-large-bone-dysplasia-group.png"/> {{ groupName.name }}<?php //print $name; ?> <small>Group</small></h1>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="span7">
                <section class="section-large">
                    <h2>Members</h2>
                    <table class="table table-striped table-bordered table-dark">
                        <tr>
                            <th>Bone Dysplasia</th>
                        </tr>
                        <tr ng-repeat="member in members">
                            <td><a ng-href="?q=node/{{ member.vid }}">{{ member.title }}</a></td>
                        </tr>
                    </table>

                </section>
                <!--<section class="section-large">
                    <h2>Genetic Information</h2>
                    <table class="table table-striped table-bordered table-dark">
                        <tr>
                            <th>Gene</th>
                            <th>Gene Mutation</th>
                            <th>Mutation Type</th>
                        </tr>
                        <tbody ng-repeat="gene in genes">
                        <tr ng-repeat="geneMutation in gene.field_gene_gene_mutation">

                            <td><a ng-href="?q=node/{{gene.gene.nid}}">{{ gene.title}}</a></td>
                            <td>{{ geneMutation.title}}</td>
                            <td>{{ geneMutation.field_gm_mutation_type.title }}</td>
                        </tr>
                        </tbody>
                    </table>
                </section>-->
                <section class="section-large">
                    <h2>Common Clinical Features</h2>
                    <p ng-show="clinicalFeatures.length == 0">The are no common clinical features for these disorders.</p>
                    <table ng-show="clinicalFeatures.length > 0" class="table table-striped table-bordered table-dark">
                        <tr>
                            <th>Clinical Feature</th>
                            <th>Information Content</th>
                        </tr>
                        <tr ng-repeat="clinicalFeature in clinicalFeatures | orderBy:'-information_content'">
                            <td><a href="?q=taxonomy/term/{{clinicalFeature.tid}}">{{clinicalFeature.name}}</a></td>
                            <td>
                                <div class="progress">
                                    <div class="bar" style="width:{{ clinicalFeature.information_content }}%"></div>
                                </div>
                            </td>
                        </tr>
                    </table>
                </section>
            </div>
            <div class="span5">
                <section>
                    <h3>Source</h3>
                    <a ng-show="source" href="?q=taxonomy/term/{{source.tid}}"><i class="icon-list"></i> {{ source.name }}</a>
                    <!--<table class="table table-dark table-bordered table-striped">
                        <tr>
                            <th>Name</th>
                            <th>Type</th>
                        </tr>
                        <tr>
                            <td><i class="icon-list"></i> {{ groupName.name }}</td>
                            <td>Name</td>
                        </tr>
                        <tr>
                            <td><a href="?q=taxonomy/term/{{ source.tid }}"><i class="icon-list"></i> {{ source.name }}</a></td>
                            <td>Source</td>
                        </tr>
                        <tr>
                            <td><i class="icon-list"></i> {{ sourceRelease.name }}</td>
                            <td>Edition</td>
                        </tr>
                    </table>-->
                    <a ng-show="groupName" href></a>

                    <a ng-show="sourceRelease" href></a>
                </section>
                <!--<section>
                    <h3>See Also</h3>

                    <p>Todo: Add in see also</p>
                </section>-->
            </div>
        </div>
    </div>
</div>