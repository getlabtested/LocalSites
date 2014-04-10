{% load mptt_tags pages_tags %}
<script type="text/javascript">
<!--
    jQuery('#id_{{ name }}').wymeditor({
        lang: '{{ language }}',
        skin: 'django',
        skinPath: "{{ PAGES_MEDIA_URL }}javascript/wymeditor/skins/django/",
        updateSelector: '.submit-row input[type=submit]',
        updateEvent: 'click',
        containersItems: [
            {'name': 'P', 'title': 'Paragraph', 'css': 'wym_containers_p'},
            {'name': 'H3', 'title': 'Heading_3', 'css': 'wym_containers_h3'},
            {'name': 'H4', 'title': 'Heading_4', 'css': 'wym_containers_h4'},
            {'name': 'H5', 'title': 'Heading_5', 'css': 'wym_containers_h5'}
        ],
        classesItems: [
            {'name': 'left', 'title': 'Image left align', 'expr': 'img'},
            {'name': 'right', 'title': 'Image right align', 'expr': 'img'},
            {'name': 'border', 'title': 'Image border', 'expr': 'img'},
            {'name': 'external', 'title': 'External link', 'expr': 'a'}
        ],
        
        editorStyles: [
            {'name': 'img', 'css': 'margin: 0 0 1.5em; max-width: 100%;'},
            {'name': 'p img', 'css': 'float: left; margin: 0.7em 0 0.8em 0; position: relative; width: 98%;'},
            {'name': 'p img.left', 'css': 'float: left; margin: 0.7em 1em 0.8em 0; width: auto;'},
            {'name': 'p img.right', 'css': 'float: right; margin: 0.7em 0 0.8em 1em; width: auto;'},
            
            {'name': 'img', 'css': 'border: none;'},
            {'name': 'img.border', 'css': 'border: 2px solid #757575;'}
        ],
        
        postInit: function(wym) {
            if ({{ page_link_wymeditor }}) {
                //construct the button's html
                html = "<li class='wym_tools_pagelink'>"
                     + "<a name='PageLink' href='#'"
                     + " title='Page link'"
                     + " style='background-image:"
                     + " url({{ PAGES_MEDIA_URL }}javascript/wymeditor/skins/django/icons.png);"
                     + " background-position: 0 -623px;'>"
                     + "Page link"
                     + "</a></li>";

                //add the button to the tools box
                jQuery(wym._box)
                .find(wym._options.toolsSelector + wym._options.toolsListSelector)
                .append(html);

                //handle click event on wrap button
                jQuery(wym._box)
                .find('li.wym_tools_pagelink a').click(function() {
                    var selected_page_id = 0;
                    // get Selection
                    var sel = wym.selected();
                    if (sel != null){
                        if (sel.tagName.toLowerCase() == 'a') {
                            // do we have a selection?
                            if (sel.className != null){
                                var page_id = sel.className.split('_'); 
                                var selected_page_id = page_id[1];
                            }
                        }
                    }
                    //construct the dialog's html
                    html = "<body class='wym_dialog wym_dialog_pagelink'"
                         + " onload='WYMeditor.INIT_DIALOG(" + WYMeditor.INDEX + ");jQuery(\".wym_select_pagelink option.page_" 
                         + selected_page_id + "\").attr(\"selected\",\"selected\").addClass(\"selected\");'"
                         + ">"
                         + "<form>"
                         + "<fieldset>"
                         + "<input type='hidden' class='wym_dialog_type' value='"
                         + "Pages"
                         + "' />"
                         + "<legend>Page link {{ lang }}</legend>"
                         + "<div class='row'>"
                         + "<label>Page</label>"
                         + "<select class='wym_select_pagelink'>"
                         {% for page in page_list %}
                         + "<option class='page_{{page.id}}' value='{% show_absolute_url page language %}#{{page.id}}'>{% show_slug_with_level page language %}</option>"
                         {% endfor %}
                         + "</select>"
                         + "</div>"
                         + "<div class='row row-indent'>"
                         + "<input class='wym_submit wym_submit_pagelink' type='button'"
                         + " value='{Submit}' />"
                         + "<input class='wym_cancel' type='button'"
                         + "value='{Cancel}' />"
                         + "</div>"
                         + "</fieldset>"
                         + "</form>"
                         + "</body>";

                    wym.dialog("Link Page", "menubar=no,titlebar=no,toolbar=no,resizable=no"
                               + ",width=560,height=200,top=0,left=0", html);
                
                    return(false);
                });
            }           
            wym.resizable({handles: "s", maxHeight: 600});


        },
        //handle click event on dialog's submit button
        postInitDialog: function( wym, wdw ) {
            if ({{ page_link_wymeditor }}) {
                var body = wdw.document.body;
                jQuery(body)
                    .find('input.wym_submit_pagelink')
                    .click(function() {
                        var values = jQuery(body).find('.wym_select_pagelink').val().split('#');
                        wym.wrap('<a class="page_' + values[1] + '" href="' + values[0] + '" title="">', '</a>');
                        wdw.close();
    
                    });
            }
            if ({{ filebrowser }}) {
                // Filebrowser callback
                wymeditor_filebrowser(wym, wdw);
            }
        }
    });
-->
</script>
