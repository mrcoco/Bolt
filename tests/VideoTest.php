<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class VideoTest extends TestCase
{
    /**
     * A basic test example.
     *
     * @return void
     */
    public function testExample()
    {
        $this->assertTrue(true);
    }

    public function testVideoIndex()
    {
    	$category = $this->createCategory();
    	$this->visit('/videos')
    		->seePageIs('videos');
    		// ->see('MsDotNet');
    }

    public function testVideoShowWithoutAuthUser()
    {
    	$this->createVideo();
    	$this->visit('videos/1')
                // ->see('roy')
    			// ->see('A Introduction to MsDotNet')
    			;	
        // $this->countElements('.video-user', 1);
    }

    public function testAddVideoLinkFailsForNoAuth()
    {
        $page = $this->visit('videos/add')
                    ->seePageIs('login')
                    ;
    }

    public function testAddVideoSucceeds()
    {
        $user = $this->createUser();
        $category = $this->createCategory();
        $page = $this->actingAs($user)
                    ->visit('videos/add')
                    ->seePageIs('videos/add')
                    ->type('A new title', 'title')
                    ->type('https://www.youtube.com/watch?v=3oT9PQcFZKc', 'url')
                    ->type('A new description', 'description')
                    ->select(1, 'category_id')
                    ->press('Add')
                    ->seePageIs('dashboard')
                    // ->see('A new title')
                    ;
    }

    public function testAddVideoValidatorFails()
    {
        $user = $this->createUser();
        $category = $this->createCategory();
        $page = $this->actingAs($user)
                    ->visit('videos/add')
                    ->seePageIs('videos/add')
                    ->type('', 'title')
                    ->type('', 'url')
                    ->type('', 'description')
                    ->select('', 'category_id')
                    ->press('Add')
                    ->seePageIs('videos/add')
                    // ->see('A new title')
                    ;
    }

    public function testVideoEditPage()
    {
        $this->createTTModels();

        $user = Bolt\User::find(1);
        $page = $this->actingAs($user)
                ->visit('videos/1/edit')
                ->see('A Introduction to MsDotNet')
                ->see('This is an introduction to the Microsoft DotNet Framework. It is very powerful.')
                ->type('This is the updated description.', 'description')
                ->see('Save')
                ->press('Save')
                ;
        // $video = Bolt\Video::find(1);
        // $this->assertEquals('This is the updated description.', $video->description);
    }

    public function testEditVideoLinkFailsForNoAuth()
    {
        $page = $this->visit('videos/1/edit')
                    ->seePageIs('login')
                    ;
    }

    public function testEditVideoLinkFailsForWrongOwner()
    {
    	$this->createTTModels();

    	factory(Bolt\User::class)->create([
            'id'   => 100,
        ]);

        $user = Bolt\User::find(100);

        $page = $this->actingAs($user)
        			->visit('videos/1/edit')
                    ->seePageIs('dashboard')
                    ;
    }

    public function testVideoDelete()
    {
        $this->createTTModels();

        $user = Bolt\User::find(1);
        $page = $this->actingAs($user)
                ->visit('videos/1/edit')
                ->press('Delete')
                // ->seePageIs('dashboard')
                // ->see('Video Deleted')
                ;

        // $this->assertSessionHas('success', 'Please Login.');
        $this->countElements('.delete_video', 0);
        $video = Bolt\Video::find(1);
        $this->assertEquals(null, $video);
    }
}