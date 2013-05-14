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

<div xmlns="http://www.w3.org/1999/html" xmlns="http://www.w3.org/1999/html" ng-controller="GeneCtrl" ng-init="init()">
    <div  ng-cloak>


        <div class="row-fluid">
            <div class="span12">

                <div class="page-heading" >
                    <div ng-show="boneDysplasia">
                        From <a href="?q=node/{{ boneDysplasia.nid }}">{{ boneDysplasia.title }}</a>
                    </div>

                    <h1>
                        <img class="type-logo" src="<?php echo base_path() . drupal_get_path('module', 'skeletome_builder'); ?>/images/gene_logo.svg"/>
                        <?php print $title; ?>
                    </h1>

                </div>
            </div>
        </div>

        <div class="row-fluid">

            <div class="span8">

                <?php include('description.php'); ?>

                <?php include('statements.php'); ?>

            </div>

            <div class="span4">
                <section>
                    <div class="section-segment section-segment-header">
                        <div class="section-segment-header-buttons pull-right">

                            <?php if((user_access('administer site configuration')) || is_array($user->roles) && in_array('sk_moderator', $user->roles)): ?>
                                <a href ng-click="showEditDetails()" data-toggle="modal" role="button" class="btn"><i class="icon-pencil"></i> Edit</a>
                            <?php endif; ?>
                        </div>
                        <h3>Details</h3>
                    </div>

                    <div ng-show="master.gene.field_gene_go.und[0].value" class="section-segment">
                        <b>Gene Ontology ID</b> {{ master.gene.field_gene_go.und[0].value }}
                    </div>

                    <div ng-show="master.gene.field_gene_locus.und[0].value" class="section-segment" >
                        <b>Locus</b> {{ master.gene.field_gene_locus.und[0].value }}
                    </div>

                    <div class="section-segment" ng-show="master.gene.field_gene_mesh.und[0].value ">
                        <b>MeSH Term</b>
                        {{ master.gene.field_gene_mesh.und[0].value }}
                    </div>

                    <a ng-href="http://www.omim.org/entry/{{master.gene.field_gene_omim.und[0].value}}" target="_blank" class="section-segment" ng-show="master.gene.field_gene_omim.und[0].value">
                        <i class="icon-globe pull-right"></i>
                        <i class="icon-globe icon-white pull-right"></i>

                        <b>OMIM</b>
                        {{ master.gene.field_gene_omim.und[0].value }}
                    </a>

                    <div class="section-segment" ng-show="master.gene.field_gene_umlscui.und[0].value">
                        <b>UMLS CUI</b>
                        {{ master.gene.field_gene_umlscui.und[0].value }}
                    </div>
                    <a target="_blank" ng-href="http://www.uniprot.org/uniprot/{{ master.gene.field_gene_uniprot.und[0].value }}" class="section-segment" ng-show="master.gene.field_gene_uniprot.und[0].value">
                        <i class="icon-globe pull-right"></i>
                        <i class="icon-globe icon-white pull-right"></i>

                        <b>Uniprot ID</b>
                        {{ master.gene.field_gene_uniprot.und[0].value }}
                    </a>
                    <div class="section-segment" ng-show="master.gene.field_gene_accession.und[0].value">
                        <b>Accesion Number</b>
                        {{ master.gene.field_gene_accession.und[0].value }}
                    </div>
                    <div class="section-segment" ng-show="master.gene.field_gene_entrezgene.und[0].value">
                        <b>Entrez Gene ID</b>
                        {{ master.gene.field_gene_entrezgene.und[0].value }}
                    </div>
                    <a target="_blank" ng-href="http://www.ncbi.nlm.nih.gov/nuccore/{{ master.gene.field_gene_refseq.und[0].value }}" class="section-segment" ng-show="master.gene.field_gene_refseq.und[0].value">
                        <i class="icon-globe pull-right"></i>
                        <i class="icon-globe icon-white pull-right"></i>

                        <b>RefSeq</b>
                        {{ master.gene.field_gene_refseq.und[0].value }}
                    </a>
                </section>

                <section>
                    <div class="section-segment section-segment-header">
                        <h3>Bone Dysplasias</h3>
                    </div>

                    <div ng-show="!boneDysplasias.length" class="section-segment muted">
                        No Bone dysplasias associated with this gene.
                    </div>

                    <div ng-repeat="boneDysplasia in boneDysplasias">
                        <a class="section-segment" href="?q=node/{{ boneDysplasia.nid }}">
                            <i class="icon-chevron-right pull-right"></i>
                            <i class="icon-chevron-right icon-white pull-right"></i>

                            {{ boneDysplasia.title }}
                        </a>
                    </div>

                </section>
            </div>
        </div>
    </div>

    <div id="new-bone-dysplasia" class="modal modal-dark fade hide" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
            <h3>Add New Gene Mutation</h3>
        </div>
        <div class="modal-body">
            <label>Gene Mutation</label>
            <input class="full-width" type="text" ng-model="newGeneMutationTitle" />
        </div>
        <div class="modal-footer modal-footer-bottom">

        </div>
    </div>

    <div cm-modal="view.editingPanel" ng-switch on="view.editingPanelType" class="modal modal-dark fade hide" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">

        <div class="modal-switch" ng-switch-when="add-new-gene-mutation">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                <h3>Create New Variant</h3>
            </div>

            <div class="modal-body">
                <div class="modal-body-inner">
                    <p>Create a new variant for '{{master.gene.title}}'.</p>
                    <div class="section-top">
                        <input placeholder="Variant Name" type="text" ng-model="edit.newGeneMutationTitle" class="full-width"/>
                    </div>
                </div>
            </div>

            <div class="modal-footer modal-footer-bottom">
                <button class="btn btn-success" ng-click="addGeneMutationToGene(edit.newGeneMutationTitle, master.gene)"><i class="icon-plus icon-white"></i> Create</button>
            </div>
        </div>

        <div class="modal-switch" ng-switch-when="edit-details">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                <h3>Edit Details</h3>
            </div>

            <div class="modal-body">
                <div class="modal-body-inner">
                    <p>Edit the details for '{{master.gene.title}}'.</p>
                    <div class="section-top">
                        <p>Locus</p>
                        <input type="text" ng-model="edit.locus" class="full-width"/>

                        <p>MeSH Term</p>
                        <input type="text" ng-model="edit.mesh" class="full-width"/>

                        <p>OMIM</p>
                        <input type="text" ng-model="edit.omim" class="full-width"/>

                        <p>UMLS CUI</p>
                        <input type="text" ng-model="edit.umls" class="full-width"/>

                        <p>Uniprot ID</p>
                        <input type="text" ng-model="edit.uniprot" class="full-width"/>

                        <p>Accession Number</p>
                        <input type="text" ng-model="edit.accession" class="full-width"/>

                        <p>Entrez Gene ID</p>
                        <input type="text" ng-model="edit.entrez" class="full-width"/>

                        <p>RefSeq</p>
                        <input type="text" ng-model="edit.refseq" class="full-width"/>
                    </div>
                </div>
            </div>

            <div class="modal-footer modal-footer-bottom">
                <a href class="btn btn-success" ng-click="saveDetails()"><i class="icon-ok icon-white"></i> Save</a>
            </div>
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
                    <p>Add a statement about '{{master.gene.title}}'.</p>
                    <textarea ck-editor ng-model="newStatement"></textarea>
                </div>

            </div>
            <!-- /Modal Body -->

            <!-- Modal Footer -->
            <div class="modal-footer modal-footer-bottom">
                <a href class="btn btn-success" ng-click="addStatement(newStatement, master.gene); newStatement = ''"><i class="icon-plus icon-white"></i> Add Statement</a>
            </div>
            <!-- /Modal Footer -->
        </div>


        <div class="modal-switch" ng-switch-when="edit-gene-mutation">
            <!-- Modal Header -->
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                <h3>Edit Description</h3>
            </div>
            <!-- /Modal Header -->

            <!-- Modal Body -->
            <div class="modal-body">
                <div class="modal-body-inner">
                    <p>Edit the Description for '{{ edit.geneMutation.title }}'.</p>
                    <textarea ck-editor ng-model="edit.geneMutationDescription" ></textarea>
                </div>

            </div>
            <!-- /Modal Body -->

            <!-- Modal Footer -->
            <div class="modal-footer modal-footer-bottom">
                <a href class="btn btn-success" ng-click="saveGeneMutationDescription(edit.geneMutationDescription, edit.geneMutation)"><i class="icon-ok icon-white"></i> Save Description</a>
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
                    <p>Edit the Description for '{{master.gene.title}}'.</p>
                    <textarea ck-editor ng-model="edit.description" ></textarea>
                </div>

            </div>
            <!-- /Modal Body -->

            <!-- Modal Footer -->
            <div class="modal-footer modal-footer-bottom">
                <a href class="btn btn-success" ng-click="saveEditedDescription(edit.description)"><i class="icon-ok icon-white"></i> Save Description</a>
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
                    <p>Search for a Feature to Add/Remove to '{{boneDysplasia.title}}'.</p>

                    <!-- Search box -->
                    <form>
                        <search model="$parent.editGeneSearch" change="searchForGenes($parent.editGeneSearch)" placeholder="Search for a Gene"></search>
                    </form>
                    <!-- /Search box -->

                    <table class="table table-center">
                        <!-- Table Header -->
                        <tr>
                            <th>Gene</th>
                            <th>Gene Mutation</th>
                            <th>Action</th>
                        </tr>
                        <!-- /Table Header -->
                        <tr ng-show="editGeneLoading > 0"><td colspan="3">Loading...</td></tr>

                        <!-- Existing Genes -->
                        <tr ng-repeat="geneMutation in editingGenes | filter:filterExistingGenes">
                            <td><strong>{{ geneMutation.gene.title | truncate:20 }}</strong></td>
                            <td><em>{{ geneMutation.gene_mutation.title | truncate:20 }}</em></td>
                            <td>
                                <a ng-show="geneMutation.added" role="button" class="btn btn-danger" ng-click="removeGeneMutation(geneMutation, boneDysplasia)"><i class="icon-remove icon-white"></i> Remove</a>
                                <a ng-show="!geneMutation.added" role="button" class="btn btn-success" ng-click="addGeneMutation(geneMutation, boneDysplasia)"><i class="icon-plus icon-white"></i> Add</a>
                            </td>
                        </tr>

                        <!-- /Existing Genes -->
                    </table>

                    <!-- Helpful Prompt (show when no text is entered, and no existing genes -->
                    <p class="muted info">Want to find another Gene? <br/>Try using the search bar above e.g. '<a href ng-click="$parent.editGeneSearch = 'FGFR3'; searchForGenes($parent.editGeneSearch)">FGFR3</a>'</p>
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
                        <search model="$parent.editClinicalFeatureSearch" change="searchForClinicalFeatures(editClinicalFeatureSearch)" placeholder="Search for a Clinical Feature"></search>
                    </form>


                    <table class="table table-center">
                        <tr>
                            <th>Clinical Feature</th>
                            <th>Action</th>
                        </tr>
                        <tr ng-repeat="clinicalFeature in editingClinicalFeatures | filter:editClinicalFeatureSearch">
                            <td>{{ clinicalFeature.name | truncate:40 }}</td>

                            <td>
                                <a role="button" ng-show="clinicalFeature.added" class="btn btn-danger pull-right" ng-click="removeClinicalFeature(clinicalFeature, boneDysplasia)"><i class="icon-remove icon-white"></i> Remove</a>
                                <a role="button" ng-show="!clinicalFeature.added" class="btn btn-success pull-right" ng-click="addClinicalFeature(clinicalFeature, boneDysplasia)"><i class="icon-plus icon-white"></i> Add</a>
                            </td>
                        </tr>
                    </table>

                    <!-- Helpful Prompt (show when no text is entered, and no existing genes -->
                    <p class="muted info">Want to find another Clinical Feature? <br/>Try using the search bar above e.g. '<a href ng-click="$parent.editClinicalFeatureSearch = 'Frontal Bossing'; searchForClinicalFeatures(editClinicalFeatureSearch)">Frontal Bossing</a>'</p>
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


</div>