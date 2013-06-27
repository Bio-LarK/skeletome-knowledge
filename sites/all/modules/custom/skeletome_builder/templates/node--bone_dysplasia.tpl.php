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


<?php
// Create some user access variables
$isRegistered = isset($user->uid) && $user->uid != 0;
$isCurator = is_array($user->roles) && in_array('sk_curator', $user->roles);
$isEditor = is_array($user->roles) && in_array('sk_editor', $user->roles);
$isAdmin = user_access('administer site configuration');
?>


<?php if ($page): ?>

<div ng-controller="BoneDysplasiaCtrl" ng-init="init()" class="node_page" xmlns="http://www.w3.org/1999/html"
     xmlns="http://www.w3.org/1999/html">

<div  ng-cloak>
<div class="row-fluid">
    <div class="span12">

        <div class="page-heading">
            <div class="breadcrumbs">
                <span ng-cloak>
                    <span><a href="{{ baseUrl }}">Home</a> &#187; </span>
                    <span><a href="?q=taxonomy/term/{{ tags[0].sk_gt_field_group_source_release.tid }}">BDO</a> &#187; </span>
                    <a href="?q=taxonomy/term/{{ tags[0].tid }}">
                        {{ tags[0].sk_gt_field_group_name.name }}
                    </a>
                </span>
            </div>
            <h1 ng-show="!synString.length">
                <img class="type-logo" src="<?php echo base_path() . drupal_get_path('module', 'skeletome_builder'); ?>/images/bone_dysplasia_logo.svg"/>
                <?php print $title; ?>
            </h1>
            <h1 ng-cloak ng-show="synString.length" cm-tooltip="top"
                cm-tooltip-content="Also known as {{ synString }}">

                <img class="type-logo" src="<?php echo base_path() . drupal_get_path('module', 'skeletome_builder'); ?>/images/bone_dysplasia_logo.svg"/>
                <?php print $title; ?>
            </h1>
        </div>

        <?php if ($isAdmin || $isCurator): ?>
            <!--<a href="#new-bone-dysplasia" role="button" class="btn pull-right" data-toggle="modal"><i
                    class="icon-plus"></i> Add New Disorder</a>-->
        <?php endif; ?>
    </div>
</div>

<div class="row-fluid">
<div class="span8">

    <?php include('bone_dysplasia/description.php'); ?>

    <?php include('bone_dysplasia/statements.php'); ?>

    <?php include('bone_dysplasia/xrays.php'); ?>

    <?php include('bone_dysplasia/clinical_features.php'); ?>

</div>


