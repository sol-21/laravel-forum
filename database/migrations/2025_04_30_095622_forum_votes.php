<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void {
        Schema::create('forum_votes', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('post_id');

            $table->foreign('post_id')->references('id')->on('forum_posts')->onUpdate('cascade')->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onUpdate('cascade')->onDelete('restrict');
            $table->enum('type', ['upvote', 'downvote']);
            $table->integer('upvote_count')->default(0);
            $table->integer('downvote_count')->default(0);
            $table->unique(['user_id', 'post_id']);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void {
        Schema::dropIfExists('forum_votes');
    }
};
