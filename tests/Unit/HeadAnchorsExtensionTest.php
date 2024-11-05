<?php

it('adds anchor links to headings with custom ids', function () {
    $markdown = '# My Heading {#custom-id}';
    $html = getHeadAnchorsConverter()->convert($markdown)->getContent();

    expect($html)->toEqual("<h1 id=\"custom-id\"><a href=\"#custom-id\">My Heading</a></h1>\n");
});

it('handles headings', function () {
    $markdown = "## Test\n## Test again";
    $html = getHeadAnchorsConverter()->convert($markdown)->getContent();

    expect($html)->toEqual("<h2>Test</h2>\n<h2>Test again</h2>\n");
});

it('handles special characters', function () {
    $markdown = '## Hello & World! @#$%';
    $html = getHeadAnchorsConverter()->convert($markdown)->getContent();

    expect($html)->toEqual("<h2>Hello &amp; World! @#$%</h2>\n");
});

it('handles unicode characters', function () {
    $markdown = '## 你好，世界';
    $html = trim(getHeadAnchorsConverter()->convert($markdown)->getContent());

    expect($html)->toEqual('<h2>你好，世界</h2>');
});

it('can configure heading links', function () {
    $markdown = <<<'MARKDOWN'
<a name="introduction"></a>
## Introduction

Hello World!


## Heading 2 {#heading-2}

Another section!
MARKDOWN;

    expect(trim(getHeadAnchorsConverter()->convert($markdown)->getContent()))->toMatchSnapshot();
});

it('can handle multiple headings with anchors', function () {
    $markdown = <<<'MARKDOWN'
# Heading 1 {#heading-1}
Section 1

<a name="heading-2"></a>
## Heading 2
Section 2

### Heading 3 {#heading-3}
Section 3
MARKDOWN;

    $expected = <<<'HTML'
<h1 id="heading-1"><a href="#heading-1">Heading 1</a></h1>
<p>Section 1</p>
<h2 id="heading-2"><a href="#heading-2">Heading 2</a></h2>
<p>Section 2</p>
<h3 id="heading-3"><a href="#heading-3">Heading 3</a></h3>
<p>Section 3</p>

HTML;

    expect(getHeadAnchorsConverter()->convert($markdown)->getContent())->toBe($expected);
});
