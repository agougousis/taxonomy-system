var baseUrl = window.location.protocol + "//" + window.location.host + "/"; 

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
            handleAjaxErrorWithToastr(jqXHR);
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

function loadResultsPage(searchType,searchValue,pageNum){

    // If we are looking for a page other than the first one, we should say it
    var pageInUrl = (pageNum > 1 ? "&page="+pageNum : "");

    $.ajax({
        url: baseUrl+"api/names"+"?"+searchType+"="+searchValue+pageInUrl,
        type: 'GET',
        dataType: 'json',
        success: function( result ) {
            var data = result.data;
            var hasMore = result.hasMore;
            var resultTable = $('#search_results_table tbody');
            resultTable.empty();
            for(var i = 0; i < data.length; i++) {
               resultTable.append("<tr><td><span class='linkStyle' onclick='displayNameInfo("+data[i].id+")'>"+data[i].sname+"</span></td><td>"+data[i].rank+"</td><td>"+data[i].authorship+"</td><td><span class='glyphicon glyphicon-zoom-in glyphbutton' title='Locate in tree' onclick='locateInTree("+data[i].id+")'></span></td></tr>");
            }

            //
            if((pageNum>1)||(hasMore == '1')){
                // Initialize pagination elements
                var prevPagination = "";
                var nextPagination = "";

                pagePagination = "Page "+pageNum;
                if(pageNum > 1){
                    prevPagination = "<div class='linkStyle' title='previous page' onclick='loadResultsPage(\""+searchType+"\",\""+searchValue+"\","+(pageNum-1)+")'>&lt; </div>";
                }
                if(hasMore == '1'){
                    nextPagination = "<div class='linkStyle' title='next page' onclick='loadResultsPage(\""+searchType+"\",\""+searchValue+"\","+(pageNum+1)+")'> &gt;</div>";
                }

                resultTable.append("<tr><td>"+prevPagination+pagePagination+nextPagination+"</td></tr>");
            }

        },
        error: function(jqXHR, textStatus, errorThrown) {
            handleAjaxErrorWithToastr(jqXHR);
        }
    });
}

