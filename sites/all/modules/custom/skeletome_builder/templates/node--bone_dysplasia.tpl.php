<?php
/**
 * @file
 * Default theme implementation to display a node.
 *
 * Available variables:
 * - $title: the (sanitized) title of the node.
 * - $content: An array of node items. Use render($content) to print them all,
 *   or print a subset such as render($content['field_example']). Use
 *   hide($content['field_example']) to temporarily suppress the printing of a
 *   given element.
 * - $user_picture: The node author's picture from user-picture.tpl.php.
 * - $date: Formatted creation date. Preprocess functions can reformat it by
 *   calling format_date() with the desired parameters on the $created variable.
 * - $name: Themed username of node author output from theme_username().
 * - $node_url: Direct URL of the current node.
 * - $display_submitted: Whether submission information should be displayed.
 * - $submitted: Submission information created from $name and $date during
 *   template_preprocess_node().
 * - $classes: String of classes that can be used to style contextually through
 *   CSS. It can be manipulated through the variable $classes_array from
 *   preprocess functions. The default values can be one or more of the
 *   following:
 *   - node: The current template type; for example, "theming hook".
 *   - node-[type]: The current node type. For example, if the node is a
 *     "Blog entry" it would result in "node-blog". Note that the machine
 *     name will often be in a short form of the human readable label.
 *   - node-teaser: Nodes in teaser form.
 *   - node-preview: Nodes in preview mode.
 *   The following are controlled through the node publishing options.
 *   - node-promoted: Nodes promoted to the front page.
 *   - node-sticky: Nodes ordered above other non-sticky nodes in teaser
 *     listings.
 *   - node-unpublished: Unpublished nodes visible only to administrators.
 * - $title_prefix (array): An array containing additional output populated by
 *   modules, intended to be displayed in front of the main title tag that
 *   appears in the template.
 * - $title_suffix (array): An array containing additional output populated by
 *   modules, intended to be displayed after the main title tag that appears in
 *   the template.
 *
 * Other variables:
 * - $node: Full node object. Contains data that may not be safe.
 * - $type: Node type; for example, story, page, blog, etc.
 * - $comment_count: Number of comments attached to the node.
 * - $uid: User ID of the node author.
 * - $created: Time the node was published formatted in Unix timestamp.
 * - $classes_array: Array of html class attribute values. It is flattened
 *   into a string within the variable $classes.
 * - $zebra: Outputs either "even" or "odd". Useful for zebra striping in
 *   teaser listings.
 * - $id: Position of the node. Increments each time it's output.
 *
 * Node status variables:
 * - $view_mode: View mode; for example, "full", "teaser".
 * - $teaser: Flag for the teaser state (shortcut for $view_mode == 'teaser').
 * - $page: Flag for the full page state.
 * - $promote: Flag for front page promotion state.
 * - $sticky: Flags for sticky post setting.
 * - $status: Flag for published status.
 * - $comment: State of comment settings for the node.
 * - $readmore: Flags true if the teaser content of the node cannot hold the
 *   main body content.
 * - $is_front: Flags true when presented in the front page.
 * - $logged_in: Flags true when the current user is a logged-in member.
 * - $is_admin: Flags true when the current user is an administrator.
 *
 * Field variables: for each field instance attached to the node a corresponding
 * variable is defined; for example, $node->body becomes $body. When needing to
 * access a field's raw values, developers/themers are strongly encouraged to
 * use these variables. Otherwise they will have to explicitly specify the
 * desired field language; for example, $node->body['en'], thus overriding any
 * language negotiation rule that was previously applied.
 *
 * @see template_preprocess()
 * @see template_preprocess_node()
 * @see template_process()
 *
 * @ingroup themeable
 */

?>



<?php if (user_access('administer site configuration')) : ?>

    <!--    <h1>is admin?</h1>-->

<?php endif; ?>


<?php if ($page): ?>

<div ng-controller="BoneDysplasiaCtrl" ng-init="init()" class="node_page" xmlns="http://www.w3.org/1999/html"
     xmlns="http://www.w3.org/1999/html">

