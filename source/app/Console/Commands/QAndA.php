<?php

namespace App\Console\Commands;

use App\Answer;
use App\Question;
use Illuminate\Console\Command;
use PhpParser\Node\Stmt\Break_;

class QAndA extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'qanda:interactive';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Runs an interactive command line based Q And A system.';

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
     * @return mixed
     */
    public function handle()
    {
        do {
            $choice0 = $this->choice(
                'Please choose option in menu',
                ['Create a question','List all questions', 'Practice','Stats', 'Reset', 'exit']
            );

            switch ($choice0) {
                case 'Create a question':
                    $this->addQuestions();
                    break;
                case 'List all questions':
                    $this->listAllQuestions();
                    break;
                case 'Practice':
                    $this->practiceQuestions();
                    break;
                case 'Stats';
                    $this->Stats();
                    break;
                case 'Reset':
                    $this->call('qanda:reset');

                    break;
                default:
                    break;
            }
        } while ($choice0 != 'exit');
    }

    public function addQuestions()
    {
        do {
            //validation
            do {
                $questionContent = $this->ask('Write the question');
            } while ($questionContent == '');
            do {
                $answerContent = $this->ask('Write the answer');
            } while ($answerContent == '');

            //save in db
            $question = new Question;
            $question->question = $questionContent;
            $question->answer = $answerContent;
            $question->save();


            //ask if he want to continue
            $moreQuestions = $this->confirm('Do you want to add another question?');

        } while ($moreQuestions == 'yes');
    }
    public function practiceQuestions()
    {
        $choice1 = null;
        //get list of questions
        $questions = Question::get();
        if (empty($questions->count())) {
            $this->error('No Questions Yet !');
            $choice1 = '<- Back';
        }
        while ($choice1 != '<- Back') {
            //counting answered questions to fill the progress
            $answeredQuestionsCount = $questions->filter(fn($q) => $q->answered)->count();

            $this->showProgress($questions->count(), $answeredQuestionsCount);

            $this->showFinalProgress($questions, $answeredQuestionsCount);

            $choice1 = $this->choice(
                'Please choose a question to practice',
                [...$questions->pluck('question')->toArray(), '<- Back']
            );

            if ($choice1 != '<- Back') {
                //save user answer
                $userQuestionAnswer = $this->ask($choice1);
                $rightQuestionAnswer = $questions->filter(fn($q) => $q->question == $choice1)->pop();

                //save that the user has answered the question
                $rightQuestionAnswer->answered = 1;

                //increment score if correct
                if ($userQuestionAnswer === $rightQuestionAnswer->answer) {
                    $rightQuestionAnswer->is_correct = 1;
                }
                $rightQuestionAnswer->save();
                $questions->fresh();
            }

        }
    }

    public function listAllQuestions()
    {
        $choice1 = null;
        $questions = Question::get();
        if (empty($questions->count())) {
            $this->error('No Questions Yet !');
            $choice1 = '<- Back';
        }
        else{
                $this->table(
                    ['question', 'correct'],
                    Question::all(['question', 'is_correct'])->toArray()
            );
        }

    }

    public function Stats()
    {
        $questions = Question::get();
        $answeredQuestionsCount = $questions->filter(fn($q) => $q->answered)->count();

        $this->showProgress($questions->count(), $answeredQuestionsCount);

        $this->showFinalProgress($questions, $answeredQuestionsCount);
    }

    public function showProgress(int $count = 0, int $answeredQuestionsCount = 0)
    {
        $this->info('Your Current Progress : ');
        $this->newLine();
        $bar = $this->output->createProgressBar($count);
        $bar->start();
        //wait 1 second for the progress bar to be filled
        if ($answeredQuestionsCount > 0) {
            sleep(1);
            $bar->advance($answeredQuestionsCount);
        }

        $this->newLine(2);
    }

    public function showFinalProgress(iterable $questions, int $answeredQuestionsCount = 0)
    {
        //check if he completed the questions
        if ($answeredQuestionsCount == $questions->count()) {
            $correctQuestionsCount = $questions->filter(fn($q) => $q->is_correct)->count();
            $this->info('And Your final Progress : ' . $correctQuestionsCount . " / " . $questions->count());
            $this->newLine();
        }
    }
}
