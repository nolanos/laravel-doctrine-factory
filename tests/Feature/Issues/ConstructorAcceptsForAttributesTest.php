<?php

namespace Tests\Feature\Issues;

use Workbench\App\Entities\Comment;
use Workbench\App\Entities\Post;

it("entities defined by BelongsTo relationship should get passed to constructor", function () {
    $comment = Comment::factory()
        ->for(Post::factory())
        ->make([
            "body" => "Blah",
        ]);

    expect($comment)->body->toBe("Blah");
    expect($comment)->post->toBeInstanceOf(Post::class);
})->wip(issue: 12);