<?php

it('can get health checks', function () {
    $this->get('/status/health')->assertOk();
});
