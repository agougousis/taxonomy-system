<?php

return [
    'rebuild_sname_create'      =>  array( // Parent is not required to exist in database
        'id'                =>  'required|integer|unique:snames,id',
        'parent_id'         =>  'integer',
        'sname'             =>  'required|string|max:128',
        'uninomen'          =>  'string|max:128',
        'rank'              =>  'required|string|max:80|exists:ranks,title',
        'accepted'          =>  'required|integer',
        'related_to_accepted' =>  'required_if:accepted,0|integer',
        'sortnophyl'        =>  'integer',
        'basionym'          =>  'integer',
        'FKaphiaBasionym'   =>  'integer',
        'protonym'          =>  'integer',
        'sortnospe'         =>  'integer',
        'authorship'        =>  'required|string|max:128',
        'authonym'          =>  'string|max:255',
        'nothonym'          =>  'integer',
        'prefavatar'        =>  'integer',
        'fk_ref_morphonym'  =>  'integer',
        'year'              =>  'integer',
        'fk_telangion_taxon'=>  'integer',
        'grouptax'          =>  'string|max:32',
        'phylum'            =>  'string|max:255',
        'remarks'           =>  'string',
        'comnames'          =>  'string|max:255',
        'comnames_languages'=>  'string|max:255',
        'fk_ref_comnames'   =>  'integer',
        'taxonp'            =>  'string|max:32',
        'taxongp'           =>  'string|max:32',
        'fk_eunis_morphonym'=>  'integer',
        'fk_aphia_morphonym'=>  'integer',
        'fk_aphia_ergonym'  =>  'integer',
        'fk_aphia_parent'   =>  'integer',
        'checked_by'        =>  'string|max:15',
        'checked_date'      =>  'date_format:d/m/Y H:i:s',
        'validated_by'      =>  'string|max:15',
        'validated_date'    =>  'date_format:d/m/Y H:i:s',
        'workfield'         =>  'string|max:15',
        'status_synonymy'   =>  'string|max:255',
        'status_onym'       =>  'string|max:255',
        'status_chresonym'  =>  'string|max:255'
    ),
    'sname_create'      =>  array(
        'id'                =>  'required|integer|unique:snames,id',
        'parent_id'         =>  'required_unless:rank,Kingdom|integer|exists:snames,id',
        'sname'             =>  'required|string|max:128',
        'uninomen'          =>  'string|max:128',
        'rank'              =>  'required|string|max:80|exists:ranks,title',
        'accepted'          =>  'required|integer',
        'related_to_accepted' =>  'required_if:accepted,0|integer',
        'sortnophyl'        =>  'integer',
        'basionym'          =>  'integer',
        'FKaphiaBasionym'   =>  'integer',
        'protonym'          =>  'integer',
        'sortnospe'         =>  'integer',
        'authorship'        =>  'required|string|max:128',
        'authonym'          =>  'string|max:255',
        'nothonym'          =>  'integer',
        'prefavatar'        =>  'integer',
        'fk_ref_morphonym'  =>  'integer',
        'year'              =>  'integer',
        'fk_telangion_taxon'=>  'integer',
        'grouptax'          =>  'string|max:32',
        'phylum'            =>  'string|max:255',
        'remarks'           =>  'string',
        'comnames'          =>  'string|max:255',
        'comnames_languages'=>  'string|max:255',
        'fk_ref_comnames'   =>  'integer',
        'taxonp'            =>  'string|max:32',
        'taxongp'           =>  'string|max:32',
        'fk_eunis_morphonym'=>  'integer',
        'fk_aphia_morphonym'=>  'integer',
        'fk_aphia_ergonym'  =>  'integer',
        'fk_aphia_parent'   =>  'integer',
        'checked_by'        =>  'string|max:15',
        'checked_date'      =>  'date_format:d/m/Y H:i:s',
        'validated_by'      =>  'string|max:15',
        'validated_date'    =>  'date_format:d/m/Y H:i:s',
        'workfield'         =>  'string|max:15',
        'status_synonymy'   =>  'string|max:255',
        'status_onym'       =>  'string|max:255',
        'status_chresonym'  =>  'string|max:255'
    ),
    'sname_update'      =>  array(
        'id'                =>  'required|integer|exists:snames,id',
        'sname'             =>  'required|string|max:128',
        'uninomen'          =>  'string|max:128',
        'accepted'          =>  'required|integer',
        'related_to_accepted' =>  'required_if:accepted,0|integer',
        'sortnophyl'        =>  'integer',
        'basionym'          =>  'integer',
        'FKaphiaBasionym'   =>  'integer',
        'protonym'          =>  'integer',
        'sortnospe'         =>  'integer',
        'authorship'        =>  'required|string|max:128',
        'authonym'          =>  'string|max:255',
        'nothonym'          =>  'integer',
        'prefavatar'        =>  'integer',
        'fk_ref_morphonym'  =>  'integer',
        'year'              =>  'integer',
        'fk_telangion_taxon'=>  'integer',
        'grouptax'          =>  'string|max:32',
        'phylum'            =>  'string|max:255',
        'remarks'           =>  'string',
        'comnames'          =>  'string|max:255',
        'comnames_languages'=>  'string|max:255',
        'fk_ref_comnames'   =>  'integer',
        'taxonp'            =>  'string|max:32',
        'taxongp'           =>  'string|max:32',
        'fk_eunis_morphonym'=>  'integer',
        'fk_aphia_morphonym'=>  'integer',
        'fk_aphia_ergonym'  =>  'integer',
        'fk_aphia_parent'   =>  'integer',
        'checked_by'        =>  'string|max:15',
        'checked_date'      =>  'date_format:d/m/Y H:i:s',
        'validated_by'      =>  'string|max:15',
        'validated_date'    =>  'date_format:d/m/Y H:i:s',
        'workfield'         =>  'string|max:15',
        'status_synonymy'   =>  'string|max:255',
        'status_onym'       =>  'string|max:255',
        'status_chresonym'  =>  'string|max:255'
    ),
    'add_nodes_from_file'      =>  array(
        'csv_file'                =>  'required|mimes:csv,txt|max:5000'
    ),
    'sname_move'      =>  array(
        'id'                =>  'required|integer|exists:snames,id',
        'new_parent_id'     =>  'required|integer|exists:snames,id'
    )
];