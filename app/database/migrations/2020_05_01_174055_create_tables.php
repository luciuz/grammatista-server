<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTables extends Migration
{
    private const TABLE_USER      = 'user';
    private const TABLE_LESSON    = 'lesson';
    private const TABLE_TEST     = 'test';
    private const TABLE_VARIANT  = 'variant';
    private const TABLE_BOOKMARK = 'bookmark';

    private const FK_LESSON_USER      = 'fk_lesson_user';
    private const FK_TEST_LESSON      = 'fk_test_lesson';
    private const FK_USER_TEST_LESSON = 'fk_user_test_lesson';
    private const FK_USER_TEST_TEST   = 'fk_user_test_test';
    private const FK_USER_TEST_USER   = 'fk_user_test_user';
    private const FK_BOOKMARK_USER    = 'fk_bookmark_user';
    private const FK_BOOKMARK_LESSON  = 'fk_bookmark_lesson';

    private const EN = 'en';
    private const RU = 'ru';

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(): void
    {
        $this->createUser();
        $this->createLesson();
        $this->createTest();
        $this->createVariant();
        $this->createBookmark();
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        Schema::dropIfExists(self::TABLE_BOOKMARK);
        Schema::dropIfExists(self::TABLE_VARIANT);
        Schema::dropIfExists(self::TABLE_TEST);
        Schema::dropIfExists(self::TABLE_LESSON);
        Schema::dropIfExists(self::TABLE_USER);
    }

    private function createUser(): void
    {
        Schema::create(self::TABLE_USER, static function (Blueprint $table) {
            $table->id();
            $table->boolean('is_active');
            $table->unsignedBigInteger('vk_id');
            $table->timestamps();
        });
    }

    private function createLesson(): void
    {
        Schema::create(self::TABLE_LESSON, function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->enum('locale', $this->getLocales());
            $table->text('title');
            $table->jsonb('body');
            $table->timestamps();
            $table->timestamp('published_at')->nullable();
            $table->timestamp('deleted_at')->nullable();

            $table->foreign('user_id', self::FK_LESSON_USER)
                ->references('id')->on(self::TABLE_USER);
        });
    }

    private function createTest(): void
    {
        Schema::create(self::TABLE_TEST, function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('lesson_id');
            $table->enum('locale', $this->getLocales());
            $table->jsonb('question');
            $table->jsonb('answer');
            $table->unsignedInteger('duration');
            $table->timestamps();
            $table->timestamp('deleted_at')->nullable();

            $table->foreign('lesson_id', self::FK_TEST_LESSON)
                ->references('id')->on(self::TABLE_LESSON);
        });
    }

    private function createVariant(): void
    {
        Schema::create(self::TABLE_VARIANT, static function (Blueprint $table) {
            $table->id();
            $table->boolean('is_complete');
            $table->unsignedBigInteger('lesson_id');
            $table->unsignedBigInteger('test_id');
            $table->unsignedBigInteger('user_id');
            $table->jsonb('question');
            $table->jsonb('answer');
            $table->timestamps();
            $table->timestamp('expired_at')->nullable();
            $table->timestamp('finished_at')->nullable();
            $table->timestamp('deleted_at')->nullable();

            $table->foreign('lesson_id', self::FK_USER_TEST_LESSON)
                ->references('id')->on(self::TABLE_LESSON);
            $table->foreign('test_id', self::FK_USER_TEST_TEST)
                ->references('id')->on(self::TABLE_TEST);
            $table->foreign('user_id', self::FK_USER_TEST_USER)
                ->references('id')->on(self::TABLE_USER);
        });
    }

    private function createBookmark(): void
    {
        Schema::create(self::TABLE_BOOKMARK, static function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('lesson_id');
            $table->timestamp('created_at')->nullable();

            $table->foreign('user_id', self::FK_BOOKMARK_USER)
                ->references('id')->on(self::TABLE_USER);
            $table->foreign('lesson_id', self::FK_BOOKMARK_LESSON)
                ->references('id')->on(self::TABLE_LESSON);
        });
    }

    /**
     * @return array|string[]
     */
    private function getLocales(): array
    {
        return [self::RU, self::EN];
    }
}
