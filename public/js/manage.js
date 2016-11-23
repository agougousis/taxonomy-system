var baseUrl = window.location.protocol + "//" + window.location.host + "/"; 
var mode = 'menu';
var selected_node = '';

// Reload the tree using ajax
function reloadTree(){

    $('#jsTree').tree('loadData', []);
    $.ajax({
        url: baseUrl+"tree_roots",
        type: 'GET',
        dataType: 'json',
        async: false,
        success: function( data ) {
            $('#jsTree').tree('loadData',data);
        },
        error: function(jqXHR, textStatus, errorThrown) {
            alert('Reloading tree failed!');
        }
    });
}

// Unfold all the ancestors of a node
function locateInTree(nameId){
    $.ajax({
        url: baseUrl+'api/names/'+nameId+'/ancestors',
        type: 'GET',
        dataType: 'json',
        success: function( data ) {
            loadNodes(data,0);
        },
        error: function(jqXHR, textStatus, errorThrown) {
            handleAjaxErrorWithResultDiv(jqXHR);
        }
    });

}

// Called by locateInTree()
function loadNodes(allIds,index){

    if(allIds.length > 0){ // If the node we locate is Kingdom, the ancestors array will be empty
        var nid = allIds[index];

        // Locate the node to open
        var node = $('#jsTree').tree('getNodeById', nid);

        // Retrieve node children
        $.ajax({
            url : baseUrl+"node_children?node="+nid,
            type: "GET",
            dataType : 'json',
            success:function(data, textStatus, jqXHR)
            {
                // Load the children to the node
                $('#jsTree').tree('loadData', data, node);
                // Open the node
                $('#jsTree').tree('openNode', node);
                //
                if((index+1)<allIds.length){
                    loadNodes(allIds,index+1);
                }
            },
            error: function(jqXHR, textStatus, errorThrown)
            {
                alert('failure!');
            }
        });
    }
}

// Reload the tree using ajax
function reloadTreeWithData(jsonData){
    $('#jsTree').tree('loadData', []);
    $('#jsTree').tree('loadData',jsonData);
}

function initializeTree(){
    $('#jsTree').tree({
        data: [],
        closedIcon: $('<span class="glyphicon glyphicon-chevron-right" style="color: #428bca"></span>'),
        openedIcon: $('<span class="glyphicon glyphicon-chevron-down" style="color: #428bca"></span>'),
        onCreateLi: function(node, $li) {
            // Append a link to the jqtree-element div.
            // The link has an url '#node-[id]' and a data property 'node-id'.
            if(node.leaves == 0){
                $li.find('.jqtree-title').before("<span style='margin-left: 1.5em; margin-right: 0.5em; vertical-align: middle'>"+node.rank+"</span> ");
                if(node.accepted == "0"){                   
                    $li.find("span").addClass('not-accepted-name');
                }
            } else {
                $li.find('.jqtree-title').before("<span style='margin-right: 0.5em; vertical-align: middle'>"+node.rank+"</span> ");                                                
            }
        },
        onLoadFailed: function(response) {
            handleAjaxErrorWithToastr(response);
        }
    });

    $('#jsTree').bind(
        'tree.select',
        function(event) {
            if (event.node) {
                // node was selected
                var node = event.node;
                selected_node = node.id;
                $('#edit_menu_button').show();
                $('#move_menu_button').show();
                if(node.leaves == 0){
                    $('#delete_leaf_menu_button').show();
                    $('#delete_branch_menu_button').hide();
                } else {
                    $('#delete_leaf_menu_button').hide();
                    $('#delete_branch_menu_button').show();
                }
            } else {
                // event.node is null
                // a node was deselected
                // e.previous_node contains the deselected node
                selected_node = '';
                $('#edit_menu_button').hide();
                $('#move_menu_button').hide();
                $('#delete_leaf_menu_button').hide();
                $('#delete_branch_menu_button').hide();
            }
        }
    );

};

