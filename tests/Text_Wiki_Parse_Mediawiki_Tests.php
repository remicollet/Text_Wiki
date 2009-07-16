<?php

require_once 'PHPUnit/Framework.php';
require_once 'Text/Wiki/Mediawiki.php';
require_once 'Text/Wiki/Parse/Mediawiki/Break.php';
require_once 'Text/Wiki/Parse/Mediawiki/Code.php';
require_once 'Text/Wiki/Parse/Mediawiki/Comment.php';
require_once 'Text/Wiki/Parse/Mediawiki/Deflist.php';
require_once 'Text/Wiki/Parse/Mediawiki/Emphasis.php';
require_once 'Text/Wiki/Parse/Mediawiki/Heading.php';
require_once 'Text/Wiki/Parse/Mediawiki/List.php';
require_once 'Text/Wiki/Parse/Mediawiki/Newline.php';
require_once 'Text/Wiki/Parse/Mediawiki/Preformatted.php';
require_once 'Text/Wiki/Parse/Mediawiki/Raw.php';
require_once 'Text/Wiki/Parse/Mediawiki/Redirect.php';
require_once 'Text/Wiki/Parse/Mediawiki/Subscript.php';
require_once 'Text/Wiki/Parse/Mediawiki/Superscript.php';
require_once 'Text/Wiki/Parse/Mediawiki/Table.php';
require_once 'Text/Wiki/Parse/Mediawiki/Tt.php';
require_once 'Text/Wiki/Parse/Mediawiki/Url.php';
require_once 'Text/Wiki/Parse/Mediawiki/Wikilink.php';

// default parse rules used by Mediawiki parser
require_once 'Text/Wiki/Parse/Default/Horiz.php';

class Text_Wiki_Parse_Mediawiki_AllTests extends PHPUnit_Framework_TestSuite
{
    
    public static function suite()
    {
        $suite = new PHPUnit_Framework_TestSuite('Text_Wiki_Render_Mediawiki_TestSuite');
        $suite->addTestSuite('Text_Wiki_Parse_Mediawiki_Break_Test');
        /*$suite->addTestSuite('Text_Wiki_Parse_Mediawiki_Code_Test');
        $suite->addTestSuite('Text_Wiki_Parse_Mediawiki_Comment_Test');
        $suite->addTestSuite('Text_Wiki_Parse_Mediawiki_Deflist_Test');
        $suite->addTestSuite('Text_Wiki_Parse_Mediawiki_Emphasis_Test');*/
        $suite->addTestSuite('Text_Wiki_Parse_Mediawiki_Heading_Test');
        $suite->addTestSuite('Text_Wiki_Parse_Mediawiki_Horiz_Test');
        /*$suite->addTestSuite('Text_Wiki_Parse_Mediawiki_List_Test');
        $suite->addTestSuite('Text_Wiki_Parse_Mediawiki_Newline_Test');
        $suite->addTestSuite('Text_Wiki_Parse_Mediawiki_Preformatted_Test');
        $suite->addTestSuite('Text_Wiki_Parse_Mediawiki_Raw_Test');*/
        $suite->addTestSuite('Text_Wiki_Parse_Mediawiki_Redirect_Test');
        /*$suite->addTestSuite('Text_Wiki_Parse_Mediawiki_Subscript_Test');
        $suite->addTestSuite('Text_Wiki_Parse_Mediawiki_Superscript_Test');
        $suite->addTestSuite('Text_Wiki_Parse_Mediawiki_Table_Test');
        $suite->addTestSuite('Text_Wiki_Parse_Mediawiki_Tt_Test');*/
        $suite->addTestSuite('Text_Wiki_Parse_Mediawiki_Url_Test');
        $suite->addTestSuite('Text_Wiki_Parse_Mediawiki_Wikilink_Test');
        
        return $suite;
    }
    
}

class Text_Wiki_Parse_Mediawiki_SetUp_Tests extends PHPUnit_Framework_TestCase
{

    protected function setUp()
    {
        $obj = Text_Wiki::factory('Mediawiki');
        $testClassName = get_class($this);
        $ruleName = preg_replace('/Text_Wiki_Parse_Mediawiki_(.+?)_Test/', '\\1', $testClassName);
        $this->className = 'Text_Wiki_Parse_' . $ruleName;
        $this->t = new $this->className($obj);
        $this->fixture = file_get_contents(dirname(__FILE__) . '/fixtures/mediawiki_syntax.txt');
        preg_match_all($this->t->regex, $this->fixture, $this->matches);
    }
    
}