<div class="container-fluid" ng-cloak>
<div class="row-fluid">
    <div class="span12">

        <div class="page-heading">
            <h1 ng-show="!synString.length"><img
                    src="<?php echo base_path() . drupal_get_path('module', 'skeletome_builder'); ?>/images/logo-large-bone-dysplasia.png"/> <?php print $title; ?>
            </h1>
            <h1 ng-cloak ng-show="synString.length" cm-tooltip="top"
                cm-tooltip-content="Also known as {{ synString }}"><img
                    src="<?php echo base_path() . drupal_get_path('module', 'skeletome_builder'); ?>/images/logo-large-bone-dysplasia.png"/> <?php print $title; ?>
            </h1>
        </div>

        <?php if ((user_access('administer site configuration')) || is_array($user->roles) && in_array('sk_moderator', $user->roles)): ?>
            <!--<a href="#new-bone-dysplasia" role="button" class="btn pull-right" data-toggle="modal"><i
                    class="icon-plus"></i> Add New Disorder</a>-->
        <?php endif; ?>
    </div>
</div>

<div class="row-fluid">
<div class="span8">
<!-- The content -->
<!-- ng-class="{'section-more': boneDysplasia.body.und[0].safe_value.length > 500}" -->
<section style="margin-bottom: 14px">

    <div class="section-segment section-segment-header">
        <?php if ((user_access('administer site configuration')) || is_array($user->roles) && in_array('sk_moderator', $user->roles)): ?>
            <div class="section-segment-header-buttons pull-right">

                <!-- is Editing Description -->
                                <span ng-show="!isEditingDescription">
                                    <a ng-show="!showEditDescription"
                                       href class="btn"
                                       ng-click="editDescription()">
                                        <i class="icon-pencil"></i> Edit
                                    </a>
                                </span>

                <!-- Not Editing Description -->
                                <span ng-show="isEditingDescription">
                                    <a href class="btn btn-success"
                                       ng-click="saveEditedDescription(editedDescription)">
                                        <i class="icon-ok icon-white"></i> Save Description
                                    </a>

                                    <a ng-show="isEditingDescription"
                                       href class="btn"
                                       ng-click="cancelEditingDescription()">
                                        <i class="icon-remove"></i> Cancel
                                    </a>
                                </span>


            </div>
        <?php endif; ?>

        <div>
            <b>Contributors</b>
                            <span ng-repeat="editor in editors">
                                {{ editor.name | capitalize }},
                            </span>
            <span>Gene Reviews</span>
        </div>
    </div>

    <div class="section-segment alert alert-info" ng-show="!isEditingDescription">
        <div style="margin-bottom: 7px;">
            <i class="icon-info-sign"></i> <b>This is a stub sourced from GeneReviews</b>.
        </div>
        <div style="font-size: 12px">
            Francomano, CA, (Updated January 9, 2006). Achondroplasia. In: Pagon RA, Bird TD, Dolan CR, et al. editors GeneReviews [Internet]. Copyright, University of Washington, Seattle. 1997-2011. Available at http://www.ncbi.nlm.nih.gov/pubmed/20301295
        </div>

    </div>
    <div class="section-segment" ng-class="{ 'section-segment-nopadding': isEditingDescription }">

        <!-- is Editing Description -->
        <div ng-show="isEditingDescription">
            <textarea ck-editor height="800px" ng-model="editedDescription"></textarea>
        </div>


        <!-- Not Editing Description -->
        <div ng-show="!isEditingDescription" class="description-text">
            <!-- is Loading -->
            <div ng-show="boneDysplasia.body.und[0].isLoading" class="refreshing-box">
                <i class="icon-refresh icon-refreshing"></i>
            </div>

            <p class="muted" ng-show="!boneDysplasia.body.und[0].safe_value.length">There is currently no
                description of '{{ boneDysplasia.title }}'.</p>

            <!--  | truncate:descriptionLength -->
            <div ng-show="boneDysplasia.body.und[0].safe_value.length && !boneDysplasia.body.und[0].isLoading">
                <div ng-bind-html-unsafe="boneDysplasia.body.und[0].safe_value">
                    <?php echo render($content); ?>
                </div>

                <!--<div class="clearfix" style="text-align: center">
                    <a href ng-show="boneDysplasia.body.und[0].safe_value.length > descriptionLength"
                       class="btn btn-more"
                       ng-click="descriptionLength=boneDysplasia.body.und[0].safe_value.length"><i
                            class="icon-chevron-down icon-black"></i> Show All</a>
                    <a href ng-show="descriptionLength == boneDysplasia.body.und[0].safe_value.length"
                       class="btn btn-more" ng-click="descriptionLength=1000"><i
                            class="icon-chevron-up icon-black"></i> Hide</a>
                </div>-->
            </div>
        </div>
    </div>
