<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBlogCategory extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('blog_category', function (Blueprint $table) {
            $table->Increments('id');
            $table->string('name');
            $table->string('image')->nullable();
            $table->unsignedInteger('parent_id')->nullable();
            $table->index('parent_id');

            $table->integer('_lft')->nullable();
            $table->index('_lft');

            $table->integer('_rgt')->nullable();
            $table->index('_rgt');

            $table->integer('depth')->nullable();
            $table->timestamps();
        });

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('blog_category');
    }
}