<div class="span4">
    <section ng-show="model.boneDysplasia.field_bd_superbd.length">
        <div class="section-segment section-segment-header">
            <h3>Parent </h3>
        </div>

        <div ng-repeat="subType in model.boneDysplasia.field_bd_superbd">
            <a class="section-segment" href="?q=node/{{ subType.nid }}">
                <i class="ficon-angle-right pull-right"></i>


                {{ subType.title }}
            </a>
        </div>
    </section>

    <!--<section>
        <div class="section-segment section-segment-header">
            <h3>Classifications</h3>
        </div>

        <div ng-cloak ng-repeat="tag in tags">

            <a class="section-segment" href="?q=taxonomy/term/{{ tag.tid }}">
                <i class="ficon-angle-right pull-right"></i>


                <b>{{ tag.sk_gt_field_group_source_release.name }}</b> &#187; {{ tag.sk_gt_field_group_name.name }}
            </a>
        </div>
    </section>-->

    <section>
        <div class="section-segment section-segment-header" ng-class="{ 'section-segment-editing': model.detailsState=='isEditing' }">
            <?php if ($isAdmin || $isEditor || $isCurator): ?>
                <div class="pull-right section-segment-header-buttons">
                    <div ng-switch on="model.detailsState">
                        <div ng-switch-when="isLoading">
                        </div>
                        <div ng-switch-when="isEditing">
                            <save-button click="saveDetails()"></save-button>

                            <a href ng-click="cancelDetails()" class="btn btn-cancel">
                                <i class="ficon-remove"></i> Cancel
                            </a>
                        </div>
                        <div ng-switch-when="isDisplaying">
                            <a href ng-click="editDetails()" class="btn btn-edit">
                                <i class="ficon-pencil"></i> Edit
                            </a>
                        </div>
                    </div>
                </div>
            <?php endif; ?>

            <h3>Details</h3>
        </div>

        <cm-alert state="model.detailsState" from="isLoading" to="isDisplaying">
            <i class="ficon-ok"></i> Details Updated.
        </cm-alert>

        <div ng-switch on="model.detailsState">
            <div ng-switch-when="isLoading">
                <div class="section-segment">
                    <div class="refreshing-box">
                        <i class="icon-refresh icon-refreshing"></i>
                    </div>
                </div>
            </div>
            <div ng-switch-when="isEditing">
                <div class="section-segment section-segment-editing">
                    <b>OMIM</b>
                    <div>
                        <input class="full-width" ng-model="model.edit.omim" type="text"/>
                    </div>
                </div>

                <div class="section-segment section-segment-editing">
                    <b>Mode of Inheritance</b>
                    <div>
                        <select class="full-width"
                                ng-model="model.edit.moi"
                                ng-options="moi.name for moi in model.edit.allMois">
                        </select>
                    </div>
                </div>
            </div>
            <div ng-switch-when="isDisplaying">
                <div>
                    <a ng-show="omim" class="section-segment" ng-href="http://www.omim.org/entry/{{omim}}" target="_blank">
                        <i class="ficon-globe pull-right"></i>

                        <span><b>OMIM</b></span>
                        <span ng-show="omim">{{omim}}</span>
                    </a>

                    <div ng-show="!omim" class="section-segment">
                        <span><b>OMIM</b></span>
                        <span class="muted">Not Recorded</span>
                    </div>
                </div>

                <div>
                    <div ng-show="moi" class="section-segment" target="_blank">
                        <span><b>Mode of Inheritance</b></span>
                        <span>{{ moi.name }}</span>
                    </div>

                    <span ng-show="!moi" class="section-segment">
                        <span><b>Mode of Inheritance</b></span>
                        <span  class="muted">Not Recorded</span>
                    </span>
                </div>
            </div>

        </div>
    </section>

    <!--<box>
        <box-state>
            <div>
                this is a test
            </div>
        </box-state>
        <box-state>
            this is my content!
        </box-state>
        <box-state>
            this is my content!
        </box-state>
        <box-state>
            this is my content!
        </box-state>
    </box>-->
    <!--<cm-genes title="Genes">
        <div class="editing">
            <div ng-repeat="gene in genes">
                <a class="section-segment section-segment-editing">
                    <span class="btn btn-remove"><i class="ficon-remove"></i></span>
                    {{ gene.title }}
                </a>
            </div>
        </div>
        <div class="displaying">
            <div ng-repeat="gene in genes">
                <a class="section-segment">
                    <i class="fficon-angle-right pull-right"></i>
                    {{ gene.title }}
                </a>
            </div>
        </div>
    </cm-genes>-->

    <section>
        <div class="section-segment section-segment-header" ng-class="{'section-segment-editing': model.genesState == 'isEditing' }">
            <div class="pull-right section-segment-header-buttons">
                <div ng-switch on="model.genesState">
                    <div ng-switch-when="isLoading">
                    </div>
                    <div ng-switch-when="isEditing">

                        <save-button click="saveGenes(model.edit.genes)"></save-button>

                        <button ng-click="cancelGenes()" class="btn btn-cancel">Cancel</button>

                        <div class="header-divider"></div>

                        <button ng-click="showAddGene()" class="btn btn-cancel"><i class="ficon-plus-sign"></i> Add</button>
                    </div>
                    <div ng-switch-when="isDisplaying">
                        <?php if ($isAdmin || $isCurator): ?>
                        <button ng-click="editGenes()" class="btn btn-edit">
                            <i class="ficon-pencil"></i> Edit
                        </button>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <h3>Genes</h3>
        </div>

        <cm-alert state="model.genesState" from="isLoading" to="isDisplaying">
            <i class="ficon-ok"></i> Genes Updated.
        </cm-alert>


        <div ng-switch on="model.genesState">
            <div ng-switch-when="isLoading">
                <div class="section-segment">
                    <div class="refreshing-box">
                        <i class="icon-refresh icon-refreshing"></i>
                    </div>
                </div>
            </div>
            <div ng-switch-when="isEditing">
                <remove-list list-model="model.edit.genes"></remove-list>
            </div>
            <div ng-switch-when="isDisplaying">
                <div class="section-segment muted" ng-show="!genes.length">
                    '{{ model.boneDysplasia.title }}' is associated with {{ genes.length }} genes.
                </div>

                <a ng-href="?q=node/{{gene.nid}}" class="section-segment" ng-repeat="gene in genes">
                    {{ gene.title }}
                    <i class="ficon-angle-right pull-right"></i>

                </a>
            </div>
        </div>





    </section>

    <section ng-show="model.boneDysplasia.field_bd_subbd.length">
        <div class="section-segment section-segment-header">
            <h3>Sub-types</h3>
        </div>
        <div ng-repeat="subType in model.boneDysplasia.field_bd_subbd">
            <a class="section-segment" href="?q=node/{{ subType.nid }}">
                {{ subType.title }}

                <i class="ficon-angle-right pull-right"></i>

            </a>
        </div>

    </section>

    <section ng-show="model.boneDysplasia.field_bd_sameas.length">
        <div class="section-segment section-segment-header">
            <h3>Same As</h3>
        </div>
        <div ng-repeat="subType in model.boneDysplasia.field_bd_sameas">
            <a class="section-segment" href="?q=node/{{ subType.nid }}">
                {{ subType.title }}

                <i class="ficon-angle-right pull-right"></i>

            </a>
        </div>
    </section>



    <section ng-show="model.boneDysplasia.field_bd_seealso.length">
        <div class="section-segment section-segment-header">
            <h3>See Also</h3>
        </div>
        <div ng-repeat="subType in model.boneDysplasia.field_bd_seealso">
            <a class="section-segment" href="?q=node/{{ subType.nid }}">
                <i class="ficon-angle-right pull-right"></i>


                {{ subType.title }}
            </a>
        </div>
    </section>


    <section>
        <div class="section-segment section-segment-header">
            <h2>Group Members</h2>
        </div>
        <div ng-repeat="groupBoneDysplasia in groupBoneDysplasias">
            <a class="section-segment" href="?q=node/{{ groupBoneDysplasia.nid }}">
                <i class="ficon-angle-right pull-right"></i>


                {{ groupBoneDysplasia.title }}
            </a>
        </div>
    </section>
    <!--<section ng-show="similar.length">
        <div class="section-segment section-segment-header">
            <h3>Similar</h3>
        </div>
        <div ng-repeat="object in similar">
            <a class="section-segment" href="{{object.url}}">
                <i class="ficon-angle-right pull-right"></i>


                {{ object.label }}
            </a>
        </div>
    </section>-->

    <!--<section>
        <div class="section-segment section-segment-header">
            <h3>Editors</h3>
        </div>

        <div ng-show="!editors.length" class="section-segment muted">
            No editors associated with this disorder.
        </div>

        <div ng-repeat="editor in editors">
            <a href="?q=profile-page/{{ editor.uid }}" class="section-segment" >
                <i class="ficon-angle-right pull-right"></i>
                

                <i class="icon-user"></i> {{ editor.name | capitalize }}
            </a>
        </div>

    </section>-->