</section>

{{ isAddingStatement }}
<?php include('statements.php'); ?>

<section class="media-body" ng-class="{'section-more': xrays.length > xrayDisplayLimit}">
    <div class="section-segment section-segment-header">
        <?php if ((user_access('administer site configuration')) || is_array($user->roles) && in_array('sk_moderator', $user->roles)): ?>
            <div class="pull-right section-segment-header-buttons">
                <a href ng-click="showEditXRays()" data-toggle="modal" role="button" class="btn"><i
                        class="icon-pencil"></i> Edit</a>
            </div>
        <?php endif; ?>

        <h3>X-Rays</h3>
    </div>

    <?php if ((user_access('administer site configuration')) || is_array($user->roles) && in_array('sk_moderator', $user->roles)): ?>
        <div class="section-segment">
            <div class="dropzone" ng-model="xrays"
                 drop-zone-upload="?q=ajax/bone-dysplasia/{{ boneDysplasia.nid }}/xray/add">
            </div>
        </div>
    <?php endif; ?>

    <!-- No x-rays -->
    <div ng-show="!xrays.length" class="section-segment muted">
        There are no x-rays for '{{boneDysplasia.title}}'.
    </div>

    <!-- has x-rays -->
    <div ng-show="xrays.length" fancy-box="xrays" class="section-segment media-body">
        <div ng-repeat="image in xrays" class="xray-list-image">
            <a class="xray-list-image-link" rel="xrays" href="{{ image.full_url }}">
                <img ng-src="{{ image.thumb_url }}" alt=""/>
            </a>
        </div>
    </div>

    <!--<a ng-show="xrays.length > xrayDisplayLimit" ng-click="xrayDisplayLimit = xrays.length" class="btn btn-more"
       href>Show All X-Rays ({{ boneDysplasia.field_bd_xray_images.und.length }})</a>-->
</section>


<!-- Clinical Features -->
        <section id="clinical_features" class="block">
            <div class="section-segment section-segment-header">
                <div class="section-segment-header-buttons">
                    <?php if ((user_access('administer site configuration')) || is_array($user->roles) && in_array('sk_moderator', $user->roles)): ?>
                        <div class="pull-right">
                            <a href ng-click="showEditClinicalFeatures()" data-toggle="modal" role="button" class="btn"><i
                                    class="icon-pencil"></i> Edit</a>
                        </div>
                    <?php endif ?>
                </div>
                <h2>Clinical Features</h2>
            </div>

            <div class="section-segment">
                <p ng-class="{muted: !clinicalFeatures.length}">
                    '{{boneDysplasia.title}}' has {{clinicalFeatures.length}} clinical features.
                </p>
                <form style="margin-bottom: 0">
                    <search model="clinicalFeatureFilter" placeholder="Search for a Clinical Feature"></search>
                </form>
            </div>

            <div class="section-segment">
                <table class="table ">
                    <tr>
                        <th>Clinical Feature</th>
                        <th>Information Content</th>
                    </tr>
                    <tr ng-repeat="clinicalFeature in clinicalFeatures | filter:clinicalFeatureFilter | orderBy:'-information_content' | limitTo:clinicalFeatureDisplayLimit">
                        <td>
                            <a href="?q=node/{{ boneDysplasia.nid }}/clinical-feature/{{clinicalFeature.tid}}"
                               title="{{clinicalFeature.name}}">
                                {{clinicalFeature.name | truncate:40 | capitalize}}
                            </a>
                        </td>
                        <td>
                            <div class="progress">
                                <div class="bar bar-warning" style="width:{{ clinicalFeature.information_content }}%"></div>
                            </div>
                        </td>
                    </tr>
                </table>
            </div>
        </section>

