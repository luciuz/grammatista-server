<?php

namespace App\Console\Commands;

use App\DataAssemblers\ImportLessonDataAssembler;
use App\Lib\FileParser;
use App\Lib\Markdown\MarkdownParser;
use App\Repositories\LessonRepository;
use App\Repositories\TestRepository;
use App\Validators\ImportLessonValidator;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Validation\ValidationException;

/**
 * Class ImportLessonCommand
 * @package App\Console\Commands
 */
class ImportLessonCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'import:lesson {filename}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Import lesson';

    /** @var FileParser */
    private $fileParser;

    /** @var MarkdownParser */
    private $markdownParser;

    /** @var ImportLessonDataAssembler */
    private $dataAssembler;

    /** @var ImportLessonValidator */
    private $importLessonValidator;

    /** @var LessonRepository */
    private $lessonRepository;

    /** @var TestRepository */
    private $testRepository;

    /**
     * Create a new command instance.
     *
     * @param FileParser                $fileParser
     * @param MarkdownParser            $markdownParser
     * @param ImportLessonDataAssembler $dataAssembler
     * @param ImportLessonValidator     $importLessonValidator
     * @param LessonRepository          $lessonRepository
     * @param TestRepository            $testRepository
     */
    public function __construct(
        FileParser $fileParser,
        MarkdownParser $markdownParser,
        ImportLessonDataAssembler $dataAssembler,
        ImportLessonValidator $importLessonValidator,
        LessonRepository $lessonRepository,
        TestRepository $testRepository
    ) {
        parent::__construct();
        $this->fileParser = $fileParser;
        $this->markdownParser = $markdownParser;
        $this->dataAssembler = $dataAssembler;
        $this->importLessonValidator = $importLessonValidator;
        $this->lessonRepository = $lessonRepository;
        $this->testRepository = $testRepository;
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $filename = $this->getFilename();
        $this->markdownParser->init();
        $this->fileParser->init($filename, [$this->markdownParser, 'parseLine']);
        $this->fileParser->run();
        $this->markdownParser->pickSubResult();
        $result = $this->markdownParser->getResult();

        $data = $this->dataAssembler->make($result);
        try {
            $this->importLessonValidator->validate($data);
        } catch (ValidationException $e) {
            $this->error($e->getMessage() . ' ' . print_r($e->errors(), 1));
            return 1;
        }

        $params = [
            'title'        => $data['options']['TITLE'],
            'locale'       => $data['options']['LOCALE'],
            'body'         => $data['lesson'],
        ];
        if ($data['options']['PUBLISHED_AT']) {
            $params['published_at'] = Carbon::createFromFormat('Y-m-d\TH:i:sP', $data['options']['PUBLISHED_AT']);
        }
        $lesson = $this->lessonRepository->create($params);

        $testParams = [
            'lesson_id' => $lesson->id,
            'question'  => $data['test'],
            'answer'    => $data['answer'],
            'duration'  => $data['options']['TEST_DURATION'],
        ];
        $this->testRepository->create($testParams);

        $this->info(sprintf('Lesson created. Id %d', $lesson->id));
        return 0;
    }

    /**
     * @return string
     */
    protected function getFilename(): string
    {
        $filename = $this->argument('filename');
        if (!file_exists($filename)) {
            throw new \RuntimeException(sprintf('File "%s" does not exist.', $filename));
        }
        return $filename;
    }
}