</div>

<div cm-modal="showEditingPanel" ng-switch on="editingPanel" class="modal modal-dark fade hide" tabindex="-1"
     role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">


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
            <p>Search for a Gene to Add/Remove to '{{model.boneDysplasia.title}}'.</p>

            <!-- Search box -->
            <form>
                <search model="$parent.editGeneSearch"
                        change="searchForGenes($parent.editGeneSearch); newGeneName=editGeneSearch"
                        placeholder="Search for a Gene"></search>
            </form>


            <!-- /Search box -->
            <div style="margin-bottom: 20px">
                <div ng-show="editGeneLoading > 0">
                    <div class="refreshing-box">
                        <i class="icon-refresh icon-refreshing"></i>
                    </div>
                </div>

                <div ng-repeat="gene in editingGenes" style="overflow: auto; margin-bottom: 10px">
                    <strong>{{ gene.title }}</strong>
                    <a ng-show="!gene.added" ng-click="addGeneToBoneDysplasia(gene, model.boneDysplasia)"
                       class="btn btn-success pull-right" href><i class="icon-plus icon-white"></i> Add</a>
                    <a ng-show="gene.added" ng-click="removeGeneFromBoneDysplasia(gene, model.boneDysplasia)"
                       class="btn btn-danger pull-right" href><i class="icon-minus icon-white"></i> Remove</a>
                </div>

                <div ng-show="showAddNewGeneForm">
                    <p class="muted">Gene not found. Add the Gene to Skeletome.</p>

                    <div class="input-append">
                        <input placeholder="Enter a Gene Name" ng-model="newGeneName" type="text"/>
                        <a ng-click="addNewGeneToBoneDysplasia(newGeneName, model.boneDysplasia)" class="btn btn-success"><i
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
                            <a ng-click="gene.showGeneMutations = true" ng-show="!gene.showGeneMutations" class="btn btn-success pull-right" href><i class="icon-chevron-down icon-white"></i> Show</a>
                            <a ng-click="gene.showGeneMutations = false" ng-show="gene.showGeneMutations" class="btn btn-success pull-right" href><i class="icon-chevron-up icon-white"></i> Hide</a>
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



    <my-modal visible="model.isShowingAddGene">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
            <h3>Add Gene</h3>
        </div>
        <div class="section-segment">
            <lookup url="?q=ajax/search/gene/" placeholder="Search for a Gene" query="model.geneLookupQuery" results="model.geneLookupResults" is-loading="model.geneLookingIsLoading"></lookup>
        </div>
        <add-list add-to-model="model.edit.genes" list-model=" model.geneLookupResults"></add-list>

        <div class="modal-footer">
            <a ng-click="model.isShowingAddGene = false" class="btn btn-save" href=""> Done</a>
        </div>
    </my-modal>

</div>

<?php endif; ?>
