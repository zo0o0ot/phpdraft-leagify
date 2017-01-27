<?php
namespace PhpDraft\Domain\Validators;

use \Silex\Application;
use Symfony\Component\HttpFoundation\Request;
use PhpDraft\Domain\Entities\LoginUser;
use PhpDraft\Domain\Entities\Draft;
use PhpDraft\Domain\Models\PhpDraftResponse;
use Symfony\Component\Security\Core\Util\StringUtils;
use Egulias\EmailValidator\EmailValidator;

class DraftValidator {
  private $app;

  public function __construct(Application $app) {
    $this->app = $app;
  }

  public function IsDraftViewableForUser($draft_id, Request $request) {
    $draft = $this->app['phpdraft.DraftRepository']->Load($draft_id);
    $current_user = $this->app['phpdraft.LoginUserService']->GetUserFromHeaderToken($request);
    $draft_password = $request->headers->get(DRAFT_PASSWORD_HEADER, '');

    if($current_user != null && ($draft->commish_id == $current_user->id || $this->app['phpdraft.LoginUserService']->CurrentUserIsAdmin($current_user))) {
      return true;
    }

    if(empty($draft->draft_password) || ($draft->draft_password == $draft_password)) {
      return true;
    }

    return false;
  }

  public function IsDraftEditableForUser(Draft $draft, LoginUser $current_user) {
    if(!empty($current_user) && !empty($draft) && $draft->commish_id == $current_user->id) {
      return true;
    }

    if(!empty($current_user) && $this->app['phpdraft.LoginUserService']->CurrentUserIsAdmin($current_user)) {
      return true;
    }

    return false;
  }

  public function IsDraftValidForCreateAndUpdate(Draft $draft) {
    $valid = true;
    $errors = array();
    $draft_sports = $this->app['phpdraft.DraftDataRepository']->GetSports();
    $draft_styles = $this->app['phpdraft.DraftDataRepository']->GetStyles();

    if(empty($draft->commish_id)
      || empty($draft->draft_name)
      || empty($draft->draft_sport)
      || empty($draft->draft_style)) {
      $errors[] = "One or more missing fields.";
      $valid = false;
    }

    if(!empty($draft->draft_password) && strlen($draft->draft_password) > 255) {
      $errors[] = "Password is above maximum length.";
      $valid = false;
    }

    if(strlen($draft->draft_name) > 255) {
      $errors[] = "Draft name is above maximum length";
      $valid = false;
    }

    if(strlen($draft->draft_sport) < 3 || strlen($draft->draft_sport) > 4 || strlen($draft_sports[$draft->draft_sport]) == 0) {
      $errors[] = "Draft sport is an invalid value.";
      $valid = false;
    }

    if(!array_key_exists($draft->draft_style, $draft_styles)) {

      $errors[] = "Draft style is an invalid value.";
      $valid = false;
    }

    if(!$this->app['phpdraft.DraftRepository']->NameIsUnique($draft->draft_name, $draft->draft_id)) {
      $errors[] = "Draft name is already taken, please choose a different name.";
      $valid = false;
    }

    if($draft->draft_rounds < 1 || $draft->draft_rounds > 30) {
      $errors[] = "Invalid number of draft rounds.";
      $valid = false;
    }

    return new PhpDraftResponse($valid, $errors);
  }

  public function IsDraftStatusValid(Draft $draft, $old_status) {
    $valid = true;
    $errors = array();
    $draft_statuses = $this->app['phpdraft.DraftDataRepository']->GetStatuses();

    if($old_status == "complete") {
      $valid = false;
      $errors[] = "The draft is completed, therefore its status cannot be changed.";
    }

    if($draft->draft_status == "complete") {
      $valid = false;
      $errors[] = "You cannot set the draft as completed manually.";
    }

    if(empty($draft->draft_status)) {
      $errors[] = "One or more missing fields.";
      $valid = false;
    }

    if(!array_key_exists($draft->draft_status, $draft_statuses)) {
      $errors[] = "Draft status is an invalid value.";
      $valid = false;
    }

    if($draft->draft_status == "in_progress" && !$this->app['phpdraft.ManagerRepository']->DraftHasManagers($draft->draft_id)) {
      $valid = false;
      $errors[] = "A draft must have at least 2 managers before it can begin.";
    }

    return new PhpDraftResponse($valid, $errors);
  }

