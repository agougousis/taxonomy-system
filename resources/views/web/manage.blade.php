@extends('layouts.app')

@section('content')

<div class="row">

    <div class="col-md-6">
        <div class='well'>

            <div class="alert alert-warning" role="alert">
                <img src="{{ asset('images/tree.png') }}" class="action-title-image">
                <strong>Greek Taxonomic Information Tree</strong>
            </div>

            <div id="jsTree" data-url="/all_node_children"></div>
        </div>

        <script type="text/javascript" src="{{ asset('js/manage.js') }}"></script>
        <script type="text/javascript">
            initializeTree();
            reloadTreeWithData(<?=$jsonTree?>);
        </script>
    </div>

    <div class="col-md-6">
        <div class='well'>

            <div class="alert alert-warning" role="alert">
                <img src="{{ asset('images/manage.png') }}" class="action-title-image">
                <strong>Administration Area</strong>
            </div>

                <div id='action_menu'>
                    <div id='build_menu_button' class='action_button' title="Delete all names and build a new taxonomic tree from CSV">
                        <img src="{{ asset('images/csv.png') }}">
                        <div class="action_button_text">Build tree from CSV</div>
                    </div>
                    <div id='supplement_menu_button' class='action_button' title="Add new names from CSV to the taxonomic tree">
                        <img src="{{ asset('images/csv_add.png') }}">
                        <div class="action_button_text">Add nodes from CSV</div>
                    </div>
                    <div id='add_menu_button' class='action_button' title="Add a single name using a form">
                        <img src="{{ asset('images/add.png') }}">
                        <div class="action_button_text">Add name manually</div>
                    </div>
                    <div id='edit_menu_button' class='action_button' style="display: none" title="Edit basic information about a name">
                        <img src="{{ asset('images/edit.png') }}">
                        <div class="action_button_text">Edit Node</div>
                    </div>
                    <div id='delete_leaf_menu_button' class='action_button' style="display: none" title="Delete a node without children (leaf node)">
                        <img src="{{ asset('images/delete_leaf.png') }}">
                        <div class="action_button_text">Delete Leaf Node</div>
                    </div>
                    <div id='delete_branch_menu_button' class='action_button' style="display: none" title="Delete a node with children (branch)">
                        <img src="{{ asset('images/delete_branch.png') }}">
                        <div class="action_button_text">Delete Branch</div>
                    </div>
                    <div id='move_menu_button' class='action_button' style="display: none" title="Move a leaf node to another part of the tree">
                        <img src="{{ asset('images/move.png') }}">
                        <div class="action_button_text">Move Node</div>
                    </div>
                    <div id='seeding_menu_button' class='action_button' title="Add dummy names (nodes) to the taxonomic tree">
                        <img src="{{ asset('images/seeding.png') }}">
                        <div class="action_button_text">Seeding</div>
                    </div>
                    <div id='clear_cache_button' class='action_button' title="Clear cache">
                        <img src="{{ asset('images/clear_cache.png') }}">
                        <div class="action_button_text">Clear Cache</div>
                    </div>
                    <div id='instructions_menu_button' class='action_button' title="Instructions">
                        <img src="{{ asset('images/instructions.png') }}">
                        <div class="action_button_text">Instructions</div>
                    </div>
                    <div style='clear: both'></div>
                </div>

                @include('web.forms.add')
                @include('web.forms.edit')
                @include('web.forms.move')
                @include('web.forms.build')
                @include('web.forms.supplement')
                @include('web.forms.seeding')
                @include('web.forms.instructions')

                <div id='file_result_div' style='margin-top: 20px'>
                    @if(!empty($errors))
                        <ul style='color:red'>
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    @endif
                    @if(Session::has('validation_errors'))
                        <ul style='color:red'>
                            @foreach(Session::get('validation_errors') as $error)
                                <li>Name ID: {{ $error['index'] }}, Field: {{ $error['field'] }} -> {{ $error['message'] }}</li>
                            @endforeach
                        </ul>
                    @endif
                    @if(Session::has('importation_errors'))
                        @foreach(Session::get('importation_errors') as $error)
                            <div class="alert alert-danger alert-dismissible" role="alert">
                                <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                                <strong>Error:</strong> Name ID: {{ $error['index'] }}, Field: {{ $error['field'] }} -> {{ $error['message'] }}
                            </div>
                        @endforeach
                    @endif
                </div>

        </div>

    </div>

</div>

<div id="deleteLeafModal" class="modal fade">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title">Confirmation</h4>
            </div>
            <div class="modal-body">
                <p>Do you really want to delete this scientific name?</p>
                <p class="text-warning"><small>This action can not be reversed.</small></p>
                <form id="delete_leaf_form">{{ Form::hidden('_token',csrf_token()) }}</form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary" data-dismiss="modal" id="delete_leaf_confirm_button">Delete It!</button>
            </div>
        </div>
    </div>
</div>

<div id="deleteBranchModal" class="modal fade">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title">Confirmation</h4>
            </div>
            <div class="modal-body">
                <p>Do you really want to delete this scientific name and all its descendants?</p>
                <p class="text-warning"><small>This action can not be reversed.</small></p>
                <form id="delete_branch_form">{{ Form::hidden('_token',csrf_token()) }}</form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary" data-dismiss="modal" id="delete_branch_confirm_button">Delete It!</button>
            </div>
        </div>
    </div>
</div>

@endsection