<?php

use Curder\CommonmarkExtensions\CodeGroupsExtension;
use League\CommonMark\Environment\Environment;
use League\CommonMark\Extension\CommonMark\CommonMarkCoreExtension;
use League\CommonMark\MarkdownConverter;

test('can parse basic code groups', function () {
    $markdown = <<<'MD'
::: code-group
```js
console.log('Hello from JS!');
```

```php
echo 'Hello from PHP!';
```
:::
MD;

    $html = getCodeGroupsConverter()->convert($markdown)->getContent();

    expect($html)
        ->toContain('class="code-group"')
        ->toContain('class="code-group-tabs"')
        ->toContain('class="code-group-contents"')
        ->toContain('language-js')
        ->toContain('language-php')
        ->toContain('Hello from JS!')
        ->toContain('Hello from PHP!')
        ->not->toContain('data-tab')
        ->toMatchSnapshot();
});

test('can parse code groups with headers', function () {
    $markdown = <<<'MD'
::: code-group
```javascript [JavaScript]
console.log('Hello!');
```

```php [PHP]
echo 'Hello!';
```
:::
MD;

    $html = getCodeGroupsConverter()->convert($markdown)->getContent();

    expect($html)
        ->toContain('JavaScript')
        ->toContain('PHP')
        ->toContain('Hello!')
        ->not->toContain('data-tab')
        ->toMatchSnapshot();
});

test('can handle empty code blocks', function () {
    $markdown = <<<'MD'
::: code-group
```php
```

```js
```
:::
MD;

    $html = getCodeGroupsConverter()->convert($markdown)->getContent();

    expect($html)
        ->toContain('class="code-group"')
        ->toContain('language-php')
        ->toContain('language-js')
        ->toMatchSnapshot();
});

test('can handle code blocks with line markers', function () {
    $markdown = <<<'MD'
::: code-group
```php{1,3-5}{2,4}
line 1
line 2
line 3
line 4
line 5
```
:::
MD;

    $html = getCodeGroupsConverter()->convert($markdown)->getContent();

    expect($html)
        ->toContain('language-php')
        ->toMatchSnapshot();
});

test('can handle invalid line ranges', function () {
    $markdown = <<<'MD'
::: code-group
```php{999}{-1,0,a}
echo 'test';
```
:::
MD;

    $html = getCodeGroupsConverter()->convert($markdown)->getContent();

    expect($html)
        ->toContain('language-php')
        ->toContain('echo \'test\';')
        ->toMatchSnapshot();
});

test('can handle nested code groups', function () {
    $markdown = <<<'MD'
::: code-group
```php
::: code-group
```js
nested
```
:::
```
:::
MD;

    $html = getCodeGroupsConverter()->convert($markdown)->getContent();

    expect($html)
        ->toContain('language-php')
        ->toContain('language-js')
        ->toMatchSnapshot();
});

test('can handle malformed code groups', function () {
    $markdown = <<<'MD'
::: code-group
```
no language specified
```

```invalid{}[]
invalid markers
```

```php{1-3}{
incomplete markers
```

::: nested
should be ignored
:::
:::
MD;

    $html = getCodeGroupsConverter()->convert($markdown)->getContent();

    expect($html)
        ->toContain('class="code-group"')
        ->toMatchSnapshot();
});

test('can handle special characters in titles', function () {
    $markdown = <<<'MD'
::: code-group
```php [PHP & HTML]
echo '<div>';
```

```js [JavaScript & TypeScript]
console.log('<div>');
```
:::
MD;

    $html = getCodeGroupsConverter()->convert($markdown)->getContent();

    expect($html)
        ->toContain('PHP &amp; HTML')
        ->toContain('JavaScript &amp; TypeScript')
        ->not->toContain('data-tab')
        ->toMatchSnapshot();
});

test('can handle empty line markers', function () {
    $markdown = <<<'MD'
::: code-group
```php{}{} [PHP]
echo 'test';
```
:::
MD;

    $html = getCodeGroupsConverter()->convert($markdown)->getContent();

    expect($html)
        ->toContain('language-php')
        ->toContain('echo \'test\';')
        ->toMatchSnapshot();
});

test('can handle overlapping line markers', function () {
    $markdown = <<<'MD'
::: code-group
```php{1-3,2-4}{2-3,3-4}
line 1
line 2
line 3
line 4
```
:::
MD;

    $html = getCodeGroupsConverter()->convert($markdown)->getContent();

    expect($html)
        ->toContain('language-php')
        ->toMatchSnapshot();
});

