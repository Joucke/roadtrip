<?php

test('guests cannot search the geocode api', function () {
    $this->getJson('/geocode-search')
        ->assertUnauthorized();
});

test('guests cannot reverse-search the geocode api', function () {
    $this->getJson('/geocode-reverse')
        ->assertUnauthorized();
});

test('users can search the geocode api', function () {
    // mock http facade
    Http::fake([
        'locationiq.com/v1/*' => Http::response([
            'place_id' => '1234',
            'lat' => '14',
            'lng' => '17',
            'display_name' => 'foobar restaurant',
        ])
    ]);

    $this->actingAs(App\Models\User::factory()->create())
        ->get('/geocode-search?q=foobar')
        ->assertOk()
        ->assertJson([
            'place_id' => 1234,
        ]);

    Http::assertSent(function ($request) {
        return Str::contains($request->url(), 'q=foobar');
    });
});

test('users can reverse-search the geocode api', function () {
    // mock http facade
    Http::fake([
        'locationiq.com/v1/*' => Http::response([
            'place_id' => '1234',
            'lat' => '14',
            'lng' => '17',
            'display_name' => 'foobar restaurant',
        ])
    ]);

    $this->actingAs(App\Models\User::factory()->create())
        ->get('/geocode-reverse?lat=14&lng=17')
        ->assertOk()
        ->assertJson([
            'place_id' => 1234,
        ]);

    Http::assertSent(function ($request) {
        return Str::contains($request->url(), 'lat=14&lon=17');
    });
});
