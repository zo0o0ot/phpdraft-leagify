<?php
namespace PhpDraft\Domain\Validators;

use \Silex\Application;
use Symfony\Component\HttpFoundation\Request;
use PhpDraft\Domain\Entities\Draft;
use PhpDraft\Domain\Entities\RoundTime;
use PhpDraft\Domain\Models\PhpDraftResponse;
use PhpDraft\Domain\Models\RoundTimeCreateModel;

class RoundTimeValidator {
  private $app;

  public function __construct(Application $app) {
    $this->app = $app;
  }

  public function AreRoundTimesValid(RoundTimeCreateModel $model) {
    $valid = true;
    $errors = array();

    if($model->isRoundTimesEnabled) {
      $roundTimeNumber = 0;
      foreach($model->roundTimes as $roundTime) {
        $roundTimeNumber++;

        if(empty($roundTime->draft_id) ||
          empty($roundTime->round_time_seconds)) {
          $errors[] = "Round time #$roundTimeNumber has one or more missing fields.";
          $valid = false;
        }

        if($roundTime->round_time_seconds <= 0) {
          $errors[] = "Round time #$roundTimeNumber must have 1 or more seconds specified.";
          $valid = false;
        }

        if(!$roundTime->is_static_time && ($roundTime->draft_round < 1 || $roundTime->draft_round > 30)) {
          $errors[] = "Round time #$roundTimeNumber cannot have a round less than 1 or greater than 30.";
          $valid = false;
        }
      }
    }

    return new PhpDraftResponse($valid, $errors);
  }
}