class Text_Wiki_Parse_Mediawiki_Break_Test extends Text_Wiki_Parse_Mediawiki_SetUp_Tests
{
    
    public function testMediawikiParseBreakProcess()
    {
        $matches1 = array(0 => '<br />');
        $matches2 = array(0 => '<br   />');
        
        $this->assertRegExp('/\d+?/', $this->t->process($matches1));
        $this->assertRegExp('/\d+?/', $this->t->process($matches2));

        $tokens = array(0 => array(0 => 'Break', 1 => array()),
                        1 => array(0 => 'Break', 1 => array()));

        $this->assertEquals(array_values($tokens), array_values($this->t->wiki->tokens));
    }
    
    public function testMediawikiParseBreakRegex()
    {
        $expectedResult = array(0 => array(0 => '<br />', 1 => '<br   />'));
        $this->assertEquals($expectedResult, $this->matches);
    }
    
}

class Text_Wiki_Parse_Mediawiki_Heading_Test extends Text_Wiki_Parse_Mediawiki_SetUp_Tests
{
    
    public function testMediawikiParseHeadingProcess()
    {
        $matches1 = array(0 => "======Level 6 heading======\n", 1 => '======', 2 => 'Level 6 heading');
        $matches2 = array(0 => "=Level 1 heading=\n", 1 => '=', 2 => 'Level 1 heading');
        $matches3 = array(0 => "==Level 2 heading==\n", 1 => '==', 2 => 'Level 2 heading');

        $this->assertRegExp("/\d+?Level 6 heading\d+?\n/", $this->t->process($matches1));
        $this->assertRegExp("/\d+?Level 1 heading\d+?\n/", $this->t->process($matches2));
        $this->assertRegExp("/\d+?Level 2 heading\d+?\n/", $this->t->process($matches3));

        $tokens = array(
            0 => array(0 => 'Heading', 1 => array('type' => 'start', 'level' => 6, 'text' => 'Level 6 heading', 'id' => 'toc0')),
            1 => array(0 => 'Heading', 1 => array('type' => 'end', 'level' => 6)),
            2 => array(0 => 'Heading', 1 => array('type' => 'start', 'level' => 1, 'text' => 'Level 1 heading', 'id' => 'toc1')),
            3 => array(0 => 'Heading', 1 => array('type' => 'end', 'level' => 1)),
            4 => array(0 => 'Heading', 1 => array('type' => 'start', 'level' => 2, 'text' => 'Level 2 heading', 'id' => 'toc2')),
            5 => array(0 => 'Heading', 1 => array('type' => 'end', 'level' => 2))
        );
        
        $this->assertEquals(array_values($tokens), array_values($this->t->wiki->tokens));
    }
    
    public function testMediawikiParseHeadingRegex()
    {
        $expectedResult = array(
            0 => array(0 => "=Level 1 heading=\n", 1 => "==Level 2 heading==\n", 2 => "==Level 2 heading==\n", 3 => "===Level 3 heading===\n", 4 => "====Level 4 heading====\n", 5 => "===Level 3 heading===\n", 6 => "===Level 3 heading===\n", 7 => "=====Level 5 heading=====\n", 8 => "======Level 6 heading======\n"),
            1 => array(0 => '=', 1 => '==', 2 => '==', 3 => '===', 4 => '====', 5 => '===', 6 => '===', 7 => '=====', 8 => '======'),
            2 => array(0 => 'Level 1 heading', 1 => 'Level 2 heading', 2 => 'Level 2 heading', 3 => 'Level 3 heading', 4 => 'Level 4 heading', 5 => 'Level 3 heading', 6 => 'Level 3 heading', 7 => 'Level 5 heading', 8 => 'Level 6 heading')
        );
        $this->assertEquals($expectedResult, $this->matches);
    }
    
}

// Mediawiki parse uses horiz rule from default parser
class Text_Wiki_Parse_Mediawiki_Horiz_Test extends Text_Wiki_Parse_Mediawiki_SetUp_Tests
{
    
