<?php

namespace PhpDraft\Controllers\Commish;

use \Silex\Application;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use PhpDraft\Domain\Models\UserProfile;
use PhpDraft\Domain\Models\PhpDraftResponse;

class UserProfileController {
  public function Get(Application $app, Request $request) {
    $user = $app['phpdraft.LoginUserService']->GetCurrentUser();

    if(empty($user)) {
      return $app->json(new PhpDraftResponse(false));
    }

    return $app->json(new UserProfile($user->id, $user->email, $user->name), Response::HTTP_OK);
  }

  public function Put(Application $app, Request $request) {
    $validity = $app['phpdraft.LoginUserValidator']->IsUserProfileUpdateValid($request);

    if(!$validity->success) {
      return $app->json($validity, Response::HTTP_BAD_REQUEST);
    }

    $response = $app['phpdraft.LoginUserService']->UpdateUserProfile($request);

    return $app->json($response, $response->responseType());
  }
}