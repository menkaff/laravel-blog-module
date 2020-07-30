<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBlogComment extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('blog_comment', function (Blueprint $table) {
            $table->Increments('id');
            $table->unsignedInteger('parent_id')->nullable();

            $table->text('content');

            $table->unsignedInteger('user_id');
            $table->string('user_table')->default('usermodule_users');
            $table->unsignedInteger('post_id');
            $table->foreign('post_id')
                ->references('id')->on('blog_post')
                ->onDelete('cascade')
                ->onUpdate('cascade');

            $table->unsignedTinyInteger('is_confirm')->default(0);

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
        Schema::dropIfExists('blog_comment');
    }
}
