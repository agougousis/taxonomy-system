{!! Form::open(array('class'=>'form-horizontal','id'=>'seeding_form','style'=>'display: none')) !!}
    
    {{ Form::hidden('_token',csrf_token()) }}

    <div class="alert alert-info" role="alert">
        <img src="{{ asset('images/seeding.png') }}" class="action-title-image">
        <strong>Branch Seeding</strong>
    </div>

    <div class="form-group">
        <label for="seeding_root" class="col-sm-4 control-label">Seeding root *</label>
        <div class="col-sm-3">
          {{ Form::text('seeding_root','',array('class'=>'form-control')) }}              
        </div>         
        <div class="col-md-3">
            <div id="select_seeding_root_button" class="btn btn-default" style="display:inline-block; margin-left: -20px" title="Get seeding root from selected tree node"><span class="glyphicon glyphicon-zoom-in"></span></div>
        </div>
        <div class='col-sm-2' style="text-align: right">    
           <img src='{{ asset('images/info.png') }}' class='info-button' data-container='body' data-toggle='popover' data-placement='top' data-content='Select the node under which the nodes will be placed.'>
        </div>
    </div>

    <div class="form-group">
        <label for="how_many" class="col-sm-4 control-label">How many nodes *</label>
        <div class="col-sm-4">
          {{ Form::text('how_many','',array('class'=>'form-control')) }}                              
        </div>     
        <div class="col-sm-2"></div>
        <div class='col-sm-2' style="text-align: right">    
           <img src='{{ asset('images/info.png') }}' class='info-button' data-container='body' data-toggle='popover' data-placement='bottom' data-content='How many new nodes you want to be inserted.'>
        </div>
    </div>        
    
    <div style='text-align: center; margin-top: 30px'>
        <div id='seeding_node_button' class='btn btn-primary'>Start seeding</div>
        <div id='cancel_seeding_action' class='btn btn-default'>Cancel</div>
    </div>

    <div id='result_div' style='margin-top: 20px'></div>

{!! Form::close() !!}