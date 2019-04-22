<?php

namespace Test\Dallgoot\Yaml;

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Dallgoot\Yaml\NodeList;
use Dallgoot\Yaml\Node;
use Dallgoot\Yaml\NodeBlank;
use Dallgoot\Yaml\NodeDocStart;
use Dallgoot\Yaml\NodeComment;
use Dallgoot\Yaml\NodeItem;
use Dallgoot\Yaml\NodeKey;
use Dallgoot\Yaml\NodeSetKey;
use Dallgoot\Yaml\NodeScalar;

/**
 * Class NodeListTest.
 *
 * @author Stephane Rebai <stephane.rebai@gmail.com>.
 * @license https://opensource.org/licenses/MIT The MIT license.
 * @link https://github.com/dallgoot/yaml
 * @since File available since Release 1.0.0
 *
 * @covers \Dallgoot\Yaml\NodeList
 */
class NodeListTest extends TestCase
{
    /**
     * @var NodeList $nodeList An instance of "NodeList" to test.
     */
    private $nodeList;

    /**
     * {@inheritdoc}
     */
    protected function setUp(): void
    {
        $this->nodeList = new NodeList(new NodeBlank('', 1));
    }

    /**
     * @covers \Dallgoot\Yaml\NodeList::__construct
     */
    public function testConstruct(): void
    {
        $this->assertTrue($this->nodeList->count() === 1);
    }

    /**
     * @covers \Dallgoot\Yaml\NodeList::has
     */
    public function testHas(): void
    {
        $this->assertTrue($this->nodeList->has('NodeBlank'));
        $this->assertFalse($this->nodeList->has('NodeItem'));
    }

    /**
     * @covers \Dallgoot\Yaml\NodeList::hasContent
     */
    public function testHasContent(): void
    {
        $this->assertFalse($this->nodeList->hasContent());
    }

   /**
     * @covers \Dallgoot\Yaml\NodeList::hasContent
     */
    public function testHasContentWithDocStart(): void
    {
        $docstartNode = new NodeDocStart('---  some value', 1);
        $this->nodeList->push($docstartNode);
        $this->assertTrue($this->nodeList->hasContent());
    }

    /**
     * @covers \Dallgoot\Yaml\NodeList::push
     */
    public function testPush(): void
    {
        $this->assertTrue(is_null($this->nodeList->type));
        $this->nodeList->push(new NodeItem('- item', 1));
        $this->assertEquals($this->nodeList->type, $this->nodeList::SEQUENCE);

        $this->nodeList = new NodeList;
        $this->nodeList->push(new NodeKey(' key: value', 1));
        $this->assertEquals($this->nodeList->type, $this->nodeList::MAPPING);

        $this->nodeList = new NodeList;
        $this->nodeList->push(new NodeSetKey(' ? simplekey  ', 1));
        $this->assertEquals($this->nodeList->type, $this->nodeList::SET);

        $this->nodeList = new NodeList;
        $this->nodeList->push(new NodeScalar('whatever string', 1));
        $this->assertEquals($this->nodeList->type, $this->nodeList::MULTILINE);
    }

    /**
     * @covers \Dallgoot\Yaml\NodeList::checkTypeCoherence
     */
    public function testCheckTypeCoherence(): void
    {
        $this->assertFalse($this->nodeList->checkTypeCoherence(null));
        $this->assertFalse($this->nodeList->checkTypeCoherence(0));
        $this->assertTrue($this->nodeList->checkTypeCoherence(4));
    }

    /**
     * @covers \Dallgoot\Yaml\NodeList::build
     * @depends testPush
     */
    public function testBuild(): void
    {
        $keyNode    = new NodeKey('key: keyvalue', 1);
        $itemNode   = new NodeItem(' - itemvalue', 1);
        $scalarNode = new NodeScalar('a string value', 1);
        //expect object
        $this->nodeList->push($keyNode);
        $this->assertTrue(is_object($this->nodeList->build()));
        // expect array
        $this->nodeList = new NodeList;
        $this->nodeList->push($itemNode);
        $this->assertTrue(is_array($this->nodeList->build()));
        // expect string
        $this->nodeList = new NodeList;
        $this->nodeList->push($scalarNode);
        $this->assertTrue(is_string($this->nodeList->build()));
    }

    /**
     * @covers \Dallgoot\Yaml\NodeList::buildList
     */
    public function testBuildList(): void
    {
        $arr = [];
        $this->assertEquals($arr, $this->nodeList->buildList($arr));
        $obj = new \StdClass;
        $this->assertEquals($obj, $this->nodeList->buildList($obj));
    }

    /**
     * @covers \Dallgoot\Yaml\NodeList::buildMultiline
     */
    public function testBuildMultiline(): void
    {
        //test when empty
        $this->assertEquals(1, $this->nodeList->count(), 'NodeList count is wrong (+1 Nodeblank on setUp)');
        $this->assertEquals('', $this->nodeList->buildMultiline(), 'buildMultiline did not return a string');
        //test with one child
        $this->nodeList->push(new NodeScalar('some string', 2));//var_dump($this->nodeList);
        $this->assertEquals(2, $this->nodeList->count(), 'NodeList does NOT contain 2 children');
        $this->assertEquals('some string', $this->nodeList->buildMultiline(), 'buildMultiline failed with 2 children');
        //test with one child AND one blank
        $this->nodeList->push(new NodeBlank('', 2));//var_dump($this->nodeList);
        $this->nodeList->push(new NodeScalar('other string', 3));//var_dump($this->nodeList);
        $this->assertEquals(4, $this->nodeList->count(), 'NodeList does NOT contain 2 children');
        $this->assertEquals("some string\nother string", $this->nodeList->buildMultiline(), 'buildMultiline failed with 2 children');
        //test with two child
        $this->nodeList->push(new NodeScalar('and some other string', 3));
        $this->assertEquals(5, $this->nodeList->count(), 'NodeList does NOT contain 3 nodes');
        $this->assertEquals("some string\nother string and some other string", $this->nodeList->buildMultiline(), "buildMultiline failed to provide correct string");
    }

    /**
     * @covers \Dallgoot\Yaml\NodeList::filterComment
     * @todo problem with building comments on YamlObject since theres no Root node and no Yaml object
     */
    public function testFilterComment(): void
    {
        $this->nodeList->push(new NodeComment('# this is a comment', 1));
        $this->assertEquals(2, $this->nodeList->count());
        $filtered = $this->nodeList->filterComment();
        $this->assertEquals(1, $filtered->count());
        $this->assertEquals(2, $this->nodeList->count());
    }
}
