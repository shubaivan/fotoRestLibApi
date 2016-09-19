<?php

namespace AppBundle\Tests\Unit\Entity;

use AppBundle\Entity\Tags;
use AppBundle\Tests\Unit\ObjectTestCase;

class TagsTest extends ObjectTestCase
{
    public function testId()
    {
        $obj = new Tags();
        $this->assertGetter($obj, 'id', null, 10);
    }

    public function testTag()
    {
        $obj = new Tags();
        $this->assertBoth($obj, 'tag', null, 'test-string');
    }
}
