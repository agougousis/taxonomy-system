{!! Form::open(array('url'=>url('load_and_rebuild?clear=no'),'class'=>'form-horizontal','id'=>'supplement_node_form','enctype'=>'multipart/form-data','style'=>'display: none')) !!}

    {{ Form::hidden('_token',csrf_token()) }}

    <div class="alert alert-info" role="alert">
        <img src="{{ asset('images/csv_add.png') }}" class="action-title-image">
        <strong>Supplement tree from CSV file</strong>
    </div>                

    <div class="form-group">
        <!-- A label for the line (optional) -->                        
        <div class="col-sm-4">
                <!-- The file selector -->
                <span class="btn btn-default btn-file" style="width:100%">
                        Select a file...
                        <input type="file" name="csv_file">
                </span>
        </div>
        <div class="col-sm-8" id="files_to_upload">
                <!-- A field to display the selected file -->
                <input type="text" name="selected_file" class="form-control" disabled>
        </div>                       
    </div>

    <div style='text-align: center;  margin-top: 30px'>
        <button class='btn btn-primary' type="submit">Import</button>
        <div id='cancel_supplement_action' class='btn btn-default'>Cancel</div>
    </div>

    <div id="instructionsButton" onclick="toggleInstructions()" class="linkStyle" style="margin-top: 30px; float: right">Show Instructions</div>
     <div style="clear: both"></div>
    <div id="csv_instructions" style="display: none; margin-top: 15px;">
        
        <p><span style="text-decoration: underline">Note 1:</span> All names with rank equal to "Kingdom" should have an empty parent_id!!</p>
        <p><span style="text-decoration: underline">Note 2:</span> All ranks should starts with a capital letter.</p>
        
        Please use the following columns names for the CSV file:
        <table class="table, table-hover" style="margin-top: 10px">
            <thead>
                <th>column name</th>
                <th>description</th>
            </thead>
            <tbody>
            <tr><td>id</td><td>(id_morphonym) Unique identifier of the scientific name. Derived from grBioDB.
                                Number >=2,000,000 are the ones added by N. Bailly starting in
                                February 2015.</td></tr>
            <tr><td>sname</td><td>(morphonym) Uni-, bi-, tri- etc. nomen; includes var. (variety), f. (forma), n. (natio).
                                Does not contain aff., cf. If the name is supraspecific then TaxName
                                = SciName.</td></tr>
            <tr><td>rank</td><td>(tax_rank) Rank of the taxon.</td></tr>
            <tr><td>authorship</td><td>Author(s), year, and parentheses. Formats without or with parenthses: LastName, Year; LastName1 & LastName2, Year;
                                LastName1, LastName2, ..., LastNamei, ... & LastNamen; LastName, Intial(s)., Year</td></tr>
            <tr><td>authonym</td><td>See authorship for layout rules.</td></tr>
            <tr><td>accepted</td><td>(ergonym) Marks a current accepted name. In that case, the IDmorphonym = FKergonym.</td></tr>
            <tr><td>related_to_accepted</td><td>(fk_ergonym) Forward key to the accepted name. If the name is a current accepted one, this key is equal to the ID.</td></tr>
            <tr><td>uninomen</td><td>Uninomen only. If the name is supraspecific then Morphonym = Uninomen.</td></tr>
            <tr><td>sortnophyl</td><td>Ordering number in NB phylogenetic order.</td></tr>
            <tr><td>basionym</td><td>Marks the corrected original spelling. If the spelling of the protonym is correct, then protonym=basionym.</td></tr>
            <tr><td>fk_aphia_basionym</td><td>Forward key to the basionym, correctly spelled (including after correction of published incorrect original spelling).</td></tr>
            <tr><td>protonym</td><td>Marks the original spelling for an introduced name (=Originally published in FishBase).</td></tr>
            <tr><td>sortnospe</td><td>Ordering number from the specialist.</td></tr>
            <tr><td>nothonym</td><td>Marks a misspelled name.</td></tr>
            <tr><td>prefavatar</td><td>Marks a preferred representation (i.e., without a subgenus).</td></tr>
            <tr><td>fk_ref_morphonym</td><td>Forward key to the bibliographic reference using that name.</td></tr>
            <tr><td>year</td><td>In the case of a range, the most recent year. The full range is indicated in the nomenclature remarks.</td></tr>
            <tr><td>fk_telangio_taxon</td><td>Forward key to the next 'parent' level taxon.</td></tr>
            <tr><td>parent_id</td><td>(fk_getangio_taxon) Forward key to the next 'parent' taxon.</td></tr>
            <tr><td>grouptax</td><td>Management taxon group.</td></tr>
            <tr><td>phylum</td><td>Phylum for data management.</td></tr>
            <tr><td>remarks</td><td>Any remarks about the nomenclature.</td></tr>
            <tr><td>comnames</td><td>List of common names.</td></tr>
            <tr><td>comnames_languages</td><td>List of respective languages.</td></tr>
            <tr><td>fk_ref_comnames</td><td>Forward key to the bibliographic reference on the common names.</td></tr>
            <tr><td>taxonp</td><td>Name of the parent taxon.</td></tr>
            <tr><td>taxongp</td><td>Name of the grandparent taxon.</td></tr>
            <tr><td>fk_eunis_morphonym</td><td>Forward key to the name in the EEA EUNIS database.</td></tr>
            <tr><td>fk_aphia_morphonym</td><td>Forward key to the name in WoRMS.</td></tr>
            <tr><td>fk_eunis_ergonym</td><td>Forward key to the accepted name in WoRMS.</td></tr>
            <tr><td>fk_aphia_parent</td><td>Forward key to the parent taxon in WoRMS.</td></tr>
            <tr><td>checked_by</td><td>Initials of the data manager who prepared the data.</td></tr>
            <tr><td>checked_date</td><td>Date of check.</td></tr>
            <tr><td>validated_by</td><td>Initials of the specialist.</td></tr>
            <tr><td>validated_date</td><td>Date of validation (= date of the eamil sending the validated list).</td></tr>
            <tr><td>workfield</td><td>A field for any work or temporary flag.</td></tr>
            <tr><td>status_synonymy</td><td></td></tr>
            <tr><td>status_onym</td><td></td></tr>
            <tr><td>status_chresonym</td><td></td></tr>
            <tr><td>fossil</td><td></td></tr>
            </tbody>
        </table>
    </div>

{!! Form::close() !!} 



    <script type="text/javascript">
        
        $("input[name='csv_file']").change(function(){
                // This is a loop that can be used in case there are many selected files
                for(var i=0; i< this.files.length; i++){
                        var file = this.files[i];
                        name = file.name.toLowerCase();						
                        $('input[name="selected_file"]').val(name);
                }
        });
        
        function toggleInstructions(){
            $('#csv_instructions').toggle();
            var buttonText = $('#instructionsButton').text();
            if(buttonText == 'Show Instructions'){
                $('#instructionsButton').html('Hide Instructions');
            } else {
                $('#instructionsButton').html('Show Instructions');
            }
        }
        
    </script>
