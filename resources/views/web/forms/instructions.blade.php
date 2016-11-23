<style type="text/css">
    .left-aligned-table tr td {
        text-align: left !important;
    }
    .left-aligned-table th {
        background-color: khaki !important;
    }
    .list-group-item {
        max-width: 250px;
    }
</style>

<div id="instructions_form" style="display: none">

    <div class="alert alert-info" role="alert">
        <img src="{{ asset('images/instructions.png') }}" class="action-title-image">
        <strong>Instructions</strong>
    </div>
    <div style="font-weight: bold; margin-bottom: 10px">Taxonomic ranks supported/allowed in GTIS:</div>
    <ul class="list-group">
        <li class="list-group-item"><span class="badge">Main</span><strong>Kingdom</strong></li>
        <li class="list-group-item"><span class="badge">Main</span><strong>Plylum</strong></li>
        <li class="list-group-item">Subphylum</li>
        <li class="list-group-item">Infraphylum</li>
        <li class="list-group-item">Superclass</li>
        <li class="list-group-item"><span class="badge">Main</span><strong>Class</strong></li>
        <li class="list-group-item">Subclass</li>
        <li class="list-group-item">Infraclass</li>
        <li class="list-group-item">Superorder</li>
        <li class="list-group-item"><span class="badge">Main</span><strong>Order</strong></li>
        <li class="list-group-item">Suborder</li>
        <li class="list-group-item">Infraorder</li>
        <li class="list-group-item">Superfamily</li>
        <li class="list-group-item"><span class="badge">Main</span><strong>Family</strong></li>
        <li class="list-group-item">Subfamily</li>
        <li class="list-group-item">Tribe</li>
        <li class="list-group-item">Subtribe </li>
        <li class="list-group-item"><span class="badge">Main</span><strong>Genus</strong></li>
        <li class="list-group-item">Subgenus</li>
        <li class="list-group-item"><span class="badge">Main</span><strong>Species</strong></li>
        <li class="list-group-item">Subspecies</li>
        <li class="list-group-item">Variety</li>
        <li class="list-group-item">Form</li>
    </ul>

    <div style="font-weight: bold; margin-bottom: 10px; margin-top: 20px">
        Parent-child rules when adding a new name:
    </div>
    <table class="table table-bordered left-aligned-table" style="background-color: white">
        <thead>
            <th>New name belongs to</th>
            <th>Its parent can be</th>
        </thead>
        <tbody>
            <tr>
                <td>main rank B</td>
                <td></td>
            </tr>
            <tr>
                <td></td>
                <td>
                    a name with the right previous main rank A
                    <br>
                    a name with a subrank between A and B
                </td>
            </tr>
            <tr>
                <td>the first subrank to main rank A</td>
                <td>the main rank A</td>
            </tr>
            <tr>
                <td>the second or lower subrank to main rank A</td>
                <td>the immediate previous subrank of A</td>
            </tr>
        </tbody>
    </table>


    <div style='text-align: center; margin-top: 30px'>
        <div id='cancel_instructions_action' class='btn btn-default'>Close</div>
    </div>

</div>