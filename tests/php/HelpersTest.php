<?php

use PHPUnit\Framework\TestCase;

class AceTestModx
{
    public $controller = null;
    /** @var array */
    public $options = array();

    public function __construct(array $options = array(), $controller = null)
    {
        $this->options = $options;
        $this->controller = $controller;
    }

    public function getOption($key, $options = null, $default = null)
    {
        if (is_array($options) && array_key_exists($key, $options)) {
            return $options[$key];
        }
        if (array_key_exists($key, $this->options)) {
            return $this->options[$key];
        }
        return $default;
    }
}

class AceTestContext
{
    /** @var array */
    public $options = array();

    public function __construct(array $options = array())
    {
        $this->options = $options;
    }

    public function getOption($key, $default = null)
    {
        if (array_key_exists($key, $this->options)) {
            return $this->options[$key];
        }
        return $default;
    }
}

class HelpersTest extends TestCase
{
    protected function tearDown(): void
    {
        $_REQUEST = array();
        parent::tearDown();
    }

    public function testMimeSupportsModxTagsForHtmlLikeMime()
    {
        $this->assertTrue(aceMimeSupportsModxTags('text/html'));
        $this->assertTrue(aceMimeSupportsModxTags('text/x-smarty'));
        $this->assertTrue(aceMimeSupportsModxTags('text/x-twig'));
    }

    public function testMimeRejectsNonHtmlAndFileHints()
    {
        $this->assertFalse(aceMimeSupportsModxTags(''));
        $this->assertFalse(aceMimeSupportsModxTags('text/x-scss'));
        $this->assertFalse(aceMimeSupportsModxTags('text/css'));
        $this->assertFalse(aceMimeSupportsModxTags('application/json'));
        $this->assertFalse(aceMimeSupportsModxTags('application/x-php'));
        $this->assertFalse(aceMimeSupportsModxTags('@FILE:style.scss'));
    }

    public function testIsNonAceRichTextEditorDetectsOtherRte()
    {
        $this->assertTrue(aceIsNonAceRichTextEditor(new AceTestModx(array('which_editor' => 'TinyMCE'))));
        $this->assertTrue(aceIsNonAceRichTextEditor(new AceTestModx(array('which_editor' => 'tinymce'))));
    }

    public function testIsNonAceRichTextEditorAllowsAceOrEmpty()
    {
        $this->assertFalse(aceIsNonAceRichTextEditor(new AceTestModx(array('which_editor' => 'Ace'))));
        $this->assertFalse(aceIsNonAceRichTextEditor(new AceTestModx(array('which_editor' => 'ace'))));
        $this->assertFalse(aceIsNonAceRichTextEditor(new AceTestModx(array('which_editor' => ''))));
        $this->assertFalse(aceIsNonAceRichTextEditor(new AceTestModx(array())));
    }

    public function testGetEffectiveUseEditorUsesGlobalWhenNoContext()
    {
        $this->assertTrue(aceGetEffectiveUseEditor(new AceTestModx(array('use_editor' => 1))));
        $this->assertFalse(aceGetEffectiveUseEditor(new AceTestModx(array('use_editor' => 0))));
    }

    public function testGetEffectiveUseEditorPrefersContextOverride()
    {
        $controller = new stdClass();
        $controller->context = new AceTestContext(array('use_editor' => 0));
        $this->assertFalse(aceGetEffectiveUseEditor(new AceTestModx(array('use_editor' => 1), $controller)));

        $controller->context = new AceTestContext(array('use_editor' => 1));
        $this->assertTrue(aceGetEffectiveUseEditor(new AceTestModx(array('use_editor' => 0), $controller)));
    }

    public function testGetEffectiveWhichEditorPrefersContextOverride()
    {
        $controller = new stdClass();
        $controller->context = new AceTestContext(array('which_editor' => 'TinyMCE'));
        $modx = new AceTestModx(array('which_editor' => 'Ace'), $controller);
        $this->assertSame('TinyMCE', aceGetEffectiveWhichEditor($modx));
        $this->assertTrue(aceIsNonAceRichTextEditor($modx));
    }

    public function testIsResourceDataPageFromControllerConfig()
    {
        $controller = new stdClass();
        $controller->config = array('controller' => 'resource/data');
        $this->assertTrue(aceIsResourceDataPage(new AceTestModx(array(), $controller)));

        $controller->config = array('controller' => 'Resource\\Data');
        $this->assertTrue(aceIsResourceDataPage(new AceTestModx(array(), $controller)));
    }

    public function testIsResourceDataPageFromRequestAction()
    {
        $controller = new stdClass();
        $controller->config = array();
        $_REQUEST['a'] = 'resource/data';
        $this->assertTrue(aceIsResourceDataPage(new AceTestModx(array(), $controller)));
    }

    public function testIsResourceDataPageFalseOtherwise()
    {
        $this->assertFalse(aceIsResourceDataPage(new AceTestModx(array())));

        $controller = new stdClass();
        $controller->config = array('controller' => 'resource/update');
        $this->assertFalse(aceIsResourceDataPage(new AceTestModx(array(), $controller)));
    }

    public function testDefaultSnippetsFileContainsCoreTriggers()
    {
        $path = dirname(__DIR__, 2) . '/assets/components/ace/snippets/modx.default.snippets';
        $this->assertFileExists($path);
        $contents = file_get_contents($path);
        $this->assertStringContainsString("snippet getr\n", $contents);
        $this->assertStringContainsString("snippet pdoResources\n", $contents);
        $this->assertStringContainsString("snippet chunk\n", $contents);
        $this->assertStringContainsString('[[!getResources?', $contents);
    }
}
