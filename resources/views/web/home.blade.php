@extends('layouts.app')

@section('content')
<div class='row'>
    <div class='col-md-6'>

        <div class='well'>

            <div class="alert alert-warning" role="alert">
                <img src="{{ asset('images/tree.png') }}" class="action-title-image">
                <strong>Greek Taxonomic Information Tree</strong>
            </div>

            <div style="padding: 10px; margin-left: 10px; margin-top: 30px">
                <div id="jsTree" data-url="/node_children"></div>
            </div>

            <div style="margin-top: 20px; color: gray">* number in parenthesis represents the number of leaf nodes under each node</div>
        </div>

    </div>
    <div class='col-md-6'>

        <div style="text-align: right">
            @if($is_admin)
                <a href="{{ url('manage') }}" style="float: left"><img style="height: 40px" title="Manage GTIS tree" src="{{ asset('images/manage.png') }}"></a>
            @endif
            <a href="{{ url('api_doc') }}" style="float: left; margin-left: 10px"><img style="height: 40px" title="API documentation" src="{{ asset('images/api_doc.png') }}"></a>
            <div class="btn-group btn-toggle" id="mode-toggle">
                <button class="btn btn-sm btn-primary active" data-mode="search">Search</button>
                <button class="btn btn-sm btn-default" data-mode="info">Info</button>
            </div>
            <div style="clear: both"></div>
        </div>

        <script type="text/javascript">

            function toggle_it(){
                var tbutton = $('#mode-toggle');

                // Toggle the "active" class to all buttons
                tbutton.find('.btn').toggleClass('active');

                // Toggle the "btn-primary" class to all buttons
                if (tbutton.find('.btn-primary').size()>0) {
                    tbutton.find('.btn').toggleClass('btn-primary');
                }

                // Toggle the "btn-default" class to all buttons
                tbutton.find('.btn').toggleClass('btn-default');

                // Display the right panel
                var toggle_mode = tbutton.find('.btn-primary').first().attr('data-mode');

                switch(toggle_mode){
                    case 'search':
                        $('#info_panel').hide();
                        $('#search_panel').show();
                        break;
                    case 'info':
                        $('#search_panel').hide();
                        $('#info_panel').show();
                        break;
                }
            }

            function toggle_to_info(){
                var toggle_mode = $('#mode-toggle').find('.btn-primary').first().attr('data-mode');
                if(toggle_mode == 'search'){
                    toggle_it();
                }
            }

            $('#mode-toggle').click(function() {
                toggle_it();
            });
        </script>

        <style type="text/css">

            #search_panel, #info_panel {
                margin-top: 10px;
                padding: 15px;
                background-color: lightyellow;
                border: 1px solid gray;
            }

            #search_panel table tr td, #info_panel table tr td {
                text-align: left;
            }

            .table tr td, .table tr th {
                /** border: 1px solid green; */
            }

        </style>

        <div id="synonyms_list" style="display: none; margin-top: 10px" class="alert alert-warning alert-dismissible">
            <div style="font-weight: bold; margin-bottom: 10px">
                Synonyms of <span id="synonym_of_span" style="color: red">?</span>
                <button type="button" title="Close" class="close close_alert_x" style="right: 0px"><span>&times;</span></button>
            </div>
            <table class="table table-condensed table-bordered" style="background-color: white">
                <tbody>

                </tbody>
            </table>
        </div>

        <div id="search_panel">

            {!! Form::open(array('class'=>'form-horizontal','id'=>'search_form')) !!}

            <table style="margin-bottom: 5px">
                <tr>
                    <td style='font-weight: bold'>Search by:</td>
                    <td style="padding-left: 15px">{{ Form::radio('search_by', 'sname',true) }} Scientific Name</td>
                    <td style="padding-left: 15px">{{ Form::radio('search_by', 'authorship') }} Authorship</td>
                </tr>
            </table>

            <div class="form-group">
                <div class="col-sm-8">
                  {{ Form::text('search','',array('class'=>'form-control')) }}
                </div>
                <button type='submit' class='btn btn-default' id='search_button'><span class="glyphicon glyphicon-zoom-in"></span></button>
            </div>

            {!! Form::close() !!}

            <div style="margin-top: 30px">
                <table class="table table-condensed" id='search_results_table'>
                    <thead>
                        <th>Name</th>
                        <th>Rank</th>
                        <th>Authorship</th>
                        <th></th>
                    </thead>
                    <tbody>
                        <tr><td colspan="5">No results...</td></tr>
                    </tbody>
                </table>
            </div>
        </div>

        <div id="info_panel" style="display: none">

            <div class="alert alert-info" style="margin-bottom: 0px" role="alert">
                <span class="glyphicon glyphicon-list-alt" aria-hidden="true"></span>
                <strong> Name Information</strong>
                <div id="synonymsShowIcon" class="hover-pointer" style="display: none; float: right" title="Show synonyms"><img src="{{ asset('images/synonym.png') }}" style="height: 25px"></div>
            </div>

            <table class="table table-condensed table-hover">
                <tr><td style="width: 50%">Name:</td><td style="width: 50%"></td></tr>
                <tr><td>Rank:</td><td></td></tr>
                <tr><td>Uninomen:</td><td></td></tr>
                <tr><td>Accepted:</td><td></td></tr>
                <tr><td>Related to:</td><td></td></tr>
                <tr><td>Sortnophyl:</td><td></td></tr>
                <tr><td>Basionym:</td><td></td></tr>
                <tr><td>FK aphia basionym:</td><td></td></tr>
                <tr><td>Protonym:</td><td></td></tr>
                <tr><td>Sortnospe:</td><td></td></tr>
                <tr><td>Authorship:</td><td></td></tr>
                <tr><td>Authonym:</td><td></td></tr>
                <tr><td>Nothonym:</td><td></td></tr>
                <tr><td>Prefavatar:</td><td></td></tr>
                <tr><td>FK ref morphonym:</td><td></td></tr>
                <tr><td>Year:</td><td></td></tr>
                <tr><td>FK telangio taxon:</td><td></td></tr>
                <tr><td>FK getangio taxon:</td><td></td></tr>
                <tr><td>Grouptax:</td><td></td></tr>
                <tr><td>Phylum:</td><td></td></tr>
                <tr><td>Remarks:</td><td></td></tr>
                <tr><td>Comnames:</td><td></td></tr>
                <tr><td>Comnames languages:</td><td></td></tr>
                <tr><td>FK ref comnames:</td><td></td></tr>
                <tr><td>taxonp:</td><td></td></tr>
                <tr><td>taxongp:</td><td></td></tr>
                <tr><td>FK eunis morphonym:</td><td></td></tr>
                <tr><td>FK aphia morphonym:</td><td></td></tr>
                <tr><td>FK eunis ergonym:</td><td></td></tr>
                <tr><td>FK aphia parent:</td><td></td></tr>
                <tr><td>checked_by:</td><td></td></tr>
                <tr><td>checked_date:</td><td></td></tr>
                <tr><td>validated_by:</td><td></td></tr>
                <tr><td>validated_date:</td><td></td></tr>
                <tr><td>Workfield:</td><td></td></tr>
                <tr><td>Status synonymy:</td><td></td></tr>
                <tr><td>Status onym:</td><td></td></tr>
                <tr><td>Status chresonym:</td><td></td></tr>
                </tr>
            </table>
        </div>

    </div>
</div>

<script type="text/javascript" src="{{ asset('js/home.js') }}"></script>
<script type="text/javascript">
    initializeTree();
    reloadTree();
</script>

@endsection