{!! Form::open(array('class'=>'form-horizontal','id'=>'edit_node_form','style'=>'display: none')) !!}
    
    {{ Form::hidden('_token',csrf_token()) }}

    <div class="alert alert-info" role="alert">
        <img src="{{ asset('images/edit.png') }}" class="action-title-image">
        <strong>Edit a scientific name</strong>
    </div>

    <div class="form-group">
        <label for="id" class="col-sm-4 control-label">IDmorphonym *</label>
        <div class="col-sm-3">
          {{ Form::text('id','',array('class'=>'form-control','readonly'=>'readonly')) }}              
        </div>            
    </div> 

    <div class="form-group">
        <label for="sname" class="col-sm-4 control-label">Scientific Name *</label>
        <div class="col-sm-8">
          {{ Form::text('sname','',array('class'=>'form-control')) }}              
        </div>            
    </div>

    <div class="form-group">
        <label for="parent_id" class="col-sm-4 control-label">Parent ID *</label>
        <div class="col-sm-3">
          {{ Form::text('parent_id','',array('class'=>'form-control','disabled'=>'disabled')) }}              
        </div>                                
    </div>

    <div class="form-group">
        <label for="rank" class="col-sm-4 control-label">Rank *</label>
        <div class="col-sm-4">
          {{ Form::select('rank',$ranks,'Kingdom',array('class'=>'form-control','disabled'=>'disabled')) }}                            
        </div>                                    
    </div>        

    <div class="form-group">
        <label for="authorship" class="col-sm-4 control-label">Authorship *</label>
        <div class="col-sm-8">
          {{ Form::text('authorship','',array('class'=>'form-control')) }}              
        </div>            
    </div>

    <div class="form-group">
        <label for="authonym" class="col-sm-4 control-label">Authonym</label>
        <div class="col-sm-8">
          {{ Form::text('authonym','',array('class'=>'form-control')) }}              
        </div>            
    </div>

    <div class="form-group">
        <label for="accepted" class="col-sm-4 control-label">Accepted *</label>
        <div class="col-sm-8">
          {{ Form::checkbox('accepted','1',true) }}              
        </div>            
    </div>  

    <div class="form-group">
        <label for="related_to_accepted" class="col-sm-4 control-label">Accepted name ID *</label>
        <div class="col-sm-3">
          {{ Form::text('related_to_accepted','',array('class'=>'form-control')) }}              
        </div>            
    </div>

    <script type="text/javascript">
        $('#edit_node_form input[name="accepted"]').on('change',function(){
            if($('#edit_node_form input[name="accepted"]').is(':checked')){
                $('#edit_node_form input[name="related_to_accepted"]').prop('disabled',false);
            } else {
                $('#edit_node_form input[name="related_to_accepted"]').prop('disabled',true);
            }
        });
    </script>

    <div class="form-group">
        <label for="uninomen" class="col-sm-4 control-label">Uninomen</label>
        <div class="col-sm-8">
          {{ Form::text('uninomen','',array('class'=>'form-control')) }}              
        </div>            
    </div>
    
    <div class="form-group">
        <label for="sortnophyl" class="col-sm-4 control-label">Sortnophyl</label>
        <div class="col-sm-8">
          {{ Form::text('sortnophyl','',array('class'=>'form-control')) }}              
        </div>            
    </div>
    
    <div class="form-group">
        <label for="basionym" class="col-sm-4 control-label">Basionym</label>
        <div class="col-sm-8">
          {{ Form::text('basionym','',array('class'=>'form-control')) }}              
        </div>            
    </div>
    
    <div class="form-group">
        <label for="fk_aphia_basionym" class="col-sm-4 control-label">FKaphiaBasionym</label>
        <div class="col-sm-8">
          {{ Form::text('fk_aphia_basionym','',array('class'=>'form-control')) }}              
        </div>            
    </div>
    
    <div class="form-group">
        <label for="protonym" class="col-sm-4 control-label">Protonym</label>
        <div class="col-sm-8">
          {{ Form::text('protonym','',array('class'=>'form-control')) }}              
        </div>            
    </div>
    
    <div class="form-group">
        <label for="sortnospe" class="col-sm-4 control-label">Sortnospe</label>
        <div class="col-sm-8">
          {{ Form::text('sortnospe','',array('class'=>'form-control')) }}              
        </div>            
    </div>
    
    <div class="form-group">
        <label for="nothonym" class="col-sm-4 control-label">Nothonym</label>
        <div class="col-sm-8">
          {{ Form::text('nothonym','',array('class'=>'form-control')) }}              
        </div>            
    </div>
    
    <div class="form-group">
        <label for="prefavatar" class="col-sm-4 control-label">Prefavatar</label>
        <div class="col-sm-8">
          {{ Form::text('prefavatar','',array('class'=>'form-control')) }}              
        </div>            
    </div>
    
    <div class="form-group">
        <label for="fk_ref_morphonym" class="col-sm-4 control-label">FKrefMorphonym</label>
        <div class="col-sm-8">
          {{ Form::text('fk_ref_morphonym','',array('class'=>'form-control')) }}              
        </div>            
    </div>
    
    <div class="form-group">
        <label for="year" class="col-sm-4 control-label">Year</label>
        <div class="col-sm-8">
          {{ Form::text('year','',array('class'=>'form-control')) }}              
        </div>            
    </div>
    
    <div class="form-group">
        <label for="fk_telangio_taxon" class="col-sm-4 control-label">FK telangio taxon</label>
        <div class="col-sm-8">
          {{ Form::text('fk_telangio_taxon','',array('class'=>'form-control')) }}              
        </div>            
    </div>
    
    <div class="form-group">
        <label for="fk_getangio_taxon" class="col-sm-4 control-label">FK getangio taxon</label>
        <div class="col-sm-8">
          {{ Form::text('fk_getangio_taxon','',array('class'=>'form-control')) }}              
        </div>            
    </div>
    
    <div class="form-group">
        <label for="grouptax" class="col-sm-4 control-label">Grouptax</label>
        <div class="col-sm-8">
          {{ Form::text('grouptax','',array('class'=>'form-control')) }}              
        </div>            
    </div>
    
    <div class="form-group">
        <label for="phylum" class="col-sm-4 control-label">Phylum</label>
        <div class="col-sm-8">
          {{ Form::text('phylum','',array('class'=>'form-control')) }}              
        </div>            
    </div>
    
    <div class="form-group">
        <label for="remarks" class="col-sm-4 control-label">Remarks</label>
        <div class="col-sm-8">
          {{ Form::textarea('remarks','',array('class'=>'form-control')) }}              
        </div>            
    </div>
    
    <div class="form-group">
        <label for="comnames" class="col-sm-4 control-label">Comnames</label>
        <div class="col-sm-8">
          {{ Form::text('comnames','',array('class'=>'form-control')) }}              
        </div>            
    </div>
    
    <div class="form-group">
        <label for="comnames_languages" class="col-sm-4 control-label">comnames_languages</label>
        <div class="col-sm-8">
          {{ Form::text('comnames_languages','',array('class'=>'form-control')) }}              
        </div>            
    </div>

    <div class="form-group">
        <label for="fk_ref_comnames" class="col-sm-4 control-label">FK ref comnames</label>
        <div class="col-sm-8">
          {{ Form::text('fk_ref_comnames','',array('class'=>'form-control')) }}              
        </div>            
    </div>
    
    <div class="form-group">
        <label for="taxonp" class="col-sm-4 control-label">TaxonP</label>
        <div class="col-sm-8">
          {{ Form::text('taxonp','',array('class'=>'form-control')) }}              
        </div>            
    </div>
    
    <div class="form-group">
        <label for="taxongp" class="col-sm-4 control-label">TaxonGP</label>
        <div class="col-sm-8">
          {{ Form::text('taxongp','',array('class'=>'form-control')) }}              
        </div>            
    </div>
    
    <div class="form-group">
        <label for="fk_eunis_morphonym" class="col-sm-4 control-label">FK eunis morphonym</label>
        <div class="col-sm-8">
          {{ Form::text('fk_eunis_morphonym','',array('class'=>'form-control')) }}              
        </div>            
    </div>
    
    <div class="form-group">
        <label for="fk_aphia_morphonym" class="col-sm-4 control-label">FK aphia morphonym</label>
        <div class="col-sm-8">
          {{ Form::text('fk_aphia_morphonym','',array('class'=>'form-control')) }}              
        </div>            
    </div>
    
    <div class="form-group">
        <label for="fk_eunis_ergonym" class="col-sm-4 control-label">FK eunis ergonym</label>
        <div class="col-sm-8">
          {{ Form::text('fk_eunis_ergonym','',array('class'=>'form-control')) }}              
        </div>            
    </div>
    
    <div class="form-group">
        <label for="fk_aphia_parent" class="col-sm-4 control-label">FK aphia parent</label>
        <div class="col-sm-8">
          {{ Form::text('fk_aphia_parent','',array('class'=>'form-control')) }}              
        </div>            
    </div>

    <div class="form-group">
        <label for="checked_by" class="col-sm-4 control-label">Checked by</label>
        <div class="col-sm-8">
          {{ Form::text('checked_by','',array('class'=>'form-control')) }}              
        </div>            
    </div>
    
    <div class="form-group">
        <label for="checked_date" class="col-sm-4 control-label">Checked date</label>
        <div class="col-sm-8">
          {{ Form::text('checked_date','',array('class'=>'form-control')) }}              
        </div>            
    </div>
    
    <div class="form-group">
        <label for="validated_by" class="col-sm-4 control-label">Validated by</label>
        <div class="col-sm-8">
          {{ Form::text('validated_by','',array('class'=>'form-control')) }}              
        </div>            
    </div>
    
    <div class="form-group">
        <label for="validated_date" class="col-sm-4 control-label">Validated date</label>
        <div class="col-sm-8">
          {{ Form::text('validated_date','',array('class'=>'form-control')) }}              
        </div>            
    </div>
    
    <div class="form-group">
        <label for="workfield" class="col-sm-4 control-label">Workfield</label>
        <div class="col-sm-8">
          {{ Form::text('workfield','',array('class'=>'form-control')) }}              
        </div>            
    </div>
    
    <div class="form-group">
        <label for="status_synonymy" class="col-sm-4 control-label">Status synonymy</label>
        <div class="col-sm-8">
          {{ Form::text('status_synonymy','',array('class'=>'form-control')) }}              
        </div>            
    </div>
    
    <div class="form-group">
        <label for="status_onym" class="col-sm-4 control-label">Status Onym</label>
        <div class="col-sm-8">
          {{ Form::text('status_onym','',array('class'=>'form-control')) }}              
        </div>            
    </div>
    
    <div class="form-group">
        <label for="status_chresonym" class="col-sm-4 control-label">Status Chresonym</label>
        <div class="col-sm-8">
          {{ Form::text('status_chresonym','',array('class'=>'form-control')) }}              
        </div>            
    </div>
    
    <div style='text-align: center; margin-top: 30px'>
        <div id='edit_node_button' class='btn btn-primary'>Save node</div>
        <div id='cancel_edit_action' class='btn btn-default'>Cancel</div>
    </div>

    <div id='result_div' style='margin-top: 20px'></div>

{!! Form::close() !!}