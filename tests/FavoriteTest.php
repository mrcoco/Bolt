<?php


class FavoriteTest extends TestCase
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

    public function testFavorit()
    {
        // test the video that is being liked
        $this->createTTModels();

        $user = Bolt\User::find(1);
        $video = Bolt\Video::find(1);
        $favorite = $this->createFavoriteFor($video);
        $favVid = $favorite->favoritable;
        $this->assertEquals($video, $favVid);
    }

    public function testFavoriter()
    {
        // test the person that liked a video
        $this->createTTModels();

        $user = Bolt\User::find(1);
        $video = Bolt\Video::find(1);
        $favorite = $this->createFavoriteFor($video);
        $favOwner = $favorite->favoriter();
        $this->assertEquals($user, $favOwner);
    }
}
