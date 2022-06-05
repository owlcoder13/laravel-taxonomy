<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class Taxonomy extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('taxonomy', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->softDeletes();

            $table->string('name');
            $table->string('slug')->nullable();
            $table->string('description')->nullable();
        });

        Schema::create('term', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->softDeletes();

            $table->string('name');
            $table->string('slug')->nullable();
        });

        Schema::create('term_taxonomy', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->softDeletes();

            $table->foreignId('taxonomy_id')->references('id')->on('taxonomy');
            $table->foreignId('term_id')->references('id')->on('term');
            $table->foreignId('parent_id')->nullable()->references('id')->on('term');

            $table->integer('position')->default(0);
        });

        Schema::create('term_bind', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->softDeletes();

            $table->foreignId('term_id')->references('id')->on('term');

            $table->morphs('item');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('term_bind');
        Schema::drop('term_taxonomy');
        Schema::drop('taxonomy');
        Schema::drop('term');
    }
}
