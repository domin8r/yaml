<?php

namespace Test\Dallgoot\Yaml;

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Dallgoot\Yaml;
use Dallgoot\Yaml\YamlObject;
use Dallgoot\Yaml\NodeRoot;

/**
 * Class YamlTest.
 *
 * @author Stephane Rebai <stephane.rebai@gmail.com>.
 * @license https://opensource.org/licenses/MIT The MIT license.
 * @link https://github.com/john-doe/my-awesome-project
 * @since File available since Release 1.0.0
 *
 * @covers \Dallgoot\Yaml
 */
class YamlTest extends TestCase
{
    /**
     * @var Yaml $yaml An instance of "Yaml" to test.
     */
    private $yaml;

    /**
     * {@inheritdoc}
     */
    protected function setUp(): void
    {
        /** @todo Maybe add some arguments to this constructor */
        $this->yaml = new Yaml();
    }

    /**
     * @covers \Dallgoot\Yaml::isOneOf
     */
    public function testIsOneOf(): void
    {
        $this->assertTrue($this->yaml::isOneOf(new NodeRoot, ['NodeRoot']));
    }

    /**
     * @covers \Dallgoot\Yaml::parse
     */
    public function testParse(): void
    {
        $yaml = "- 1\n- 2\n- 3\n";
        $this->assertTrue($this->yaml::parse($yaml) instanceof YamlObject);
    }

    /**
     * @covers \Dallgoot\Yaml::parse
     */
    public function testParseException(): void
    {
        $obj =  [];
        $this->expectException(\Error::class);
        $this->yaml::parse($obj);
    }

    /**
     * @covers \Dallgoot\Yaml::parseFile
     */
    public function testParseFile(): void
    {
        $this->assertTrue($this->yaml::parseFile(__DIR__."/../definitions/parsing_tests.yml") instanceof YamlObject);
    }

    /**
     * @covers \Dallgoot\Yaml::parseFile
     */
    public function testParseFileException(): void
    {
        $this->expectException(\Exception::class);
        $this->yaml::parseFile('ssh:example.com');
    }

    /**
     * @covers \Dallgoot\Yaml::dump
     */
    public function testDump(): void
    {
        $this->markTestIncomplete();
        // $phpVar = [1,2,3];
        // $this->assertEquals("- 1\n- 2\n- 3\n", $this->yaml::dump($phpVar));
    }

    /**
     * @covers \Dallgoot\Yaml::dumpFile
     */
    public function testDumpFile(): void
    {
        $this->markTestIncomplete();
        // $phpVar = [1,2,3];
        // $fileName = 'dumpfile_test.yml';
        // $this->assertTrue($this->yaml::dumpFile($fileName, $phpVar));
        // $this->assertEquals("- 1\n- 2\n- 3\n", file_get_contents($fileName));
        // unlink($fileName);
    }
}
