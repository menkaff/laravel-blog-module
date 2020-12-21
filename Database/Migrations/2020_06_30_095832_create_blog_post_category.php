<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBlogPostCategory extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('blog_post_category', function (Blueprint $table) {
            $table->Increments('id');

            $table->unsignedInteger('post_id');

            $table->foreign('post_id')
                ->references('id')->on('blog_post')
                ->onDelete('cascade')
                ->onUpdate('cascade');

            $table->unsignedInteger('category_id');

            $table->foreign('category_id')
                ->references('id')->on('blog_category')
                ->onDelete('cascade')
                ->onUpdate('cascade');

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
        Schema::dropIfExists('blog_post_category');
    }
}
