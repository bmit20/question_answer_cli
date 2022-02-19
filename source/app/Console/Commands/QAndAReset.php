<?php

namespace App\Console\Commands;

use App\Question;
use Illuminate\Console\Command;

class QAndAReset extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'qanda:reset';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'removes all previous progresses';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        Question::orWhere([
            'answered' => 1,
            'is_correct' => 1,
        ])->update([
            'answered' => 0,
            'is_correct' => 0,
        ]);

        $this->info("All previous progresses has been removed");
    }
}
