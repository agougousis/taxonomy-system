# Taxonomic Information System

This application was developed under the LifewatchGreece project and was used 
as base for the Greek Taxon Information System (GTIS) virtual laboratory which 
is part of LifewatchGreece portal.

## Frameworks/Packages/Libraries 

PHP

* Laravel 5, PHP Framework (https://laravel.com/)
* Laravel Collective, HTML and Form Builders for the Laravel (https://github.com/laravelcollective/html)
* Laravel-Excel, Eloquent-based package for PHP and CSV (https://github.com/Maatwebsite/Laravel-Excel)
* Guzzle, PHP HTTP client and webservice framework (https://github.com/guzzle/guzzle3)

Javascript/CSS

* jQuery (https://jquery.com/)
* toastr - non-blocking notifications library (https://github.com/CodeSeven/toastr)
* jsTree - Tree widget for jQuery (https://github.com/mbraak/jqTree)
* Bootstrap CSS Framework (http://getbootstrap.com/)

## Taxonomic rules enforced by the system

| New name belongs to        | Its parent can be  |
| ------------- |:-------------:|
| main rank B   |  |
|       | a name with the right previous main rank A       |
|  | a name with a subrank between A and B      |
| the first subrank to main rank A   |  |
|       | the main rank A       |
| the second or lower subrank to main rank A |       |
|       | the immediate previous subrank of A       |

## Taxonomic ranks used by the system

* Kingdom
* Plylum
* Subphylum
* Infraphylum
* Superclass
* Class
* Subclass
* Infraclass
* Superorder
* Order
* Suborder
* Infraorder
* Superfamily
* Family
* Subfamily
* Tribe
* Subtribe 
* Genus
* Subgenus
* Species
* Subspecies
* Variety
* Form

**Note:** The taxonomic tree displayed on the home page contains only the accepted 
names. Synonyms are displayed as part of the information related to the accepted
name. On the contrary, the tree on the management page contains both accepted 
names and synonyms. 