test('can handle unicode characters', function () {
    $markdown = <<<'MD'
::: code-group
```php [中文标题]
echo '你好，世界！';
```

```js [日本語]
console.log('こんにちは、世界！');
```
:::
MD;

    $html = getCodeGroupsConverter()->convert($markdown)->getContent();

    expect($html)
        ->toContain('中文标题')
        ->toContain('日本語')
        ->toContain('你好，世界！')
        ->toContain('こんにちは、世界！')
        ->toMatchSnapshot();
});

test('can use custom class names for containers', function () {
    $environment = new Environment([
        'code_groups' => [
            'class_name' => [
                'code-group' => 'custom-group',
                'code-group-tabs' => 'custom-tabs',
                'code-group-contents' => 'custom-contents',
            ],
        ],
    ]);
    $environment->addExtension(new CommonMarkCoreExtension());
    $environment->addExtension(new CodeGroupsExtension());
    $converter = new MarkdownConverter($environment);

    $markdown = <<<'MD'
::: code-group
```php
echo 'test';
```
:::
MD;

    $html = $converter->convert($markdown)->getContent();

    expect($html)
        ->toContain('class="custom-group"')
        ->toContain('class="custom-tabs"')
        ->toContain('class="custom-contents"')
        ->toMatchSnapshot();
});

test('can use custom class names for tabs', function () {
    $environment = new Environment([
        'code_groups' => [
            'class_name' => [
                'code-group-tab' => 'custom-tab',
                'code-group-tab-active' => 'custom-active',
            ],
        ],
    ]);
    $environment->addExtension(new CommonMarkCoreExtension());
    $environment->addExtension(new CodeGroupsExtension());
    $converter = new MarkdownConverter($environment);

    $markdown = <<<'MD'
::: code-group
```php [PHP]
echo 'test';
```

```js [JavaScript]
console.log('test');
```
:::
MD;

    $html = $converter->convert($markdown)->getContent();

    expect($html)
        ->toContain('class="custom-tab custom-active"')
        ->toContain('class="custom-tab"')
        ->toMatchSnapshot();
});

test('can use custom class names for content', function () {
    $environment = new Environment([
        'code_groups' => [
            'class_name' => [
                'code-group-content' => 'custom-content',
                'code-group-content-active' => 'custom-active',
            ],
        ],
    ]);
    $environment->addExtension(new CommonMarkCoreExtension());
    $environment->addExtension(new CodeGroupsExtension());
    $converter = new MarkdownConverter($environment);

    $markdown = <<<'MD'
::: code-group
```php
echo 'test';
```

```js
console.log('test');
```
:::
MD;

    $html = $converter->convert($markdown)->getContent();

    expect($html)
        ->toContain('class="custom-content custom-active"')
        ->toContain('class="custom-content"')
        ->toMatchSnapshot();
});

test('can mix default and custom class names', function () {
    $environment = new Environment([
        'code_groups' => [
            'class_name' => [
                'code-group' => 'custom-group',
                // 其他使用默认值
            ],
        ],
    ]);
    $environment->addExtension(new CommonMarkCoreExtension());
    $environment->addExtension(new CodeGroupsExtension());
    $converter = new MarkdownConverter($environment);

    $markdown = <<<'MD'
::: code-group
```php
echo 'test';
```
:::
MD;

    $html = $converter->convert($markdown)->getContent();

    expect($html)
        ->toContain('class="custom-group"')
        ->toContain('class="code-group-tabs"') // 默认值
        ->toContain('class="code-group-contents"') // 默认值
        ->toMatchSnapshot();
});

test('can handle empty class name configuration', function () {
    $environment = new Environment([
        'code_groups' => [
            'class_name' => [],
        ],
    ]);
    $environment->addExtension(new CommonMarkCoreExtension());
    $environment->addExtension(new CodeGroupsExtension());
    $converter = new MarkdownConverter($environment);

    $markdown = <<<'MD'
::: code-group
```php
echo 'test';
```
:::
MD;

    $html = $converter->convert($markdown)->getContent();

    expect($html)
        ->toContain('class="code-group"') // 默认值
        ->toContain('class="code-group-tabs"') // 默认值
        ->toContain('class="code-group-contents"') // 默认值
        ->toMatchSnapshot();
});

test('can handle null class name values', function () {
    $environment = new Environment([
        'code_groups' => [
            'class_name' => [
                'code-group' => null,
                'code-group-tabs' => null,
            ],
        ],
    ]);
    $environment->addExtension(new CommonMarkCoreExtension());
    $environment->addExtension(new CodeGroupsExtension());
    $converter = new MarkdownConverter($environment);

    $markdown = <<<'MD'
::: code-group
```php
echo 'test';
```
:::
MD;

    $html = $converter->convert($markdown)->getContent();

    expect($html)
        ->toContain('class="code-group"') // 默认值
        ->toContain('class="code-group-tabs"') // 默认值
        ->toMatchSnapshot();
});
