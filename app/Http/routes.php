<?php

// Public API routes
Route::group(['prefix'=>'api','middleware' => ['myapi']], function () {
    Route::get('/names', array('uses'=>'ApiController@listing'));   // Searching throughout the tree (no synonyms)
    Route::get('/names/{nid}', array('uses'=>'ApiController@read'))->where('nid', '[0-9]+');  // Retrieve information about a certain name
    Route::get('/names/{nid}/synonyms', array('uses'=>'ApiController@synonyms'))->where('nid', '[0-9]+');    // Retrieve the synonyms of an accepted name
    Route::get('/names/{nid}/children', array('uses'=>'ApiController@children'))->where('nid', '[0-9]+');    // Retrieve the children of a node (no synonyms)
    Route::get('/names/{nid}/ancestors', array('uses'=>'ApiController@ancestors'))->where('nid', '[0-9]+');  // Retrieve the ancestors chain of a child node (with synonyms that are part of the ancestors chain)
});

// Admin routes
Route::group(['middleware' => ['admin']], function () {
    // Clear cache
    Route::post('/clear_cache', array('uses'=>'AdminController@clearCache'));

    // HTML-Page routes
    Route::get('/manage', array('uses'=>'AdminController@managePage'));

    // JSON endpoints (to be used with AJAX)
    Route::post('/names', array('uses'=>'AdminController@create'));
    Route::post('/names/{nid}/seeding', array('uses'=>'AdminController@nodeSeeding'))->where('nid', '[0-9]+');
    Route::put('/names', array('uses'=>'AdminController@update'));
    Route::put('/names/move', array('uses'=>'AdminController@move'));
    Route::delete('/names/{nid}', array('uses'=>'AdminController@delete'))->where('nid', '[0-9]+');
    //Route::post('/load_and_rebuild', array('uses'=>'WebController@loadAndRebuild'));
    Route::post('/load_and_rebuild', array('uses'=>'WebController@loadDepthFirst'));
});

// Logged user routes
Route::group(['middleware' => ['logged']], function () {
    // HTML-Page routes
    Route::get('/home', array('uses'=>'WebController@home'));
    Route::get('/api_doc', array('uses'=>'WebController@documentation'));   // Searching throughout the tree (no synonyms)

    // JSON endpoints (to be used with AJAX)
    Route::get('/tree_roots', array('uses'=>'WebController@treeRoots'));   // To initialize the tree UI
    Route::get('/node_children', array('uses'=>'WebController@nodeChildren')); // Unfold a parent node (the js library requires a get parameter in the URL)
    Route::get('/all_node_children', array('uses'=>'WebController@allNodeChildren')); // Same as /node_children but includes the not-accepted names

    // Help or Testing routes
    //Route::get('/seed', array('uses'=>'WebController@seed_names'));
});

// Visitor routes
Route::group(['middleware' => ['visitors']], function () {
    Route::get('/', array('uses'=>'WebController@index'));

     // Authentication troutes
    Route::auth();
});
