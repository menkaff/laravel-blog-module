<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateBlogMenu extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('blog_menu', function (Blueprint $table) {
            $table->Increments('id');
            $table->string('name');
            $table->string('icon')->nullable();
            $table->string('url')->nullable();
            $table->string('target')->nullable();
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
        Schema::dropIfExists('blog_menu');
    }
}