    public function testMediawikiParseHorizProcess()
    {
        $matches1 = array(0 => '----', 1 => '----');
        $matches2 = array(0 => '------', 1 => '------');

        $this->assertRegExp("/\d+?/", $this->t->process($matches1));
        $this->assertRegExp("/\d+?/", $this->t->process($matches2));

        $tokens = array(
            0 => array(0 => 'Horiz', array()),
            1 => array(0 => 'Horiz', array()),
        );
        
        $this->assertEquals(array_values($tokens), array_values($this->t->wiki->tokens));
    }
    
    public function testMediawikiParseHeadingRegex()
    {
        $expectedResult = array(
            0 => array(0 => '----', 1 => '------'),
            1 => array(0 => '----', 1 => '------'),
        );
        $this->assertEquals($expectedResult, $this->matches);
    }
    
}

class Text_Wiki_Parse_Mediawiki_Redirect_Test extends Text_Wiki_Parse_Mediawiki_SetUp_Tests
{
    
    public function testMediawikiParseRedirectProcess()
    {
        $matches1 = array(0 => "#REDIRECT [[Some page name]]", 1 => 'Some page name');
        $matches2 = array(0 => "#redirect [[Other page name]]", 1 => 'Other page name');

        $this->assertRegExp("/\d+?Some page name\d+?/", $this->t->process($matches1));
        $this->assertRegExp("/\d+?Other page name\d+?/", $this->t->process($matches2));

        $tokens = array(
            0 => array(0 => 'Redirect', 1 => array('type' => 'start', 'text' => 'Some page name')),
            1 => array(0 => 'Redirect', 1 => array('type' => 'end')),
            2 => array(0 => 'Redirect', 1 => array('type' => 'start', 'text' => 'Other page name')),
            3 => array(0 => 'Redirect', 1 => array('type' => 'end')),
        );
        
        $this->assertEquals(array_values($tokens), array_values($this->t->wiki->tokens));
    }
    
    public function testMediawikiParseRedirectRegex()
    {
        $expectedResult = array(
            0 => array(0 => "#REDIRECT [[Some page name]]", 1 => "#redirect [[Other page name]]"),
            1 => array(0 => 'Some page name', 1 => 'Other page name'),
        );
        $this->assertEquals($expectedResult, $this->matches);
    }
    
}

class Text_Wiki_Parse_Mediawiki_Wikilink_Test extends Text_Wiki_Parse_Mediawiki_SetUp_Tests
{
    
    public function testMediawikiParseWikilinkProcessWithSpaceUnderscoreFalse()
    {
        $this->t->conf['spaceUnderscore'] = false;

        $matches1 = array(0 => '[[convallis elementum]]', 1 => '', 2 => '', 3 => 'convallis elementum', 4 => '', 5 => '', 6 => '');
        $matches2 = array(0 => '[[Etiam]]', 1 => '', 2 => '', 3 => 'Etiam', 4 => '', 5 => '', 6 => '');
        $matches3 = array(0 => '[[pt:Language link]]', 1 => '', 2 => 'pt:', 3 => 'Language link', 4 => '', 5 => '', 6 => '');
        $matches4 = array(0 => '[[Image:some image]]', 1 => '', 2 => 'Image:', 3 => 'some image', 4 => '', 5 => '', 6 => '');
        $matches5 = array(0 => '[[Etiam|description text]]', 1 => '', 2 => '', 3 => 'Etiam', 4 => '', 5 => 'description text', 6 => '');

        $this->assertRegExp("/\d+?/", $this->t->process($matches1));
        $this->assertRegExp("/\d+?/", $this->t->process($matches2));
        $this->assertRegExp("/\d+?/", $this->t->process($matches3));
        $this->assertRegExp("/\d+?/", $this->t->process($matches4));
        $this->assertRegExp("/\d+?/", $this->t->process($matches5));

        $tokens = array(
            0 => array(0 => 'Wikilink', 1 => array('page' => 'convallis elementum', 'anchor' => '', 'text' => 'convallis elementum')),
            1 => array(0 => 'Wikilink', 1 => array('page' => 'Etiam', 'anchor' => '', 'text' => 'Etiam')),
            2 => array(0 => 'Wikilink', 1 => array('page' => 'pt:Language link', 'anchor' => '', 'text' => 'pt:Language link')),
            3 => array(0 => 'Image', 1 => array('src' => 'some image', 'attr' => array('alt' => 'some image'))),
            4 => array(0 => 'Wikilink', 1 => array('page' => 'Etiam', 'anchor' => '', 'text' => 'description text')),
        );
        
        $this->assertEquals(array_values($tokens), array_values($this->t->wiki->tokens));
    }
     
