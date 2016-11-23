{!! Form::open(array('class'=>'form-horizontal','id'=>'move_node_form','style'=>'display: none')) !!}
    
    {{ Form::hidden('_token',csrf_token()) }}

    <div class="alert alert-info" role="alert">
        <img src="{{ asset('images/edit.png') }}" class="action-title-image">
        <strong>Move a scientific name</strong>
    </div>

    <div class="form-group">
        <label for="id" class="col-sm-4 control-label">IDmorphonym *</label>
        <div class="col-sm-3">
          {{ Form::text('id','',array('class'=>'form-control','disabled'=>'disabled')) }}              
        </div>            
    </div> 

    <div class="form-group">
        <label for="sname" class="col-sm-4 control-label">Scientific Name *</label>
        <div class="col-sm-8">
          {{ Form::text('sname','',array('class'=>'form-control','disabled'=>'disabled')) }}              
        </div>            
    </div>

    <div class="form-group">
        <label for="parent_id" class="col-sm-4 control-label">Parent ID *</label>
        <div class="col-sm-3">
          {{ Form::text('parent_id','',array('class'=>'form-control','disabled'=>'disabled')) }}              
        </div>                                
    </div>

    <div class="form-group">
        <label for="new_parent_id" class="col-sm-4 control-label">New Parent ID *</label>
        <div class="col-sm-3">
          {{ Form::text('new_parent_id','',array('class'=>'form-control')) }}              
        </div>         
        <div class="col-md-5">
            <div id="select_new_parent_button" class="btn btn-default" style="display:inline-block; margin-left: -20px" title="Get parent id from selected tree node"><span class="glyphicon glyphicon-zoom-in"></span></div>
        </div>
    </div>

    <div class="form-group">
        <label for="rank" class="col-sm-4 control-label">Rank *</label>
        <div class="col-sm-4">
          {{ Form::text('rank','',array('class'=>'form-control','disabled'=>'disabled')) }}                              
        </div>                                    
    </div>        

    <div class="form-group">
        <label for="accepted" class="col-sm-4 control-label">Accepted *</label>
        <div class="col-sm-8">
          {{ Form::checkbox('accepted','1',true,array('disabled'=>'disabled')) }}              
        </div>            
    </div>  

    <div class="form-group">
        <label for="related_to_accepted" class="col-sm-4 control-label">Accepted name ID *</label>
        <div class="col-sm-3">
          {{ Form::text('related_to_accepted','',array('class'=>'form-control','disabled'=>'disabled')) }}              
        </div>            
    </div>
    
    <div style='text-align: center; margin-top: 30px'>
        <div id='move_node_button' class='btn btn-primary'>Move node</div>
        <div id='cancel_move_action' class='btn btn-default'>Cancel</div>
    </div>

    <div id='result_div' style='margin-top: 20px'></div>

{!! Form::close() !!}