<?php

namespace AppBundle\Tests\Unit\Entity;

use AppBundle\Entity\Photo;
use AppBundle\Tests\Unit\ObjectTestCase;

class PhotoTest extends ObjectTestCase
{
    public function testId()
    {
        $obj = new Photo();
        $this->assertGetter($obj, 'id', null, 10);
    }

    public function testTag()
    {
        $obj = new Photo();
        $this->assertBoth($obj, 'filePath', null, 'test-string');
    }
}
