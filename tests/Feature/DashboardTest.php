<?php

use Inertia\Testing\AssertableInertia;

it('renders the dashboard through Inertia', function () {
    $this->withoutVite()
        ->get('/app')
        ->assertOk()
        ->assertInertia(fn (AssertableInertia $page) => $page->component('Home', false));
});
