<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBlogPost extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('blog_post', function (Blueprint $table) {
            $table->Increments('id');
            $table->string('title')->nullable();
            $table->string('url');
            $table->unsignedInteger('user_id');
            $table->string('user_table')->default('usermodule_users');
            $table->text('content');
            $table->text('excerpt');
            $table->string('image')->nullable();
            $table->string('video')->nullable();
            $table->enum('status', ['publish', 'draft'])->default('publish');
            $table->unsignedTinyInteger('is_comment')->default(1);

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
        Schema::dropIfExists('blog_post');
    }
}
