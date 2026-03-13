<?php

it('products module scaffold exists', function () {
    expect(is_dir(base_path('modules/Products')))->toBeTrue();
});
