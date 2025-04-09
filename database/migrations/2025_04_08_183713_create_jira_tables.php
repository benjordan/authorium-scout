<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        // Product Managers Table
        Schema::create('product_managers', function (Blueprint $table) {
            $table->id();
            $table->string('account_id')->unique();
            $table->string('name');
            $table->string('email')->nullable();
            $table->string('avatar_url')->nullable();
            $table->timestamps();
        });

        // Issues Table
        Schema::create('issues', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('product_manager_id')->nullable();
            $table->foreign('product_manager_id')->references('id')->on('product_managers')->onDelete('set null');
            $table->string('jira_key')->unique();
            $table->string('summary');
            $table->longText('description')->nullable();
            $table->string('status');
            $table->string('type');
            $table->string('priority')->nullable();
            $table->string('size')->nullable();
            $table->string('release_commit_status')->nullable();
            $table->timestamps();
        });

        // Fix Versions Table
        Schema::create('fix_versions', function (Blueprint $table) {
            $table->id();
            $table->string('jira_id')->unique();
            $table->string('name');
            $table->date('release_date')->nullable();
            $table->boolean('released')->default(false);
            $table->timestamps();
        });

        // Components Table
        Schema::create('components', function (Blueprint $table) {
            $table->id();
            $table->string('jira_id')->unique();
            $table->string('name');
            $table->text('description')->nullable();
            $table->timestamps();
        });

        // Customers Table
        Schema::create('customers', function (Blueprint $table) {
            $table->id();
            $table->string('jira_id')->unique();
            $table->string('name');
            $table->timestamps();
        });

        // Pivot: Component <-> Issue
        Schema::create('component_issue', function (Blueprint $table) {
            $table->id();
            $table->foreignId('issue_id')->constrained()->onDelete('cascade');
            $table->foreignId('component_id')->constrained()->onDelete('cascade');
            $table->timestamps();
        });

        // Pivot: Customer <-> Issue
        Schema::create('customer_issue', function (Blueprint $table) {
            $table->id();
            $table->foreignId('issue_id')->constrained()->onDelete('cascade');
            $table->foreignId('customer_id')->constrained()->onDelete('cascade');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('customer_issue');
        Schema::dropIfExists('component_issue');
        Schema::dropIfExists('customers');
        Schema::dropIfExists('components');
        Schema::dropIfExists('fix_versions');
        Schema::dropIfExists('issues');
        Schema::dropIfExists('product_managers');
    }
};
