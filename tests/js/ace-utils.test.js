const { describe, it } = require('node:test');
const assert = require('node:assert/strict');
const path = require('node:path');
const utils = require(path.join(__dirname, '../../assets/components/ace/ace-utils.js'));

describe('parseBoolSetting', () => {
  it('uses default for empty values', () => {
    assert.equal(utils.parseBoolSetting(undefined, true), true);
    assert.equal(utils.parseBoolSetting(null, true), true);
    assert.equal(utils.parseBoolSetting('', true), true);
    assert.equal(utils.parseBoolSetting(undefined, false), false);
  });

  it('parses truthy MODX setting values', () => {
    assert.equal(utils.parseBoolSetting(true, false), true);
    assert.equal(utils.parseBoolSetting('1', false), true);
    assert.equal(utils.parseBoolSetting(1, false), true);
  });

  it('parses falsy MODX setting values', () => {
    assert.equal(utils.parseBoolSetting(false, true), false);
    assert.equal(utils.parseBoolSetting('0', true), false);
    assert.equal(utils.parseBoolSetting(0, true), false);
  });
});

describe('draftStorageKey', () => {
  it('builds stable encoded keys', () => {
    assert.equal(
      utils.draftStorageKey('element/chunk/update', 'modx-chunk-snippet', '12'),
      'ace:draft:element%2Fchunk%2Fupdate:modx-chunk-snippet:12'
    );
  });

  it('defaults missing parts without colliding with id 0', () => {
    assert.equal(utils.draftStorageKey('', '', ''), 'ace:draft:manager:field:new');
    assert.equal(utils.draftStorageKey('a', 'n', 0), 'ace:draft:a:n:0');
  });
});

describe('shouldOfferDraftRestore', () => {
  it('offers only non-empty differing drafts', () => {
    assert.equal(utils.shouldOfferDraftRestore('new', 'old'), true);
    assert.equal(utils.shouldOfferDraftRestore('same', 'same'), false);
    assert.equal(utils.shouldOfferDraftRestore('', 'old'), false);
    assert.equal(utils.shouldOfferDraftRestore(null, 'old'), false);
  });
});

describe('normalizeCssColor', () => {
  it('accepts hex colors', () => {
    assert.equal(utils.normalizeCssColor('#fff'), '#fff');
    assert.equal(utils.normalizeCssColor('#ffffff'), '#ffffff');
    assert.equal(utils.normalizeCssColor('#ffffffff'), '#ffffffff');
  });

  it('accepts valid rgb/hsl', () => {
    assert.equal(utils.normalizeCssColor('rgb(0,0,0)'), 'rgb(0,0,0)');
    assert.equal(utils.normalizeCssColor('rgba(255, 128, 0, 0.5)'), 'rgba(255, 128, 0, 0.5)');
    assert.equal(utils.normalizeCssColor('hsl(120, 50%, 50%)'), 'hsl(120, 50%, 50%)');
  });

  it('rejects invalid colors', () => {
    assert.equal(utils.normalizeCssColor('red'), null);
    assert.equal(utils.normalizeCssColor('#gg'), null);
    assert.equal(utils.normalizeCssColor('rgb(999,0,0)'), null);
    assert.equal(utils.normalizeCssColor(''), null);
  });
});

describe('mimeSupportsColorPreview', () => {
  it('allows css/html family mimes', () => {
    assert.equal(utils.mimeSupportsColorPreview('text/css'), true);
    assert.equal(utils.mimeSupportsColorPreview('text/x-scss'), true);
    assert.equal(utils.mimeSupportsColorPreview('text/html'), true);
  });

  it('rejects php/json/empty', () => {
    assert.equal(utils.mimeSupportsColorPreview('application/x-php'), false);
    assert.equal(utils.mimeSupportsColorPreview('application/json'), false);
    assert.equal(utils.mimeSupportsColorPreview(''), false);
  });
});

describe('shouldWaitForAsyncBuffer', () => {
  it('waits only for resource cache buffer field', () => {
    assert.equal(utils.shouldWaitForAsyncBuffer('modx-rdata-buffer'), true);
    assert.equal(utils.shouldWaitForAsyncBuffer('modx-chunk-snippet'), false);
    assert.equal(utils.shouldWaitForAsyncBuffer('ta'), false);
  });
});

const PHP_OPEN = '<' + '?php';
const PHP_SHORT = '<' + '?';
const PHP_CLOSE = '?' + '>';

