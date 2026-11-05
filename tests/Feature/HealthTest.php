<?php

it('reports healthy on /up', function () {
    $this->get('/up')->assertOk();
});