<!--<div class="section-segment">
    <div ng-cloak ng-show="clinicalFeatures.length > 0">
        <table class="table table-striped table-bordered table-dark">
            <tr>
                <th>Clinical Feature</th>
                <th>Information Content</th>
            </tr>
            <tr ng-repeat="clinicalFeature in clinicalFeatures | filter:clinicalFeatureFilter | orderBy:'-information_content' | limitTo:clinicalFeatureDisplayLimit">
                <td>
                    <a href="?q=node/{{ boneDysplasia.nid }}/clinical-feature/{{clinicalFeature.tid}}"
                       title="{{clinicalFeature.name}}">
                        {{clinicalFeature.name | truncate:40 | capitalize}}
                    </a>
                </td>
                <td>
                    <div class="progress">
                        <div class="bar bar-warning" style="width:{{ clinicalFeature.information_content }}%"></div>
                    </div>
                </td>
            </tr>
        </table>

        <div class="clearfix" ng-show="clinicalFeatures.length > clinicalFeatureDisplayLimit"
             style="text-align: center">
            <a ng-show="(clinicalFeatures | filter:clinicalFeatureFilter).length > clinicalFeatureDisplayLimit"
               href class="btn btn-more" ng-click="clinicalFeatureDisplayLimit = clinicalFeatures.length"><i
                    class="icon-chevron-down icon-black"></i> Show All</a>
            <a ng-show="(clinicalFeatures | filter:clinicalFeatureFilter).length == clinicalFeatureDisplayLimit"
               href class="btn btn-more" ng-click="clinicalFeatureDisplayLimit = 10"><i
                    class="icon-chevron-up icon-black"></i> Show Less</a>
        </div>

    </div>
</div>-->
<!--</section>-->
</div>

