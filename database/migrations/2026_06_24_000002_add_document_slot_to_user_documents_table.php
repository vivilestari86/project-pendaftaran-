<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('user_documents', function (Blueprint $table) {
            if (! Schema::hasColumn('user_documents', 'document_slot')) {
                $table->unsignedTinyInteger('document_slot')->default(1)->after('document_type');
            }
        });

        Schema::table('user_documents', function (Blueprint $table) {
            if (! $this->indexExists('user_documents_user_id_fk_index')) {
                $table->index('user_id', 'user_documents_user_id_fk_index');
            }
        });

        Schema::table('user_documents', function (Blueprint $table) {
            if ($this->indexExists('user_documents_user_id_document_type_unique')) {
                $table->dropUnique('user_documents_user_id_document_type_unique');
            }

            if (! $this->indexExists('user_documents_user_type_slot_unique')) {
                $table->unique(['user_id', 'document_type', 'document_slot'], 'user_documents_user_type_slot_unique');
            }
        });
    }

    public function down(): void
    {
        Schema::table('user_documents', function (Blueprint $table) {
            if ($this->indexExists('user_documents_user_type_slot_unique')) {
                $table->dropUnique('user_documents_user_type_slot_unique');
            }

            if (! $this->indexExists('user_documents_user_id_document_type_unique')) {
                $table->unique(['user_id', 'document_type']);
            }
        });

        Schema::table('user_documents', function (Blueprint $table) {
            if ($this->indexExists('user_documents_user_id_fk_index')) {
                $table->dropIndex('user_documents_user_id_fk_index');
            }

            if (Schema::hasColumn('user_documents', 'document_slot')) {
                $table->dropColumn('document_slot');
            }
        });
    }

    private function indexExists(string $indexName): bool
    {
        return in_array($indexName, Schema::getIndexListing('user_documents'), true);
    }
};