  public function IsDraftSettingUp(Draft $draft) {
    $valid = true;
    $errors = array();
    $draft_statuses = $this->app['phpdraft.DraftDataRepository']->GetStatuses();
    $current_status_text = strtolower($draft_statuses[$draft->draft_status]);
    $is_setting_up = $this->app['phpdraft.DraftService']->DraftSettingUp($draft);

    if(!$is_setting_up) {
      $valid = false;
      $errors[] = "Unable to work on draft #$draft->draft_id: draft is $current_status_text";
    }

    return new PhpDraftResponse($valid, $errors);
  }

  public function IsDraftSettingUpOrInProgress(Draft $draft) {
    $valid = true;
    $errors = array();
    $draft_statuses = $this->app['phpdraft.DraftDataRepository']->GetStatuses();
    $current_status_text = strtolower($draft_statuses[$draft->draft_status]);
    $is_setting_up = $this->app['phpdraft.DraftService']->DraftSettingUp($draft);
    $is_in_progress = $this->app['phpdraft.DraftService']->DraftInProgress($draft);

    if(!$is_setting_up && !$is_in_progress) {
      $valid = false;
      $errors[] = "Unable to work on draft #$draft->draft_id: draft is $current_status_text";
    }

    return new PhpDraftResponse($valid, $errors);
  }

  public function IsDraftInProgress(Draft $draft) {
    $valid = true;
    $errors = array();
    $draft_statuses = $this->app['phpdraft.DraftDataRepository']->GetStatuses();
    $current_status_text = strtolower($draft_statuses[$draft->draft_status]);
    $is_in_progress = $this->app['phpdraft.DraftService']->DraftInProgress($draft);

    if(!$is_in_progress) {
      $valid = false;
      $errors[] = "Unable to work on draft #$draft->draft_id: draft is $current_status_text";
    }

    return new PhpDraftResponse($valid, $errors);
  }

  public function IsDraftInProgressOrComplete(Draft $draft) {
    $valid = true;
    $errors = array();
    $draft_statuses = $this->app['phpdraft.DraftDataRepository']->GetStatuses();
    $current_status_text = strtolower($draft_statuses[$draft->draft_status]);
    $is_in_progress = $this->app['phpdraft.DraftService']->DraftInProgress($draft);
    $is_complete = $this->app['phpdraft.DraftService']->DraftComplete($draft);

    if(!$is_complete && !$is_in_progress) {
      $valid = false;
      $errors[] = "Unable to work on draft #$draft->draft_id: draft is $current_status_text";
    }

    return new PhpDraftResponse($valid, $errors);
  }

  public function IsDraftInProgressOrCompletedInLessThanTen(Draft $draft) {
    $valid = true;
    $errors = array();
    $draft_statuses = $this->app['phpdraft.DraftDataRepository']->GetStatuses();
    $current_status_text = strtolower($draft_statuses[$draft->draft_status]);
    $is_in_progress = $this->app['phpdraft.DraftService']->DraftInProgress($draft);
    $is_complete = $this->app['phpdraft.DraftService']->DraftComplete($draft);
    $isLessThanTenMinutes = false;

    if($is_complete) {
      $utc_timezone = new \DateTimeZone("UTC");
      $now_utc = new \DateTime(null, $utc_timezone);
      $end_time = new \DateTime($draft->draft_end_time, $utc_timezone);
      $now_seconds = $now_utc->getTimestamp();
      $end_seconds = $end_time->getTimestamp();
      $seconds_elapsed = $end_seconds - $now_seconds;

      if($seconds_elapsed < 600) {
        $isLessThanTenMinutes = true;
      }
    }

    if(!$is_complete && !$is_in_progress) {
      $valid = false;
      $errors[] = "Unable to work on draft #$draft->draft_id: draft is $current_status_text";
    }

    if($is_complete && !$isLessThanTenMinutes) {
      $valid = false;
      $errors[] = "Unable to work on draft #$draft->draft_id: draft is complete and beyond the ten minute grace period.";
    }

    return new PhpDraftResponse($valid, $errors);
  }

  public function IsDraftComplete(Draft $draft) {
    $valid = true;
    $errors = array();
    $draft_statuses = $this->app['phpdraft.DraftDataRepository']->GetStatuses();
    $current_status_text = strtolower($draft_statuses[$draft->draft_status]);
    $is_complete = $this->app['phpdraft.DraftService']->DraftComplete($draft);

    if(!$is_complete) {
      $valid = false;
      $errors[] = "Unable to work on draft #$draft->draft_id: draft is $current_status_text";
    }

    return new PhpDraftResponse($valid, $errors);
  }
}