<div class="span4">
    <section ng-show="boneDysplasia.field_bd_superbd.length">
        <div class="section-segment section-segment-header">
            <h3>Parent </h3>
        </div>

        <div ng-repeat="subType in boneDysplasia.field_bd_superbd">
            <a class="section-segment" href="?q=node/{{ subType.nid }}">
                <i class="icon-chevron-right pull-right"></i>
                <i class="icon-chevron-right icon-white pull-right"></i>

                {{ subType.title }}
            </a>
        </div>
    </section>

    <section>
        <div class="section-segment section-segment-header">
            <h3>Classifications</h3>
        </div>

        <div ng-cloak ng-repeat="tag in tags">

            <a class="section-segment" href="?q=taxonomy/term/{{ tag.tid }}">
                <i class="icon-chevron-right pull-right"></i>
                <i class="icon-chevron-right icon-white pull-right"></i>

                <b>{{ tag.sk_gt_field_group_source_release.name }}</b> &#187; {{ tag.sk_gt_field_group_name.name }}
            </a>
        </div>
    </section>

    <section>
        <div class="section-segment section-segment-header">
            <?php if ((user_access('administer site configuration')) || is_array($user->roles) && in_array('sk_moderator', $user->roles)): ?>
                <div class="pull-right section-segment-header-buttons">
                    <a ng-click="showEditDetails()" href data-toggle="modal" role="button" class="btn"><i
                            class="icon-pencil"></i> Edit</a>
                    <!--<a href="#edit-omim" data-toggle="modal" role="button"  class="btn" ><i class="icon-edit"></i> Edit</a>-->
                </div>
            <?php endif; ?>

            <h3>Details</h3>
        </div>

        <a ng-show="omim" class="section-segment" ng-href="http://www.omim.org/entry/{{omim}}" target="_blank">
            <i class="icon-globe pull-right"></i>
            <i class="icon-globe icon-white pull-right"></i>

            <span><b>OMIM</b></span>
            <span ng-show="omim">{{omim}}</span>
            <span ng-show="!omim" class="muted">Not Recorded</span>
        </a>

                    <span ng-show="!omim" class="section-segment">
                        <span><b>OMIM</b></span>
                        <span class="muted">Not Recorded</span>
                    </span>

        <div ng-show="moi" class="section-segment" target="_blank">

            <span><b>Mode of Inheritance</b></span>
            <span>{{ moi.name }}</span>
        </div>

                    <span ng-show="!moi" class="section-segment">
                        <span><b>Mode of Inheritance</b></span>
                        <span  class="muted">Not Recorded</span>
                    </span>


    </section>

    <section>
        <div class="section-segment section-segment-header">
            <?php if ((user_access('administer site configuration')) || is_array($user->roles) && in_array('sk_moderator', $user->roles)): ?>
                <div class="pull-right section-segment-header-buttons">
                    <a href ng-click="showEditGenes()" data-toggle="modal" role="button" class="btn"><i
                            class="icon-pencil"></i> Edit</a>
                </div>
            <?php endif; ?>

            <h3>Genes</h3>
        </div>


        <a ng-href="?q=node/{{boneDysplasia.nid}}/gene/{{gene.nid}}" class="section-segment" ng-repeat="gene in genes">
            {{ gene.title }}
            <i class="icon-chevron-right pull-right"></i>
            <i class="icon-chevron-right icon-white pull-right"></i>
        </a>

        <div class="section-segment muted" ng-show="!genes.length">
            '{{ boneDysplasia.title }}' is associated with {{ genes.length }} genes.
        </div>

    </section>

    <section ng-show="boneDysplasia.field_bd_subbd.length">
        <div class="section-segment section-segment-header">
            <h3>Sub-types</h3>
        </div>
        <div ng-repeat="subType in boneDysplasia.field_bd_subbd">
            <a class="section-segment" href="?q=node/{{ subType.nid }}">
                {{ subType.title }}

                <i class="icon-chevron-right pull-right"></i>
                <i class="icon-chevron-right icon-white pull-right"></i>
            </a>
        </div>

    </section>

    <section ng-show="boneDysplasia.field_bd_sameas.length">
        <div class="section-segment section-segment-header">
            <h3>Same As</h3>
        </div>
        <div ng-repeat="subType in boneDysplasia.field_bd_sameas">
            <a class="section-segment" href="?q=node/{{ subType.nid }}">
                {{ subType.title }}

                <i class="icon-chevron-right pull-right"></i>
                <i class="icon-chevron-right icon-white pull-right"></i>
            </a>
        </div>
    </section>



    <section ng-show="boneDysplasia.field_bd_seealso.length">
        <div class="section-segment section-segment-header">
            <h3>See Also</h3>
        </div>
        <div ng-repeat="subType in boneDysplasia.field_bd_seealso">
            <a class="section-segment" href="?q=node/{{ subType.nid }}">
                <i class="icon-chevron-right pull-right"></i>
                <i class="icon-chevron-right icon-white pull-right"></i>

                {{ subType.title }}
            </a>
        </div>
    </section>


    <section ng-show="similar.length">
        <div class="section-segment section-segment-header">
            <h3>Similar</h3>
        </div>
        <div ng-repeat="object in similar">
            <a class="section-segment" href="{{object.url}}">
                <i class="icon-chevron-right pull-right"></i>
                <i class="icon-chevron-right icon-white pull-right"></i>

                {{ object.label }}
            </a>
        </div>
    </section>

    <section>
        <div class="section-segment section-segment-header">
            <h3>Editors</h3>
        </div>

        <div class="section-segment" ng-repeat="editor in editors">
            <i class="icon-user"></i> {{ editor.name | capitalize }}
        </div>
    </section>
