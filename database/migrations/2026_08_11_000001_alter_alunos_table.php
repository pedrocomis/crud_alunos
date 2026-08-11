<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('alunos', function (Blueprint $table) {
            if (Schema::hasColumn('alunos', 'name')) {
                $table->renameColumn('name', 'nome');
            }

            if (! Schema::hasColumn('alunos', 'nome')) {
                $table->string('nome')->after('id');
            }

            if (Schema::hasColumn('alunos', 'curso')) {
                $table->dropColumn('curso');
            }

            if (! Schema::hasColumn('alunos', 'email')) {
                $table->string('email')->unique()->after('nome');
            }
        });

        Schema::table('alunos', function (Blueprint $table) {
            $table->string('email')->unique()->change();
            $table->string('nome')->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('alunos', function (Blueprint $table) {
            $table->dropUnique(['email']);
        });

        if (Schema::hasColumn('alunos', 'nome')) {
            $table = Schema::getConnection()->getSchemaBuilder();
            if ($table->hasColumn('alunos', 'nome') && ! $table->hasColumn('alunos', 'name')) {
                DB::statement('ALTER TABLE alunos RENAME COLUMN nome TO name');
            }
        }
    }
};
