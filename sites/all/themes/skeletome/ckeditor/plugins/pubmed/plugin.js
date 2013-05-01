CKEDITOR.plugins.add( 'pubmed',
    {
        requires: ['iframedialog'],
        init: function( editor )
        {
            var me = this;

            (function(theEditor) {

                /* Define the dialog here, so we have access to the path */
//            CKEDITOR.dialog.addIframe('pubmedDialog', 'pubmedDialog', me.path + 'dialog.html', 400, 200);

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
                                                src : me.path + 'dialog.html?nocache=' + Math.floor(Math.random()*110),
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
                        icon: this.path + 'images/cite.png'
                    }
                );
            })(editor);



        }
    }
);


