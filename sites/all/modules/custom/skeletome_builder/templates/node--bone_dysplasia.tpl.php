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


    <div class="container" ng-cloak>
    <div class="row">
        <div class="span12">
            <div class="page-header">

                <?php if ((user_access('administer site configuration')) || is_array($user->roles) && in_array('sk_moderator', $user->roles)): ?>
                    <a href="#new-bone-dysplasia" role="button" class="btn pull-right" data-toggle="modal"><i
                            class="icon-plus"></i> Add New Disorder</a>
                <?php endif; ?>

                <ul ng-cloak class="breadcrumbs" ng-repeat="tag in tags">
                    <li>
                        <a href="?q=search/site/&f[0]=bundle%3Abone_dysplasia">Bone Dysplasias</a>
                    </li>

                    <li>
                        <a href="?q=taxonomy/term/{{ tag.sk_gt_field_group_source_release.sk_gsr_field_group_source.tid }}">{{
                            tag.sk_gt_field_group_source_release.sk_gsr_field_group_source.name }}</a>
                    </li>
                    <li>
                        <a href="?q=taxonomy/term/{{ tag.tid }}">{{ tag.sk_gt_field_group_name.name }}</a>
                    </li>
                    <li ng-show="boneDysplasia.field_bd_superbd.length">
                        <a href="?q=node/{{ boneDysplasia.field_bd_superbd[0].nid }}">{{
                            boneDysplasia.field_bd_superbd[0].title }}</a>
                    </li>
                </ul>

                <!-- <ul class="breadcrumbs" ng-repeat="tag in tags">
                     <li>
                         <a href="?q=search/site/&f[0]=bundle%3Abone_dysplasia">Bone Dysplasias</a>
                     </li>

                     <li>
                         <a href="?q=taxonomy/term/{{ tag.sk_gt_field_group_source_release.sk_gsr_field_group_source.tid }}">{{ tag.sk_gt_field_group_source_release.sk_gsr_field_group_source.name }}</a>
                     </li>
                     <li>
                         <a href="?q=taxonomy/term/{{ tag.tid }}">{{ tag.sk_gt_field_group_name.name }}</a>
                     </li>
                     <li ng-show="boneDysplasia.field_bd_superbd.length">
                         <a href="?q=node/{{ boneDysplasia.field_bd_superbd[0].nid }}">{{ boneDysplasia.field_bd_superbd[0].title }}</a>
                     </li>
                 </ul>-->


                <h1 ng-show="!synString.length"><img
                        src="<?php echo base_path() . drupal_get_path('module', 'skeletome_builder'); ?>/images/logo-large-bone-dysplasia.png"/> <?php print $title; ?>
                    <small>Bone Dysplasia</small>
                </h1>
                <h1 ng-cloak ng-show="synString.length" cm-tooltip="top"
                    cm-tooltip-content="Also known as {{ synString }}"><img
                        src="<?php echo base_path() . drupal_get_path('module', 'skeletome_builder'); ?>/images/logo-large-bone-dysplasia.png"/> <?php print $title; ?>
                    <small>Bone Dysplasia</small>
                </h1>
            </div>
        </div>
    </div>


    <div class="row">
    <div class="span8">

        <!-- The content -->
        <section ng-class="{'section-more': boneDysplasia.body.und[0].safe_value.length > descriptionLength}"
                 class="section-large section-large-description">
            <?php if ((user_access('administer site configuration')) || is_array($user->roles) && in_array('sk_moderator', $user->roles)): ?>
                <div class="pull-right">
                    <a ng-show="!showEditDescription"
                       href class="btn"
                       ng-click="editDescription()">
                        <i class="icon-pencil"></i> Edit
                    </a>

                    <a ng-show="showEditDescription"
                       href class="btn btn-success"
                       ng-click="saveEditedDescription(editedDescription)">
                        <i class="icon-ok icon-white"></i> Save Description
                    </a>

                    <a ng-show="showEditDescription"
                       href class="btn btn-primary"
                       ng-click="cancelEditingDescription()">
                        <i class="icon-remove icon-white"></i> Cancel
                    </a>

                </div>
            <?php endif; ?>

            <h2>Description</h2>

            <div ng-show="showEditDescription">
                <textarea ck-editor ng-model="editedDescription"></textarea>
            </div>

            <div ng-show="!showEditDescription" class="description-text">
                <p class="muted" ng-show="!boneDysplasia.body.und[0].safe_value.length">There is currently no
                    description of '{{ boneDysplasia.title }}'.</p>

                <div ng-show="boneDysplasia.body.und[0].safe_value.length">
                    <div ng-bind-html-unsafe="boneDysplasia.body.und[0].safe_value | truncate:descriptionLength">
                    </div>

                    <div class="clearfix" style="text-align: center">
                        <a href ng-show="boneDysplasia.body.und[0].safe_value.length > descriptionLength"
                           class="btn btn-more"
                           ng-click="descriptionLength=boneDysplasia.body.und[0].safe_value.length"><i
                                class="icon-chevron-down icon-black"></i> Show All</a>
                        <a href ng-show="descriptionLength == boneDysplasia.body.und[0].safe_value.length"
                           class="btn btn-more" ng-click="descriptionLength=1000"><i
                                class="icon-chevron-up icon-black"></i> Hide</a>
                    </div>
                </div>
            </div>
        </section>

        <?php include('statements.php'); ?>

        <!-- Clinical Features -->
        <section class="section-large section-large-noborder"
                 ng-class="{'section-more': clinicalFeatures.length > clinicalFeatureDisplayLimit}"
                 id="clinical_features" class="block">
            <?php if ((user_access('administer site configuration')) || is_array($user->roles) && in_array('sk_moderator', $user->roles)): ?>
                <div class="pull-right">
                    <a href ng-click="showEditClinicalFeatures()" data-toggle="modal" role="button" class="btn"><i
                            class="icon-pencil"></i> Edit</a>
                </div>
            <?php endif ?>

            <h2>Clinical Features</h2>

            <p ng-class="{muted: !clinicalFeatures.length}">
                '{{boneDysplasia.title}}' has {{clinicalFeatures.length}} clinical features.
            </p>

            <div ng-cloak ng-show="clinicalFeatures.length > 0">
                <form>
                    <search model="clinicalFeatureFilter" placeholder="Search for a Clinical Feature"></search>
                </form>

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
                                <div class="bar" style="width:{{ clinicalFeature.information_content }}%"></div>
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

        </section>
    </div>

    <div class="span4">

        <section>
            <?php if ((user_access('administer site configuration')) || is_array($user->roles) && in_array('sk_moderator', $user->roles)): ?>
                <div class="pull-right">
                    <a ng-click="showEditDetails()" href data-toggle="modal" role="button" class="btn"><i
                            class="icon-pencil"></i> Edit</a>
                    <!--<a href="#edit-omim" data-toggle="modal" role="button"  class="btn" ><i class="icon-edit"></i> Edit</a>-->
                </div>
            <?php endif; ?>

            <h3>Details</h3>
            <dl class="dl-horizontal dl-horizontal-squished" ng-show="omim.length">
                <dt>OMIM</dt>
                <dd ng-show="omim"><a ng-href="http://www.omim.org/entry/{{omim}}" target="_blank">{{omim}}</a></dd>
                <dd class="muted" ng-show="!omim">Not Recorded</dd>

                <dl class="dl-horizontal dl-horizontal-squished" ng-show="moi.name.length">
                    <dt>Mode of Inheritance</dt>
                    <dd ng-show="moi"><a ng-href="{{moi.field_moi_term_uri.und[0].value}}"
                                         target="_blank">{{moi.name}}</a></dd>
                    <dd class="muted" ng-show="!moi">Not Recorded</dd>
                </dl>
        </section>

        <section>
            <?php if ((user_access('administer site configuration')) || is_array($user->roles) && in_array('sk_moderator', $user->roles)): ?>
                <div class="pull-right">
                    <a href ng-click="showEditGenes()" data-toggle="modal" role="button" class="btn"><i
                            class="icon-pencil"></i> Edit</a>
                </div>
            <?php endif; ?>

            <h3>Genes</h3>

            <p ng-show="!genes.length" class="muted">'{{ boneDysplasia.title }}' is associated with {{ genes.length }}
                gene.</p>
            <ul>
                <li ng-repeat="gene in genes"><a ng-href="?q=node/{{boneDysplasia.nid}}/gene/{{gene.nid}}">{{ gene.title
                        }}</a></li>
            </ul>
        </section>

        <section ng-show="boneDysplasia.field_bd_subbd.length">
            <h3>Sub-types</h3>
            <ul>
                <li ng-repeat="subType in boneDysplasia.field_bd_subbd">
                    <a href="?q=node/{{ subType.nid }}">{{ subType.title }}</a></li>
            </ul>
        </section>

        <section ng-show="boneDysplasia.field_bd_sameas.length">
            <h3>Same As</h3>
            <ul>
                <li ng-repeat="subType in boneDysplasia.field_bd_sameas">
                    <a href="?q=node/{{ subType.nid }}">{{ subType.title }}</a></li>
            </ul>
        </section>

        <section ng-show="boneDysplasia.field_bd_superbd.length">
            <h3>Parent Type</h3>
            <ul>
                <li ng-repeat="subType in boneDysplasia.field_bd_superbd">
                    <a href="?q=node/{{ subType.nid }}">{{ subType.title }}</a></li>
            </ul>
        </section>

        <section ng-show="boneDysplasia.field_bd_seealso.length">
            <h3>See Also</h3>
            <ul>
                <li ng-repeat="subType in boneDysplasia.field_bd_seealso">
                    <a href="?q=node/{{ subType.nid }}">{{ subType.title }}</a></li>
            </ul>
        </section>

        <section class="media-body" ng-class="{'section-more': xrays.length > xrayDisplayLimit}">
            <?php if ((user_access('administer site configuration')) || is_array($user->roles) && in_array('sk_moderator', $user->roles)): ?>
                <div class="pull-right">
                    <a href ng-click="showEditXRays()" data-toggle="modal" role="button" class="btn"><i
                            class="icon-pencil"></i> Edit</a>
                </div>
            <?php endif; ?>

            <h3>X-Rays</h3>
            <?php if ((user_access('administer site configuration')) || is_array($user->roles) && in_array('sk_moderator', $user->roles)): ?>
                <div class="dropzone" ng-model="xrays"
                     drop-zone-upload="?q=ajax/bone-dysplasia/{{ boneDysplasia.nid }}/xray/add">
                </div>
            <?php endif; ?>

            <p ng-show="!xrays.length" class="muted">There are no x-rays for '{{boneDysplasia.title}}'.</p>

            <ul class="xray-list unstyled media-body" fancy-box="xrays">
                <li class="xray-list-image" ng-repeat="image in xrays | limitTo:xrayDisplayLimit">
                    <a class="xray-list-image-link" rel="xrays" href="{{ image.full_url }}">
                        <img ng-src="{{ image.thumb_url }}" alt=""/>
                    </a>
                </li>
            </ul>

            <a ng-show="xrays.length > xrayDisplayLimit" ng-click="xrayDisplayLimit = xrays.length" class="btn btn-more"
               href>Show All X-Rays ({{ boneDysplasia.field_bd_xray_images.und.length }})</a>
        </section>


        <section ng-show="similar.length">
            <h3>Similar</h3>
            <ul>
                <li ng-repeat="object in similar">
                    <a href="{{object.url}}">{{ object.label }}</a>
                </li>
            </ul>
        </section>

        <section>
            <h3>Editors</h3>
            <ul class="unstyled">
                <li ng-repeat="editor in editors">
                    <i class="icon-user"></i> {{ editor.name | capitalize }}
                </li>
            </ul>
        </section>


    </div>
    </div>
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
                    class="icon-ok icon-white"></i> Save Description</a>
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
                <p>Edit Clinical Features to attached to '{{boneDysplasia.title}}'.</p>

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
                </table>

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