</div>


<div cm-modal="showEditingPanel" ng-switch on="editingPanel" class="modal modal-dark fade hide" tabindex="-1"
     role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">

<div class="modal-switch" ng-switch-when="edit-xrays">
    <!-- Modal Header -->
    <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
        <h3>Edit X-Rays</h3>
    </div>
    <!-- /Modal Header -->

    <!-- Modal Body -->
    <div class="modal-body">
        <div class="modal-body-inner">
            <p>Edit the X-Rays for '{{boneDysplasia.title}}'.</p>

            <ul class="xray-list unstyled media-body">

                <li class="xray-list-image-edit" ng-repeat="xray in editedXRays">
                    <div ng-show="!xray.added" ng-click="readdXRay(xray)">
                        <div class="xray-list-image-edit-image">
                            <img ng-src="{{ xray.thumb_url }}" alt=""/>
                        </div>
                        <a class="btn btn-success" href>
                            <i class="icon-plus icon-white"></i> Add
                        </a>
                    </div>

                    <div ng-show="xray.added" ng-click="removeXRay(xray)">
                        <div class="xray-list-image-edit-image">
                            <img ng-src="{{ xray.thumb_url }}" alt=""/>
                        </div>
                        <a class="btn btn-danger" href>
                            <i class="icon-remove icon-white"></i> Remove
                        </a>
                    </div>


                </li>
            </ul>
        </div>

    </div>
    <!-- /Modal Body -->

    <!-- Modal Footer -->
    <div class="modal-footer modal-footer-bottom">
        <a href class="btn btn-primary" ng-click="closeEditingPanel()"><i class="icon-ok icon-white"></i> Done</a>
    </div>
    <!-- /Modal Footer -->
</div>


<div class="modal-switch" ng-switch-when="edit-details">
    <!-- Modal Header -->
    <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
        <h3>Edit Details</h3>
    </div>
    <!-- /Modal Header -->

    <!-- Modal Body -->
    <div class="modal-body">
        <div class="modal-body-inner">
            <p>Edit the details for '{{boneDysplasia.title}}'.</p>

            <div class="section-top">
                <p>OMIM Number</p>
                <input type="input" ng-model="editedOMIM" class="full-width"/>
            </div>
            <div class="section-top">
                <p>Mode of Inheritance</p>
                <select class="full-width" ng-model="edit.editedMoi" ng-options="blah.name for blah in mois"
                        ng-init="7"></select>
            </div>
        </div>

    </div>
    <!-- /Modal Body -->

    <!-- Modal Footer -->
    <div class="modal-footer modal-footer-bottom">
        <a href class="btn btn-success" ng-click="saveDetails(editedOMIM, edit.editedMoi)"><i
                class="icon-ok icon-white"></i> Save</a>
    </div>
    <!-- /Modal Footer -->
</div>

<div class="modal-switch" ng-switch-when="add-statement">
    <!-- Modal Header -->
    <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
        <h3>Add New Statement</h3>
    </div>
    <!-- /Modal Header -->

    <!-- Modal Body -->
    <div class="modal-body">
        <div class="modal-body-inner">
            <p>Add a statement about '{{boneDysplasia.title}}'.</p>
            <textarea ck-editor ng-model="newStatement"></textarea>
        </div>

    </div>
    <!-- /Modal Body -->

    <!-- Modal Footer -->
    <div class="modal-footer modal-footer-bottom">
        <a href class="btn btn-success" ng-click="addStatement(newStatement); newStatement = ''"><i
                class="icon-plus icon-white"></i> Add Statement</a>
    </div>
    <!-- /Modal Footer -->
</div>

