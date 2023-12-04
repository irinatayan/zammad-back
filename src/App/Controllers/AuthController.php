<?php

declare(strict_types=1);

namespace App\Controllers;

use Framework\Response;
use Framework\TemplateEngine;
use App\Services\{ValidatorService, UserService};

class AuthController
{

    public function __construct(
        private readonly TemplateEngine $view,
        private readonly ValidatorService $validatorService,
        private UserService $userService
    ) {
    }

    public function registerView(): void
    {
        echo $this->view->render("register.php", ['title' => 'Register']);
    }

    public function register(): void
    {
        (new Response())->success([])->send();
//        $this->validatorService->validateRegister($_POST);
//        $this->userService->isEmailTaken($_POST['email']);
//        $this->userService->create($_POST);
//
//        redirectTo('/');
    }

    public function loginView(): void
    {
        echo $this->view->render("login.php", ['title' => 'Login']);
    }

    public function login(): void
    {
        $this->validatorService->validateLogin($_POST);
        $this->userService->login($_POST);
        redirectTo('/');
    }

    public function logout(): void
    {
        $this->userService->logout();
        redirectTo('/login');
    }
}