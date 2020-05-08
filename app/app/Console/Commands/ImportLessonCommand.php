<?php

namespace App\Console\Commands;

use App\Lib\FileParser;
use App\Lib\Markdown\MarkdownParser;
use Illuminate\Console\Command;

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

    /**
     * Create a new command instance.
     *
     * @param FileParser     $fileParser
     * @param MarkdownParser $markdownParser
     */
    public function __construct(
        FileParser $fileParser,
        MarkdownParser $markdownParser
    ) {
        parent::__construct();
        $this->fileParser = $fileParser;
        $this->markdownParser = $markdownParser;
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
        dd($result);
    }

    /**
     * @return string
     */
    protected function getFilename(): string
    {
        $filename = $this->argument('filename');
//        if (strpos('/', $filename) !== 0) {
//            $filename = __DIR__ . $filename;
//        } dd($filename);
        if (!file_exists($filename)) {
            throw new \RuntimeException(sprintf('File "%s" does not exist.', $filename));
        }
        return $filename;
    }
}
