<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSnamesTable extends Migration {

  /**
   * Run the migrations.
   *
   * @return void
   */
  public function up() {
    Schema::create('snames', function(Blueprint $table) {

        $table->bigInteger('id')->primary();                    // IDmorphonym
        $table->integer('parent_id')->nullable()->index();      // FKgetangiotaxon
        $table->string('path',250)->nullable();
        $table->integer('leaves_num');

        $table->string('sname', 128);                           // Morphonym
        $table->string('uninomen',128)->nullable();             // Uninomen
        $table->string('rank',80)->nullable();                  // TaxRank
        $table->smallInteger('accepted');                       // Ergonym
        $table->bigInteger('related_to_accepted')->nullable();  // FKergonym

        $table->bigInteger('sortnophyl')->nullable();           // SortNoPhyl
        $table->smallInteger('basionym')->nullable();           //
        $table->bigInteger('FKaphiaBasionym')->nullable();      //
        $table->smallInteger('protonym')->nullable();           //
        $table->bigInteger('sortnospe')->nullable();            //
        $table->string('authorship',128);                     //
        $table->string('authonym',255)->nullable();             //
        $table->smallInteger('nothonym')->nullable();           //
        $table->smallInteger('prefavatar')->nullable();                     //
        $table->bigInteger('fk_ref_morphonym')->nullable();     // FKrefMorphonym
        $table->smallInteger('year')->nullable();               // Yearonym
        $table->bigInteger('fk_telangio_taxon')->nullable();   //
        $table->bigInteger('fk_getangio_taxon')->nullable();   //
        $table->string('grouptax',32)->nullable();              // GroupTax
        $table->string('phylum',255)->nullable();               // Phylum
        $table->text('remarks')->nullable();                    // RemarksNomen
        $table->string('comnames',255)->nullable();             //
        $table->string('comnames_languages',255)->nullable();   //
        $table->bigInteger('fk_ref_comnames')->nullable();      //
        $table->string('taxonp',32)->nullable();                //
        $table->string('taxongp',32)->nullable();               //
        $table->bigInteger('fk_eunis_morphonym')->nullable();   //
        $table->bigInteger('fk_aphia_morphonym')->nullable();   //
        $table->bigInteger('fk_aphia_ergonym')->nullable();     //
        $table->bigInteger('fk_aphia_parent')->nullable();      //
        $table->string('checked_by',15)->nullable();            //
        $table->dateTime('checked_date')->nullable();           //
        $table->string('validated_by',15)->nullable();          //
        $table->dateTime('validated_date')->nullable();         //
        $table->string('workfield',15)->nullable();             //
        $table->string('status_synonymy',255)->nullable();      //
        $table->string('status_onym',255)->nullable();          //
        $table->string('status_chresonym',255)->nullable();     //

      $table->timestamps();
    });
  }

  /**
   * Reverse the migrations.
   *
   * @return void
   */
  public function down() {
    Schema::drop('snames');
  }

}