$( document ).ready(function() {
    
    /* ----- Buttons functionality ----- */
    $('#build_menu_button').on('click',function(){
        mode = 'build';
        $('#action_menu').hide();
        $('#build_node_form').show();
    });

    $('#supplement_menu_button').on('click',function(){
        mode = 'build';
        $('#action_menu').hide();
        $('#supplement_node_form').show();
    });

    $('#add_menu_button').on('click',function(){
        mode = 'add';
        $('#action_menu').hide();
        $('#add_node_form').show();
    });

    $('#seeding_menu_button').on('click',function(){
        mode = 'seeding';
        $('#action_menu').hide();
        $('#seeding_form').show();
    });

    $('#move_menu_button').on('click',function(){
        mode = 'move';
        $('#action_menu').hide();
        $('#move_node_form').show();

        $.ajax({
            url: baseUrl+"api/names/"+selected_node,
            type: 'GET',
            dataType: 'json',
            success: function(node) {
                $('#move_node_form input[name="id"]').val(node.id);
                $('#move_node_form input[name="sname"]').val(node.sname);
                $('#move_node_form input[name="parent_id"]').val(node.parent_id);
                $('#move_node_form input[name="rank"]').val(node.rank);
                if(data.accepted == 1){
                    $('#move_node_form input[name="related_to_accepted"]').val(node.related_to_accepted);
                    $('#move_node_form input[name="accepted"]').prop('checked',true)
                }
            },
            error: function(jqXHR, textStatus, errorThrown) {
                handleAjaxErrorWithResultDiv(jqXHR);
            }
        });
    });

    $('#edit_menu_button').on('click',function(){
        mode = 'edit';
        $('#action_menu').hide();
        $('#edit_node_form').show();

        $.ajax({
            url: baseUrl+"api/names/"+selected_node,
            type: 'GET',
            dataType: 'json',
            success: function(node) {
                $('#edit_node_form input[name="id"]').val(node.id);
                $('#edit_node_form input[name="sname"]').val(node.sname);
                $('#edit_node_form input[name="parent_id"]').val(node.parent_id);
                $('#edit_node_form select[name="rank"]').val(node.rank);
                $('#edit_node_form input[name="authorship"]').val(node.authorship);
                $('#edit_node_form input[name="authonym"]').val(node.authonym);
                $('#edit_node_form input[name="uninomen"]').val(node.uninomen);
                $('#edit_node_form input[name="sortnophyl"]').val(node.sortnophyl);
                $('#edit_node_form input[name="basionym"]').val(node.basionym);
                $('#edit_node_form input[name="fk_aphia_basionym"]').val(node.fk_aphia_basionym);
                $('#edit_node_form input[name="protonym"]').val(node.protonym);
                $('#edit_node_form input[name="sortnospe"]').val(node.sortnospe);
                $('#edit_node_form input[name="nothonym"]').val(node.nothonym);
                $('#edit_node_form input[name="prefavatar"]').val(node.prefavatar);
                $('#edit_node_form input[name="fk_ref_morphonym"]').val(node.fk_ref_morphonym);
                $('#edit_node_form input[name="year"]').val(node.year);
                $('#edit_node_form input[name="fk_telangio_taxon"]').val(node.fk_telangio_taxon);
                $('#edit_node_form input[name="fk_getangio_taxon"]').val(node.fk_getangio_taxon);
                $('#edit_node_form input[name="grouptax"]').val(node.grouptax);
                $('#edit_node_form input[name="phylum"]').val(node.phylum);
                $('#edit_node_form input[name="remarks"]').val(node.remarks);
                $('#edit_node_form input[name="comnames"]').val(node.comnames);
                $('#edit_node_form input[name="comnames_languages"]').val(node.comnames_languages);
                $('#edit_node_form input[name="fk_ref_comnames"]').val(node.fk_ref_comnames);
                $('#edit_node_form input[name="taxonp"]').val(node.taxonp);
                $('#edit_node_form input[name="taxongp"]').val(node.taxongp);
                $('#edit_node_form input[name="fk_eunis_morphonym"]').val(node.fk_eunis_morphonym);
                $('#edit_node_form input[name="fk_aphia_morphonym"]').val(node.fk_aphia_morphonym);
                $('#edit_node_form input[name="fk_eunis_ergonym"]').val(node.fk_eunis_ergonym);
                $('#edit_node_form input[name="fk_aphia_parent"]').val(node.fk_aphia_parent);
                $('#edit_node_form input[name="checked_by"]').val(node.checked_by);
                $('#edit_node_form input[name="checked_date"]').val(node.checked_date);
                $('#edit_node_form input[name="validated_by"]').val(node.validated_by);
                $('#edit_node_form input[name="validated_date"]').val(node.validated_date);
                $('#edit_node_form input[name="workfield"]').val(node.workfield);
                $('#edit_node_form input[name="status_synonymy"]').val(node.status_synonymy);
                $('#edit_node_form input[name="status_onym"]').val(node.status_onym);
                $('#edit_node_form input[name="status_chresonym"]').val(node.status_chresonym);
                if(node.accepted == 1){
                    $('#edit_node_form input[name="related_to_accepted"]').prop('disabled','disabled');
                    $('#edit_node_form input[name="accepted"]').prop('checked',true)
                } else {
                    $('#edit_node_form input[name="related_to_accepted"]').val(node.related_to_accepted);
                    $('#edit_node_form input[name="related_to_accepted"]').prop('disabled','');
                    $('#edit_node_form input[name="accepted"]').prop('checked',false)
                }
            },
            error: function(jqXHR, textStatus, errorThrown) {
                handleAjaxErrorWithResultDiv(jqXHR);
            }
        });

    });

    $('#delete_leaf_menu_button').on('click',function(){
        $("#deleteLeafModal").modal('show');
    });

    $('#delete_leaf_confirm_button').on('click',function(){
        if(selected_node != ''){
            var csrf = $('#delete_leaf_form input[name="_token"]').val();
            $.ajax({
                url: baseUrl+"names/"+selected_node+"?_token="+csrf,
                type: 'DELETE',
                success: function( data ) {
                    selected_node = '';
                    $('#edit_menu_button').hide();
                    $('#move_menu_button').hide();
                    $('#delete_leaf_menu_button').hide();
                    $('#delete_branch_menu_button').hide();
                    toastr.success("Node deleted!");
                    reloadTree();
                },
                error: function(jqXHR, textStatus, errorThrown) {
                    handleAjaxErrorWithToastr(jqXHR);
                }
            });
        }
    });

    $('#delete_branch_menu_button').on('click',function(){
        $("#deleteBranchModal").modal('show');
    });

    $('#instructions_menu_button').on('click',function(){
        mode = 'instructions';
        $('#action_menu').hide();
        $('#instructions_form').show();
    });

    $('#delete_branch_confirm_button').on('click',function(){
        if(selected_node != ''){
            var csrf = $('#delete_branch_form input[name="_token"]').val();
            $.ajax({
                url: baseUrl+"names/"+selected_node+"?_token="+csrf,
                type: 'DELETE',
                success: function( data ) {
                    selected_node = '';
                    $('#edit_menu_button').hide();
                    $('#move_menu_button').hide();
                    $('#delete_leaf_menu_button').hide();
                    $('#delete_branch_menu_button').hide();
                    toastr.success("Branch deleted!");
                    reloadTree();
                },
                error: function(jqXHR, textStatus, errorThrown) {
                    handleAjaxErrorWithToastr(jqXHR);
                }
            });
        }
    });

    /* ----- Cancel Form, return to menu ----- */

    $('#cancel_add_action').on('click',function(){
        mode = 'menu';
        $('#add_node_form').hide();
        $('#action_menu').show();
    });

    $('#cancel_edit_action').on('click',function(){
        mode = 'menu';
        $('#edit_node_form').hide();
        $('#action_menu').show();
    });

    $('#cancel_build_action').on('click',function(){
        mode = 'menu';
        $('#build_node_form').hide();
        $('#action_menu').show();
    });

    $('#cancel_supplement_action').on('click',function(){
        mode = 'menu';
        $('#supplement_node_form').hide();
        $('#action_menu').show();
    });

    $('#cancel_move_action').on('click',function(){
        mode = 'menu';
        $('#move_node_form').hide();
        $('#action_menu').show();
    });

    $('#cancel_seeding_action').on('click',function(){
        mode = 'menu';
        $('#seeding_form').hide();
        $('#action_menu').show();
    });

    $('#cancel_instructions_action').on('click',function(){
        mode = 'menu';
        $('#instructions_form').hide();
        $('#action_menu').show();
    });

    /* ----- Select parent_id from tree ----- */

    $('#select_add_parent_button').on('click',function(){
        if(selected_node != ''){
            $('#add_node_form input[name="parent_id"]').val(selected_node);
        } else {
            alert('You need to select a name from the tree, first!');
        }
    });

    $('#select_new_parent_button').on('click',function(){
        if(selected_node != ''){
            $('#move_node_form input[name="new_parent_id"]').val(selected_node);
        } else {
            alert('You need to select a name from the tree, first!');
        }
    });

    $('#select_seeding_root_button').on('click',function(){
        if(selected_node != ''){
            $('#seeding_form input[name="seeding_root"]').val(selected_node);
        } else {
            alert('You need to select a name from the tree, first!');
        }
    });

    /* ----- Add node form submitted ----- */

    $('#add_node_button').on('click',function(event){
        var accepted = 0;
        var related_to_accepted = "";
        if($('#add_node_form input[name="accepted"]').is(':checked')){
            accepted = 1;            
        } else {
            accepted = 0;
            related_to_accepted = $('#add_node_form input[name="related_to_accepted"]').val();
        }

        $('#loading-image').center().show();
        var postData = {
                nodes: [{
                    id: $('#add_node_form input[name="id"]').val(),
                    parent_id: $('#add_node_form input[name="parent_id"]').val(),
                    sname: $('#add_node_form input[name="sname"]').val(),
                    rank: $('#add_node_form select[name="rank"]').val(),
                    authorship: $('#add_node_form input[name="authorship"]').val(),
                    accepted: accepted,
                    related_to_accepted: related_to_accepted,
                    authonym: $('#add_node_form input[name="authonym"]').val(),
                    uninomen: $('#add_node_form input[name="uninomen"]').val(),
                    sortnophyl: $('#add_node_form input[name="sortnophyl"]').val(),
                    basionym: $('#add_node_form input[name="basionym"]').val(),
                    fk_aphia_basionym: $('#add_node_form input[name="fk_aphia_basionym"]').val(),
                    protonym: $('#add_node_form input[name="protonym"]').val(),
                    sortnospe: $('#add_node_form input[name="sortnospe"]').val(),
                    nothonym: $('#add_node_form input[name="nothonym"]').val(),
                    prefavatar: $('#add_node_form input[name="prefavatar"]').val(),
                    fk_ref_morphonym: $('#add_node_form input[name="fk_ref_morphonym"]').val(),
                    year: $('#add_node_form input[name="year"]').val(),
                    fk_telangio_taxon: $('#add_node_form input[name="fk_telangio_taxon"]').val(),
                    fk_getangio_taxon: $('#add_node_form input[name="fk_getangio_taxon"]').val(),
                    grouptax: $('#add_node_form input[name="grouptax"]').val(),
                    phylum: $('#add_node_form input[name="phylum"]').val(),
                    remarks: $('#add_node_form textarea[name="remarks"]').val(),
                    comnames: $('#add_node_form input[name="comnames"]').val(),
                    comnames_languages: $('#add_node_form input[name="comnames_languages"]').val(),
                    fk_ref_comnames: $('#add_node_form input[name="fk_ref_comnames"]').val(),
                    taxonp: $('#add_node_form input[name="taxonp"]').val(),
                    taxongp: $('#add_node_form input[name="taxongp"]').val(),
                    fk_eunis_morphonym: $('#add_node_form input[name="fk_eunis_morphonym"]').val(),
                    fk_aphia_morphonym: $('#add_node_form input[name="fk_aphia_morphonym"]').val(),
                    fk_aphia_ergonym: $('#add_node_form input[name="fk_eunis_ergonym"]').val(),
                    fk_aphia_parent: $('#add_node_form input[name="fk_aphia_parent"]').val(),
                    checked_by: $('#add_node_form input[name="checked_by"]').val(),
                    checked_date: $('#add_node_form input[name="checked_date"]').val(),
                    validated_by: $('#add_node_form input[name="validated_by"]').val(),
                    validated_date: $('#add_node_form input[name="validated_date"]').val(),
                    workfield: $('#add_node_form input[name="workfield"]').val(),
                    status_synonymy: $('#add_node_form input[name="status_synonymy"]').val(),
                    status_onym: $('#add_node_form input[name="status_onym"]').val(),
                    status_chresonym: $('#add_node_form input[name="status_chresonym"]').val(),
                }],
            _token: $('#add_node_form input[name="_token"]').val()
        };

        $.ajax({
            url: baseUrl+"names",
            type: 'POST',
            dataType: 'json',
            data: postData,
            success: function( data ) {
                $('#loading-image').hide();
                var new_id = $('#add_node_form input[name="id"]').val();
                $('#add_node_form').hide();
                $('#add_node_form')[0].reset();
                $('#action_menu').show();
                reloadTree();
                locateInTree(new_id);
                toastr.success(data.message);
            },
            error: function(jqXHR, textStatus, errorThrown) {
                handleAjaxErrorWithToastr(jqXHR);
            }
        });
    });

    /* ----- Edit node form submitted ----- */

    $('#edit_node_button').on('click',function(event){
        var node_id = $('#edit_node_form input[name="id"]').val();

        var accepted = 0;
        var related_to_accepted = "";
        if($('#edit_node_form input[name="accepted"]').is(':checked')){
            accepted = 1;
            related_to_accepted = $('#edit_node_form input[name="related_to_accepted"]').val();
        } else {
            accepted = 0;
        }

        $('#loading-image').center().show();
        var postData = {
                nodes: [{
                    id: node_id,
                    parent_id: $('#edit_node_form input[name="parent_id"]').val(),
                    sname: $('#edit_node_form input[name="sname"]').val(),
                    rank: $('#edit_node_form select[name="rank"]').val(),
                    authorship: $('#edit_node_form input[name="authorship"]').val(),
                    accepted: accepted,
                    related_to_accepted: related_to_accepted,
                    authonym: $('#edit_node_form input[name="authonym"]').val(),
                    uninomen: $('#edit_node_form input[name="uninomen"]').val(),
                    sortnophyl: $('#edit_node_form input[name="sortnophyl"]').val(),
                    basionym: $('#edit_node_form input[name="basionym"]').val(),
                    fk_aphia_basionym: $('#edit_node_form input[name="fk_aphia_basionym"]').val(),
                    protonym: $('#edit_node_form input[name="protonym"]').val(),
                    sortnospe: $('#edit_node_form input[name="sortnospe"]').val(),
                    nothonym: $('#edit_node_form input[name="nothonym"]').val(),
                    prefavatar: $('#edit_node_form input[name="prefavatar"]').val(),
                    fk_ref_morphonym: $('#edit_node_form input[name="fk_ref_morphonym"]').val(),
                    year: $('#edit_node_form input[name="year"]').val(),
                    fk_telangio_taxon: $('#edit_node_form input[name="fk_telangio_taxon"]').val(),
                    fk_getangio_taxon: $('#edit_node_form input[name="fk_getangio_taxon"]').val(),
                    grouptax: $('#edit_node_form input[name="grouptax"]').val(),
                    phylum: $('#edit_node_form input[name="phylum"]').val(),
                    remarks: $('#edit_node_form textarea[name="remarks"]').val(),
                    comnames: $('#edit_node_form input[name="comnames"]').val(),
                    comnames_languages: $('#edit_node_form input[name="comnames_languages"]').val(),
                    fk_ref_comnames: $('#edit_node_form input[name="fk_ref_comnames"]').val(),
                    taxonp: $('#edit_node_form input[name="taxonp"]').val(),
                    taxongp: $('#edit_node_form input[name="taxongp"]').val(),
                    fk_eunis_morphonym: $('#edit_node_form input[name="fk_eunis_morphonym"]').val(),
                    fk_aphia_morphonym: $('#edit_node_form input[name="fk_aphia_morphonym"]').val(),
                    fk_aphia_ergonym: $('#edit_node_form input[name="fk_eunis_ergonym"]').val(),
                    fk_aphia_parent: $('#edit_node_form input[name="fk_aphia_parent"]').val(),
                    checked_by: $('#edit_node_form input[name="checked_by"]').val(),
                    checked_date: $('#edit_node_form input[name="checked_date"]').val(),
                    validated_by: $('#edit_node_form input[name="validated_by"]').val(),
                    validated_date: $('#edit_node_form input[name="validated_date"]').val(),
                    workfield: $('#edit_node_form input[name="workfield"]').val(),
                    status_synonymy: $('#edit_node_form input[name="status_synonymy"]').val(),
                    status_onym: $('#edit_node_form input[name="status_onym"]').val(),
                    status_chresonym: $('#edit_node_form input[name="status_chresonym"]').val(),
                }],
            _token: $('#edit_node_form input[name="_token"]').val()
        };
        $.ajax({
            url: baseUrl+"names",
            type: 'PUT',
            dataType: 'json',
            data: postData,
            success: function( data ) {
                $('#loading-image').hide();
                var edited_id = $('#edit_node_form input[name="id"]').val();

                // Reset and hide the form
                $('#edit_node_form').hide();
                $('#edit_node_form')[0].reset();

                // Hide menu buttons that are related to the selected node (the node will be unselected)
                $('#edit_menu_button').hide();
                $('#move_menu_button').hide();
                $('#delete_leaf_menu_button').hide();
                $('#delete_branch_menu_button').hide();

                // Display the menu
                $('#action_menu').show();

                // Refresh the tree and locate the selected node
                reloadTree();
                locateInTree(edited_id);

                // Display a success message
                toastr.success(data.message);
            },
            error: function(jqXHR, textStatus, errorThrown) {
                handleAjaxErrorWithToastr(jqXHR);
            }
        });
    });

    /* ----- Move node form submitted ----- */

    $('#move_node_button').on('click',function(event){
        var node_id = $('#move_node_form input[name="id"]').val();

        var postData = {
            nodes: [{
                id: node_id,
                rank: $('#move_node_form input[name="rank"]').val(),
                parent_id: $('#move_node_form input[name="parent_id"]').val(),
                new_parent_id: $('#move_node_form input[name="new_parent_id"]').val()
            }],
            _token: $('#move_node_form input[name="_token"]').val()
        };

        $('#loading-image').center().show();
        $.ajax({
            url: baseUrl+"names/move",
            type: 'PUT',
            dataType: 'json',
            data: postData,
            success: function( data ) {
                $('#loading-image').hide();
                $('#move_node_form').hide();
                $('#move_node_form')[0].reset();
                $('#action_menu').show();
                reloadTree();
                locateInTree(node_id);
                toastr.success(data.message);
            },
            error: function(jqXHR, textStatus, errorThrown) {
                handleAjaxErrorWithToastr(jqXHR);
            }
        });
    });

    /* ----- Clear cache button clicked ----- */

    $('#clear_cache_button').on('click',function(event){
        var postData = {
            _token: $('#move_node_form input[name="_token"]').val()
        };

        $('#loading-image').center().show();
        $.ajax({
            url: baseUrl+"clear_cache",
            type: 'POST',
            dataType: 'json',
            data: postData,
            success: function( data ) {
                $('#loading-image').hide();
                reloadTree();
                toastr.success(data.message);
            },
            error: function(jqXHR, textStatus, errorThrown) {
                handleAjaxErrorWithToastr(jqXHR);                          
            }
        });
    });    

    /* ----- Seeding form submitted ----- */

    $('#seeding_node_button').on('click',function(event){

        $('#loading-image').center().show();
        
        var how_many = $('#seeding_form input[name="how_many"]').val();
        var root_node = $('#seeding_form input[name="seeding_root"]').val();

        var postData = {
            how_many_seeds: how_many,
            _token: $('#seeding_form input[name="_token"]').val()
        };

        $.ajax({
            url: baseUrl+'names/'+root_node+'/seeding',
            type: 'POST',
            dataType: 'json',
            data: postData,
            async: false,
            success: function( data ) {
                $('#loading-image').hide();
                $('#seeding_form').hide();
                $('#seeding_form')[0].reset();
                $('#action_menu').show();
                reloadTree();
                locateInTree(root_node);
                toastr.success('Seeding completed!');
            },
            error: function(jqXHR, textStatus, errorThrown) {
                handleAjaxErrorWithToastr(jqXHR);
            }
        });
    });


    $('[data-toggle="popover"]').popover();
});