    public function testMediawikiParseWikilinkProcessWithSpaceUnderscoreTrue()
    {
        $this->t->conf['spaceUnderscore'] = true;

        $matches1 = array(0 => '[[convallis elementum]]', 1 => '', 2 => '', 3 => 'convallis elementum', 4 => '', 5 => '', 6 => '');

        $this->assertRegExp("/\d+?/", $this->t->process($matches1));

        $tokens = array(
            0 => array(0 => 'Wikilink', 1 => array('page' => 'convallis_elementum', 'anchor' => '', 'text' => 'convallis elementum')),
        );
        
        $this->assertEquals(array_values($tokens), array_values($this->t->wiki->tokens));
    }
    
    public function testMediawikiParseWikilinkRegex()
    {
        require_once(dirname(__FILE__) . '/fixtures/test_mediawiki_wikilink_expected_matches.php');
        global $expectedWikilinkMatches;
        $fixture = file_get_contents(dirname(__FILE__) . '/fixtures/mediawiki_syntax_to_test_wikilink.txt');
        preg_match_all($this->t->regex, $fixture, $matches);
        
        $this->assertEquals($expectedWikilinkMatches, $matches);
    }
    
}

class Text_Wiki_Parse_Mediawiki_Url_Test extends PHPUnit_Framework_TestCase
{

    protected function setUp()
    {
        $textWiki = new Text_Wiki('Mediawiki');
        $this->obj = new Text_Wiki_Parse_Url($textWiki);
    }

    public function testMediawikiParseUrlParse()
    {
        // for some weird reason I was unable to mock this class calling the constructor that is why to
        // test the regular expression I'm testing the tokens created (instead of testing many times each process function was called)
        $this->obj->wiki->source = file_get_contents(dirname(__FILE__) . '/fixtures/mediawiki_syntax.txt');
        $this->obj->parse();

        $tokens = array(
            1 => array(0 => 'Url', 1 => array('type' => 'descr', 'href' => 'http://www.example.com', 'text' => 'See the example site')),
            2 => array(0 => 'Url', 1 => array('type' => 'descr', 'href' => 'http://exemple.com/index.php', 'text' => 'consectetur adipiscing')),
            3 => array(0 => 'Url', 1 => array('type' => 'descr', 'href' => 'http://exemple.com/index.php#anchor', 'text' => 'Pellentesque')),
            4 => array(0 => 'Url', 1 => array('type' => 'descr', 'href' => 'http://www.somelink.com/index.php', 'text' => 'http://www.somelink.com/index.php')),
            5 => array(0 => 'Url', 1 => array('type' => 'inline', 'href' => 'http://example.com/index.php', 'text' => 'http://example.com/index.php'))
        );

        $this->assertEquals(array_values($tokens), array_values($this->obj->wiki->tokens));
    }

/*    public function testMediawikiParseUrlParseWithMocking()
    {
        // NOT WORKING: unable to mock the class Text_Wiki_Parse_Url using its constructor
        $textWiki = Text_Wiki::factory('Mediawiki');
        $obj = $this->getMock('Text_Wiki_Parse_Url',
            array('process', 'processWithoutProtocol', 'processInlineEmail', 'processFootnote', 'processOrdinary', 'processDescr'),
            array($textWiki),
        );
        $obj->expects($this->once())->method('process');
        $obj->expects($this->never())->method('processWithoutProtocol');
        $obj->expects($this->never())->method('processInlineEmail');
        $obj->expects($this->never())->method('processFootnote');
        $obj->expects($this->exactly(2))->method('processOrdinary');
        $obj->expects($this->exactly(5))->method('processDescr');
        $obj->wiki->source = file_get_contents(dirname(__FILE__) . '/fixtures/mediawiki_syntax.txt');
        $obj->parse();
    }*/

    public function testProcess()
    {
        $this->markTestIncomplete('Test incomplete');
    }

    public function testProcessWithoutProtocol()
    {
        $this->markTestIncomplete('Test incomplete');
    }

    public function testProcessInlineEmail()
    {
        $this->markTestIncomplete('Test incomplete');
    }

    public function testProcessFootnote()
    {
        $this->markTestIncomplete('Test incomplete');
    }

    public function testProcessOrdinary()
    {
        $this->markTestIncomplete('Test incomplete');
    }

    public function testProcessDescr()
    {
        $this->markTestIncomplete('Test incomplete');
    }
}

?>