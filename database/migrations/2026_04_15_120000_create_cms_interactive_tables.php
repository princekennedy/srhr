<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('faqs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('website_id')->constrained('websites')->cascadeOnDelete();
            $table->string('question');
            $table->string('slug');
            $table->longText('answer');
            $table->foreignId('category_id')->nullable()->constrained('content_categories')->nullOnDelete();
            $table->string('audience', 30)->default('general');
            $table->string('visibility', 20)->default('public');
            $table->unsignedInteger('sort_order')->default(0);
            $table->boolean('is_published')->default(true);
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->index(['audience', 'visibility']);
            $table->unique(['website_id', 'slug']);
        });

        Schema::create('service_centers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('website_id')->constrained('websites')->cascadeOnDelete();
            $table->string('name');
            $table->string('slug');
            $table->foreignId('category_id')->nullable()->constrained('content_categories')->nullOnDelete();
            $table->string('district', 120)->nullable();
            $table->text('physical_address')->nullable();
            $table->string('contact_phone', 80)->nullable();
            $table->string('contact_email')->nullable();
            $table->string('service_hours', 120)->nullable();
            $table->text('summary')->nullable();
            $table->longText('services')->nullable();
            $table->string('audience', 30)->default('general');
            $table->string('visibility', 20)->default('public');
            $table->boolean('is_featured')->default(false);
            $table->boolean('is_active')->default(true);
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->index(['district', 'is_active']);
            $table->unique(['website_id', 'slug']);
        });

        Schema::create('quizzes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('website_id')->constrained('websites')->cascadeOnDelete();
            $table->string('title');
            $table->string('slug');
            $table->text('summary')->nullable();
            $table->longText('intro_text')->nullable();
            $table->string('audience', 30)->default('general');
            $table->string('visibility', 20)->default('public');
            $table->string('status', 20)->default('draft');
            $table->timestamp('published_at')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->index(['status', 'visibility']);
            $table->unique(['website_id', 'slug']);
        });

        Schema::create('quiz_questions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('website_id')->constrained('websites')->cascadeOnDelete();
            $table->foreignId('quiz_id')->constrained('quizzes')->cascadeOnDelete();
            $table->text('prompt');
            $table->text('help_text')->nullable();
            $table->string('question_type', 40)->default('single_choice');
            $table->unsignedInteger('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->index(['quiz_id', 'sort_order']);
        });

        Schema::create('quiz_options', function (Blueprint $table) {
            $table->id();
            $table->foreignId('website_id')->constrained('websites')->cascadeOnDelete();
            $table->foreignId('quiz_question_id')->constrained('quiz_questions')->cascadeOnDelete();
            $table->string('option_text');
            $table->text('feedback')->nullable();
            $table->boolean('is_correct')->default(false);
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();

            $table->index(['quiz_question_id', 'sort_order']);
        });

        Schema::create('app_settings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('website_id')->constrained('websites')->cascadeOnDelete();
            $table->string('key');
            $table->string('label');
            $table->text('value')->nullable();
            $table->string('group', 60)->default('general');
            $table->string('input_type', 30)->default('text');
            $table->text('description')->nullable();
            $table->boolean('is_public')->default(false);
            $table->timestamps();

            $table->index(['group', 'label']);
            $table->unique(['website_id', 'key']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('app_settings');
        Schema::dropIfExists('quiz_options');
        Schema::dropIfExists('quiz_questions');
        Schema::dropIfExists('quizzes');
        Schema::dropIfExists('service_centers');
        Schema::dropIfExists('faqs');
    }
};