<div class="modal-switch" ng-switch-when="edit-description">
    <!-- Modal Header -->
    <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
        <h3>Edit Description</h3>
    </div>
    <!-- /Modal Header -->

    <!-- Modal Body -->
    <div class="modal-body">
        <div class="modal-body-inner">
            <p>Edit the Description for '{{boneDysplasia.title}}'.</p>

            <textarea ck-editor ng-model="$parent.editedDescription"></textarea>
        </div>

    </div>
    <!-- /Modal Body -->

    <!-- Modal Footer -->
    <div class="modal-footer modal-footer-bottom">
        <a href class="btn btn-success" ng-click="saveEditedDescription(editedDescription)"><i
                class="icon-ok icon-info"></i> Save DescriptionDescription</a>
    </div>
    <!-- /Modal Footer -->
</div>


<div class="modal-switch" ng-switch-when="edit-genes">
    <!-- Modal Header -->
    <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
        <h3>Edit Genes</h3>
    </div>
    <!-- /Modal Header -->

    <!-- Modal Body -->
    <div class="modal-body">
        <div class="modal-body-inner">
            <p>Search for a Gene to Add/Remove to '{{boneDysplasia.title}}'.</p>

            <!-- Search box -->
            <form>
                <search model="$parent.editGeneSearch"
                        change="searchForGenes($parent.editGeneSearch); newGeneName=editGeneSearch"
                        placeholder="Search for a Gene"></search>
            </form>


            <!-- /Search box -->
            <div style="margin-bottom: 20px">
                <div ng-show="editGeneLoading > 0">
                    Loading...
                </div>

                <div ng-repeat="gene in editingGenes" style="overflow: auto; margin-bottom: 10px">
                    <strong>{{ gene.title }}</strong>
                    <a ng-show="!gene.added" ng-click="addGeneToBoneDysplasia(gene, boneDysplasia)"
                       class="btn btn-success pull-right" href><i class="icon-plus icon-white"></i> Add</a>
                    <a ng-show="gene.added" ng-click="removeGeneFromBoneDysplasia(gene, boneDysplasia)"
                       class="btn btn-danger pull-right" href><i class="icon-minus icon-white"></i> Remove</a>
                </div>

                <div ng-show="showAddNewGeneForm">
                    <p class="muted">Gene not found. Add the Gene to Skeletome.</p>

                    <div class="input-append">
                        <input placeholder="Enter a Gene Name" ng-model="newGeneName" type="text"/>
                        <a ng-click="addNewGeneToBoneDysplasia(newGeneName, boneDysplasia)" class="btn btn-success"><i
                                class="icon-plus icon-white"></i> Add Gene</a>
                    </div>
                </div>
            </div>


            <!--<table class="table table-center">
                <tr>
                    <th>Gene</th>
                </tr>


                <tbody ng-repeat="gene in editingGenes">
                    <tr>
                        <td>
                            <strong>{{ gene.title | truncate:20 }}</strong>
                            <a ng-click="gene.showGeneMutations = true" ng-show="!gene.showGeneMutations" class="btn btn-primary pull-right" href><i class="icon-chevron-down icon-white"></i> Show</a>
                            <a ng-click="gene.showGeneMutations = false" ng-show="gene.showGeneMutations" class="btn btn-primary pull-right" href><i class="icon-chevron-up icon-white"></i> Hide</a>
                        </td>
                    </tr>
                    <tr ng-show="gene.showGeneMutations">
                        <td class="table-subcell table-subcell-first">
                            <div class="input-append input-append-add_gene">
                                <input ng-model="gene.geneMutationTitle" placeholder="Enter New Gene Mutation" type="text" class="full-width"/>
                                <a ng-click="addNewGeneMutationToBoneDysplasia(gene.geneMutationTitle, gene, boneDysplasia)" class="btn btn-success" ><i class="icon-plus icon-white"></i> Add</a>
                            </div>

                        </td>
                    </tr>
                    <tr ng-show="gene.showGeneMutations" ng-repeat="geneMutation in gene.field_gene_gene_mutation">
                        <td class="table-subcell"> {{geneMutation.title }}
                            <a ng-click="addGeneMutation(geneMutation, gene, boneDysplasia)" ng-show="!geneMutation.added" class="btn btn-success pull-right"><i class="icon-plus icon-white"></i> Add</a>
                            <a ng-click="removeGeneMutation(geneMutation, gene, boneDysplasia)" ng-show="geneMutation.added" class="btn btn-danger pull-right"><i class="icon-minus icon-white"></i> Remove</a>
                        </td>
                    </tr>
                </tbody>
            </table>-->

            <!-- Helpful Prompt (show when no text is entered, and no existing genes -->
            <p ng-show="!editGeneSearch" class="muted info">Want to find another Gene? <br/>Try using the search bar
                above e.g. '<a href
                               ng-click="$parent.editGeneSearch = 'FGFR3'; searchForGenes($parent.editGeneSearch)">FGFR3</a>'
            </p>
            <!-- /Helpful Prompt -->
        </div>
    </div>
    <!-- /Modal Body -->

    <!-- Modal Footer -->
    <div class="modal-footer modal-footer-bottom">
        <a href class="btn btn-primary" ng-click="closeEditingPanel()"><i class="icon-ok icon-white"></i> Done</a>
    </div>
    <!-- /Modal Footer -->
</div>

<div class="modal-switch" ng-switch-when="edit-features">
    <!-- Modal Header -->
    <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
        <h3>Edit Clinical Features</h3>
    </div>
    <!-- /Modal Header -->

    <!-- Modal Body -->
    <div class="modal-body">
        <div class="modal-body-inner">
            <clinical-feature-adder></clinical-feature-adder>

            <!--<p>Edit Clinical Features to attached to '{{boneDysplasia.title}}'.</p>

            <form>
                <search model="$parent.editClinicalFeatureSearch"
                        change="searchForClinicalFeatures(editClinicalFeatureSearch)"
                        placeholder="Search for a Clinical Feature"></search>
            </form>


            <table class="table table-center">
                <tr>
                    <th>Clinical Feature</th>
                    <th>Action</th>
                </tr>
                <tr ng-repeat="clinicalFeature in editingClinicalFeatures | filter:editClinicalFeatureSearch">
                    <td>{{ clinicalFeature.name | truncate:40 }}</td>

                    <td>
                        <a role="button" ng-show="clinicalFeature.added" class="btn btn-danger pull-right"
                           ng-click="removeClinicalFeature(clinicalFeature, boneDysplasia)"><i
                                class="icon-remove icon-white"></i> Remove</a>
                        <a role="button" ng-show="!clinicalFeature.added" class="btn btn-success pull-right"
                           ng-click="addClinicalFeature(clinicalFeature, boneDysplasia)"><i
                                class="icon-plus icon-white"></i> Add</a>
                    </td>
                </tr>
            </table>-->

            <!-- Helpful Prompt (show when no text is entered, and no existing genes -->
            <p class="muted info">Want to find another Clinical Feature? <br/>Try using the search bar above e.g.
                '<a href
                    ng-click="$parent.editClinicalFeatureSearch = 'Frontal Bossing'; searchForClinicalFeatures(editClinicalFeatureSearch)">Frontal
                    Bossing</a>'</p>
            <!-- /Helpful Prompt -->

        </div>
    </div>
    <!-- /Modal Body -->

    <!-- Modal Footer -->
    <div class="modal-footer modal-footer-bottom">
        <a href class="btn btn-primary" ng-click="closeEditingPanel()"><i class="icon-ok icon-white"></i> Done</a>
    </div>
    <!-- /Modal Footer -->
</div>
</div>


<div id="new-bone-dysplasia" class="modal modal-dark hide fade" tabindex="-1" role="dialog"
     aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
        <h3>Add New Bone Dysplasia</h3>
    </div>
    <div class="modal-body">
        <label>What is the name of the disorder?</label>
        <input placeholder="Name of Disorder" class="full-width" type="text" ng-model="newDisorderName"/>
    </div>
    <div class="modal-footer modal-footer-bottom">
        <button class="btn btn-success" ng-disabled="!newDisorderName.length"
                ng-click="createNewBoneDysplasia(newDisorderName)">Create Bone Dysplasia
        </button>
    </div>
</div>


</div>



<?php endif; ?>