function displayNameInfo(nameId){
    // default value for functional parameters is not supported before ES6/ES2015
    nameId = typeof nameId !== 'undefined' ? nameId : '';   

    var scientific_name = '';
    toggle_to_info();

    if(nameId == ''){
        if(selected_node == ''){
            return;
        } else {
            nameId = selected_node;
        }
    }

    $.ajax({
        url: baseUrl+"api/names/"+nameId,
        type: 'GET',
        dataType: 'json',
        async: false,
        success: function( node ) {
            scientific_name = node.sname; // we will need it for the next ajax request of this function
            $('#info_panel table tr:nth-child(1) td:nth-child(2)').html(node.sname);
            $('#info_panel table tr:nth-child(2) td:nth-child(2)').html(node.rank);
            $('#info_panel table tr:nth-child(3) td:nth-child(2)').html(node.uninomen);
            $('#info_panel table tr:nth-child(4) td:nth-child(2)').html(node.accepted);
            $('#info_panel table tr:nth-child(5) td:nth-child(2)').html(node.related_to_accepted);
            $('#info_panel table tr:nth-child(6) td:nth-child(2)').html(node.sortnophyl);
            $('#info_panel table tr:nth-child(7) td:nth-child(2)').html(node.basionym);
            $('#info_panel table tr:nth-child(8) td:nth-child(2)').html(node.fk_aphia_basionym);
            $('#info_panel table tr:nth-child(9) td:nth-child(2)').html(node.protonym);
            $('#info_panel table tr:nth-child(10) td:nth-child(2)').html(node.sortnospe);
            $('#info_panel table tr:nth-child(11) td:nth-child(2)').html(node.authorship);
            $('#info_panel table tr:nth-child(12) td:nth-child(2)').html(node.authonym);
            $('#info_panel table tr:nth-child(13) td:nth-child(2)').html(node.nothonym);
            $('#info_panel table tr:nth-child(14) td:nth-child(2)').html(node.prefavatar);
            $('#info_panel table tr:nth-child(15) td:nth-child(2)').html(node.fk_ref_morphonym);
            $('#info_panel table tr:nth-child(16) td:nth-child(2)').html(node.year);
            $('#info_panel table tr:nth-child(17) td:nth-child(2)').html(node.fk_telangio_taxon);
            $('#info_panel table tr:nth-child(18) td:nth-child(2)').html(node.fk_getangio_taxon);
            $('#info_panel table tr:nth-child(19) td:nth-child(2)').html(node.grouptax);
            $('#info_panel table tr:nth-child(20) td:nth-child(2)').html(node.phylum);
            $('#info_panel table tr:nth-child(21) td:nth-child(2)').html(node.remarks);
            $('#info_panel table tr:nth-child(22) td:nth-child(2)').html(node.comnames);
            $('#info_panel table tr:nth-child(23) td:nth-child(2)').html(node.comnames_languages);
            $('#info_panel table tr:nth-child(24) td:nth-child(2)').html(node.fk_ref_comnames);
            $('#info_panel table tr:nth-child(25) td:nth-child(2)').html(node.taxonp);
            $('#info_panel table tr:nth-child(26) td:nth-child(2)').html(node.taxongp);
            $('#info_panel table tr:nth-child(27) td:nth-child(2)').html(node.fk_eunis_morphonym);
            $('#info_panel table tr:nth-child(28) td:nth-child(2)').html(node.fk_aphia_morphonym);
            $('#info_panel table tr:nth-child(29) td:nth-child(2)').html(node.fk_eunis_ergonym);
            $('#info_panel table tr:nth-child(30) td:nth-child(2)').html(node.fk_aphia_parent);
            $('#info_panel table tr:nth-child(31) td:nth-child(2)').html(node.checked_by);
            $('#info_panel table tr:nth-child(32) td:nth-child(2)').html(node.checked_date);
            $('#info_panel table tr:nth-child(33) td:nth-child(2)').html(node.validated_by);
            $('#info_panel table tr:nth-child(34) td:nth-child(2)').html(node.validated_date);
            $('#info_panel table tr:nth-child(35) td:nth-child(2)').html(node.workfield);
            $('#info_panel table tr:nth-child(36) td:nth-child(2)').html(node.status_synonymy);
            $('#info_panel table tr:nth-child(37) td:nth-child(2)').html(node.status_onym);
            $('#info_panel table tr:nth-child(38) td:nth-child(2)').html(node.status_chresonym);
        },
        error: function(jqXHR, textStatus, errorThrown) {
            handleAjaxErrorWithToastr(jqXHR);
        }
    });

    $.ajax({
        url: baseUrl+"api/names/"+nameId+"/synonyms",
        type: 'GET',
        dataType: 'json',
        async: false,
        success: function( response ) {
            var synonyms = response.data;
            if(synonyms.length > 0){
                $('#synonymsShowIcon').show();
                var synonymTable = $('#synonyms_list table tbody');
                synonymTable.empty();
                for(var j=0; j<synonyms.length; j++){
                    synonymTable.append('<tr><td style="text-align: left">'+synonyms[j].sname+'</td><td>'+synonyms[j].rank+'</td></tr>');
                }
                $('#synonym_of_span').empty().append(scientific_name);
            } else {
                $('#synonyms_list').hide();
                $('#synonymsShowIcon').hide();
            }
        },
        error: function(jqXHR, textStatus, errorThrown) {
            alert('failed');
        }
    });

}

$('#synonymsShowIcon').on('click',function(){
    $('#synonyms_list').show();
});

$('.close_alert_x').on('click',function(){
    $(this).closest('.alert').hide();
});

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
                var toggle_mode = $('#mode-toggle').find('.btn-primary').first().attr('data-mode');
                if(toggle_mode == 'search'){
                    toggle_it();
                } else {
                    // Do nothing?
                }
                displayNameInfo();
            } else {
                // event.node is null
                // a node was deselected
                // e.previous_node contains the deselected node
                selected_node = '';
            }
        }
    );

};

$( document ).ready(function() {
    $('#search_button').on('click',function(event){
        event.preventDefault();
        $('#loading-image').center().show();

        var searchValue = $('#search_form input[name="search"]').val();
        var searchType = $('#search_form input[name="search_by"]').val();
        loadResultsPage(searchType,searchValue,1);

        $('#loading-image').hide();
    }); 
});