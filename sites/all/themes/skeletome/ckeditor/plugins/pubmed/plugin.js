CKEDITOR.plugins.add( 'pubmed',
    {
        requires: ['iframedialog'],
        init: function( editor )
        {

            (function(theEditor, path) {

                /* Define the dialog here, so we have access to the path */
//            CKEDITOR.dialog.addIframe('pubmedDialog', 'pubmedDialog', me.path + 'dialog.html', 400, 200);

                console.log("IS THIS RUNNING");
                CKEDITOR.dialog.add( 'pubmedDialog' + theEditor.name, function ()
                {
                    return {
                        title : 'Add PubMed Reference',
                        contents :
                            [
                                {
                                    id : 'iframe',
                                    label : 'Lien',
                                    expand : true,
                                    elements :
                                        [
                                            {
                                                type : 'iframe',
                                                src : path + 'dialog.html?nocache=' + Math.floor(Math.random()*110),
                                                width : '100%',
                                                height : '100%'
                                            }
                                        ]
                                }
                            ],
                        buttons: {
                            disabled:true
                        },
                        onCancel: function() {
                        }, onOk : function(pubmedId) {
                            theEditor.insertText('[bib]' + pubmedId +'[/bib]');
                            theEditor.fire('save');
                        },
                        onHide: function() {
                        }
                    };
                });



                editor.addCommand( 'pubmedDialog' + theEditor.name, new CKEDITOR.dialogCommand( 'pubmedDialog' + theEditor.name ) );

                editor.ui.addButton( 'Pubmed',
                    {
                        label: 'Add PubMed Reference',
                        command: 'pubmedDialog' + theEditor.name,
                        icon: path +  'images/cite.png' //parent.Drupal.settings.skeletome_builder.base_url + '/sites/all/themes/skeletome/ckeditor/plugins/pubmed/images/cite.png'
                    }
                );
            })(editor, this.path);
        }
    }
);


