<?php

use Cachet\Cachet;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request;
use Workbench\Database\Factories\UserFactory;

it('can get the current version of Cachet', function () {
    expect(Cachet::version())->toBeString()->toBe('3.x-dev');
});

it('can retrieve the user from auth resolver', function () {
    $user = UserFactory::new()->create();

    Auth::loginUsingId($user->id);
    $request = Request::create('/', 'GET');

    expect($user)->is(Cachet::user());
    expect(Cachet::user($request))->toBeNull();
});