describe('stripModxScriptWrapper', () => {
  it('matches modScript::set for a normal open tag', () => {
    assert.equal(
      utils.stripModxScriptWrapper(PHP_OPEN + '\necho "ace-php-tag";\n'),
      'echo "ace-php-tag";'
    );
  });

  it('drops a short open tag and a closing tag', () => {
    assert.equal(
      utils.stripModxScriptWrapper(PHP_SHORT + ' echo 1; ' + PHP_CLOSE),
      'echo 1;'
    );
  });

  it('leaves a short echo as an equals sign', () => {
    assert.equal(
      utils.stripModxScriptWrapper(PHP_SHORT + '= $foo ' + PHP_CLOSE),
      '= $foo'
    );
  });

  it('does not treat PHP as case-insensitive', () => {
    assert.equal(
      utils.stripModxScriptWrapper(PHP_SHORT + 'PHP echo 1;'),
      'PHP echo 1;'
    );
  });

  it('keeps a tab that the second trim does not remove', () => {
    assert.equal(
      utils.stripModxScriptWrapper(PHP_OPEN + '\n\techo 1;'),
      '\techo 1;'
    );
  });

  it('does not strip a tag that is not at the edge', () => {
    const body = 'echo ' + PHP_OPEN + ' 1;';
    assert.equal(utils.stripModxScriptWrapper(body), body);
  });
});

describe('fullscreenInsets', () => {
  it('clears a left nav and a top action bar', () => {
    assert.deepEqual(
      utils.fullscreenInsets(
        {left: 0, right: 70, top: 0, bottom: 800, width: 70, height: 800},
        {top: 0, bottom: 60, width: 437, height: 60}
      ),
      {top: 60, left: 70}
    );
  });

  it('grows when the nav or the action bar is larger', () => {
    assert.deepEqual(
      utils.fullscreenInsets(
        {left: 0, right: 88.2, top: 0, bottom: 900, width: 88.2, height: 900},
        {top: 0, bottom: 96.1, width: 500, height: 96.1}
      ),
      {top: 97, left: 89}
    );
  });

  it('treats a wide header as a top bar', () => {
    assert.deepEqual(
      utils.fullscreenInsets(
        {left: 0, right: 1400, top: 0, bottom: 48, width: 1400, height: 48},
        {top: 48, bottom: 100, width: 400, height: 52}
      ),
      {top: 100, left: 0}
    );
  });

  it('ignores action buttons that are not on the top edge', () => {
    assert.deepEqual(
      utils.fullscreenInsets(
        {left: 0, right: 70, top: 0, bottom: 800, width: 70, height: 800},
        {top: 400, bottom: 460, width: 400, height: 60}
      ),
      {top: 0, left: 70}
    );
  });

  it('keeps a side nav that is not flush with the viewport', () => {
    assert.deepEqual(
      utils.fullscreenInsets(
        {left: 8, right: 78, top: 0, bottom: 800, width: 70, height: 800},
        {top: 0, bottom: 60, width: 400, height: 60}
      ),
      {top: 60, left: 78}
    );
  });

  it('returns zero when the chrome is missing', () => {
    assert.deepEqual(utils.fullscreenInsets(null, null), {top: 0, left: 0});
  });

  it('ignores an action bar that sits fully above the viewport', () => {
    assert.deepEqual(
      utils.fullscreenInsets(
        {left: 0, right: 70, top: 0, bottom: 800, width: 70, height: 800},
        {top: -100, bottom: -40, width: 400, height: 60}
      ),
      {top: 0, left: 70}
    );
  });

  it('clears a bar that still crosses the top edge', () => {
    assert.deepEqual(
      utils.fullscreenInsets(null, {top: -10, bottom: 50, width: 200, height: 60}),
      {top: 50, left: 0}
    );
  });
});

describe('shouldKeepEditorAfterModxPhpStrip', () => {
  const source = PHP_OPEN + '\necho 1;\n' + PHP_CLOSE;

  it('keeps the editor when the save response is only the wrapper strip', () => {
    assert.equal(
      utils.shouldKeepEditorAfterModxPhpStrip(source, 'echo 1;', 'application/x-php'),
      true
    );
    assert.equal(
      utils.shouldKeepEditorAfterModxPhpStrip(source, 'echo 1;', 'text/x-php'),
      true
    );
  });

  it('applies a real body change', () => {
    assert.equal(
      utils.shouldKeepEditorAfterModxPhpStrip(source, 'echo 2;', 'application/x-php'),
      false
    );
  });

  it('does not freeze html chunks or templates', () => {
    assert.equal(
      utils.shouldKeepEditorAfterModxPhpStrip(source, 'echo 1;', 'text/html'),
      false
    );
    assert.equal(
      utils.shouldKeepEditorAfterModxPhpStrip(source, 'echo 1;', 'text/x-smarty'),
      false
    );
  });

  it('rejects non-strings', () => {
    assert.equal(
      utils.shouldKeepEditorAfterModxPhpStrip(source, null, 'application/x-php'),
      false
    );
  });
});
