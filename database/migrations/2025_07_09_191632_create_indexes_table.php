<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        // Индексы для таблицы sentences
        $this->safeCreateIndex('sentences', ['locked_at'], 'sentences_locked_at_index');
        $this->safeCreateIndex('sentences', ['delayed_until'], 'sentences_delayed_until_index');
        $this->safeCreateIndex('sentences', ['complexity'], 'sentences_complexity_index');
        $this->safeCreateFullTextIndex('sentences', ['sentence'], 'sentences_sentence_fulltext');

        // Индексы для таблицы translations
        $this->safeCreateIndex('translations', ['sentence_id', 'region_id'], 'translations_sentence_id_region_id_index');
        $this->safeCreateIndex('translations', ['translator_id', 'status'], 'translations_translator_id_status_index');
        $this->safeCreateIndex('translations', ['proofreader_id', 'status'], 'translations_proofreader_id_status_index');
        $this->safeCreateIndex('translations', ['region_id', 'status'], 'translations_region_id_status_index');
        $this->safeCreateIndex('translations', ['status', 'assigned_at'], 'translations_status_assigned_at_index');

        // Индексы для таблицы users
        $this->safeCreateIndex('users', ['region_id', 'role'], 'users_region_id_role_index');
    }

    public function down()
    {
        // Удаление индексов для sentences
        $this->safeDropIndex('sentences', 'sentences_locked_at_index');
        $this->safeDropIndex('sentences', 'sentences_delayed_until_index');
        $this->safeDropIndex('sentences', 'sentences_complexity_index');
        $this->safeDropFullTextIndex('sentences', 'sentences_sentence_fulltext');

        // Удаление индексов для translations
        $this->safeDropIndex('translations', 'translations_sentence_id_region_id_index');
        $this->safeDropIndex('translations', 'translations_translator_id_status_index');
        $this->safeDropIndex('translations', 'translations_proofreader_id_status_index');
        $this->safeDropIndex('translations', 'translations_region_id_status_index');
        $this->safeDropIndex('translations', 'translations_status_assigned_at_index');

        // Удаление индексов для users
        $this->safeDropIndex('users', 'users_region_id_role_index');
    }

    /**
     * Безопасное создание индекса
     */
    protected function safeCreateIndex(string $table, array $columns, string $indexName)
    {
        if (!$this->indexExists($table, $indexName)) {
            Schema::table($table, function (Blueprint $table) use ($columns, $indexName) {
                $table->index($columns, $indexName);
            });
        }
    }

    /**
     * Безопасное создание полнотекстового индекса
     */
    protected function safeCreateFullTextIndex(string $table, array $columns, string $indexName)
    {
        if (!$this->indexExists($table, $indexName)) {
            Schema::table($table, function (Blueprint $table) use ($columns, $indexName) {
                $table->fullText($columns, $indexName);
            });
        }
    }

    /**
     * Безопасное удаление индекса
     */
    protected function safeDropIndex(string $table, string $indexName)
    {
        if ($this->indexExists($table, $indexName)) {
            Schema::table($table, function (Blueprint $table) use ($indexName) {
                $table->dropIndex($indexName);
            });
        }
    }

    /**
     * Безопасное удаление полнотекстового индекса
     */
    protected function safeDropFullTextIndex(string $table, string $indexName)
    {
        if ($this->indexExists($table, $indexName)) {
            Schema::table($table, function (Blueprint $table) use ($indexName) {
                $table->dropFullText($indexName);
            });
        }
    }

    /**
     * Проверяет существование индекса
     */
    protected function indexExists(string $table, string $indexName): bool
    {
        $connection = DB::connection();
        $schema = $connection->getSchemaBuilder();

        try {
            // Для PostgreSQL
            if ($connection->getDriverName() === 'pgsql') {
                $result = $connection->select(
                    "SELECT 1 FROM pg_indexes WHERE tablename = ? AND indexname = ?",
                    [$table, $indexName]
                );
                return count($result) > 0;
            }

            // Для MySQL
            if ($connection->getDriverName() === 'mysql') {
                $result = $connection->select(
                    "SHOW INDEX FROM `{$table}` WHERE Key_name = ?",
                    [$indexName]
                );
                return count($result) > 0;
            }

            // Общий вариант (может не работать для всех СУБД)
            return $schema->hasIndex($table, $indexName);

        } catch (\Exception $e) {
            return false;
        }
    }
};
