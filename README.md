# CommonMark Extensions

[![Tests](https://github.com/curder/commonmark-extensions/actions/workflows/run-tests.yml/badge.svg)](https://github.com/curder/commonmark-extensions/actions/workflows/run-tests.yml)
[![Check & fix styling](https://github.com/curder/commonmark-extensions/actions/workflows/pint-fixer.yml/badge.svg)](https://github.com/curder/commonmark-extensions/actions/workflows/pint-fixer.yml)
[![Latest Stable Version](http://poser.pugx.org/curder/commonmark-extensions/v)](https://packagist.org/packages/curder/commonmark-extensions)
[![Total Downloads](http://poser.pugx.org/curder/commonmark-extensions/downloads)](https://packagist.org/packages/curder/commonmark-extensions)
[![License](http://poser.pugx.org/curder/commonmark-extensions/license)](https://packagist.org/packages/curder/commonmark-extensions)

一个用于 [league/commonmark](https://github.com/thephpleague/commonmark) 的扩展集合,提供了代码组、自定义容器和标题锚点等功能。

## 功能特性

### 1. 代码组 (Code Groups)

允许创建分组的代码块,支持多语言切换展示: 

~~~markdown
::: code-group
```php
<?php
echo "Hello, World!";
```

```python
print("Hello, World!")
```
:::
~~~

### 2. 自定义容器 (Custom Containers)

提供多种预定义的容器类型:

```markdown
::: tip 提示
这是一个提示容器。
:::
```

```markdown
::: warning 警告
这是一个警告容器。
:::
```

```markdown
::: danger 危险
这是一个危险容器。
:::
```

```markdown
::: details 详情
这是一个详情容器。
:::
```


### 3. 标题锚点 (Head Anchors)

自动为标题添加锚点链接:

```markdown
# 标题一 {#anchor-1}

Section 1

## 标题二 {#anchor-2}

Section 2

<a name="anchor-3"></a>
### 标题三

Section 3
```

## 安装

通过 Composer 安装:

```bash
composer require curder/commonmark-extensions
```

## 使用方法

```php
use League\CommonMark\Environment\Environment;
use League\CommonMark\Extension\CommonMark\CommonMarkCoreExtension;
use League\CommonMark\MarkdownConverter;
use Curder\CommonmarkExtensions\CodeGroupsExtension;
use Curder\CommonmarkExtensions\CustomContainersExtension;
use Curder\CommonmarkExtensions\HeadAnchorsExtension;

// 创建环境配置
$config = [];

// 创建环境并添加扩展
$environment = new Environment($config);
$environment->addExtension(new CommonMarkCoreExtension());
$environment->addExtension(new CodeGroupsExtension());
$environment->addExtension(new CustomContainersExtension());
$environment->addExtension(new HeadAnchorsExtension());

// 创建 Markdown 转换器
$converter = new MarkdownConverter($environment);

// 转换 Markdown 为 HTML
$html = $converter->convert($markdown)->getContent();
```

## 配置选项

### 代码组配置

```php
'code_groups' => [
    'enabled' => true,
    'class_names' => [
        'code-group' => 'code-group',
        'code-group-tabs' => 'code-group-tabs',
        'code-group-contents' => 'code-group-contents',
        'code-group-tab' => 'code-group-tab',
        'code-group-tab-active' => 'active',
        'code-group-content' => 'code-group-content',
        'code-group-content-active' => 'active',
    ],
],
```

### 自定义容器配置

```php
'custom_containers' => [
    'enabled' => true,
    'labels' => [
        'tip' => '提示',
        'warning' => '警告',
        'danger' => '危险',
        'details' => '详情',
    ],
    'details' => [
        'open' => false,
    ],
    'class_names' => [
        'container' => 'custom-block',
        'title' => 'custom-block-title',
        'content' => 'custom-block-content',
    ],
    'icons' => [
        'enabled' => false,
        'tip' => '<svg viewBox="0 0 16 16" version="1.1" width="16" height="16" aria-hidden="true"><path d="M8 1.5c-2.363 0-4 1.69-4 3.75 0 .984.424 1.625.984 2.304l.214.253c.223.264.47.556.673.848.284.411.537.896.621 1.49a.75.75 0 0 1-1.484.211c-.04-.282-.163-.547-.37-.847a8.456 8.456 0 0 0-.542-.68c-.084-.1-.173-.205-.268-.32C3.201 7.75 2.5 6.766 2.5 5.25 2.5 2.31 4.863 0 8 0s5.5 2.31 5.5 5.25c0 1.516-.701 2.5-1.328 3.259-.095.115-.184.22-.268.319-.207.245-.383.453-.541.681-.208.3-.33.565-.37.847a.751.751 0 0 1-1.485-.212c.084-.593.337-1.078.621-1.489.203-.292.45-.584.673-.848.075-.088.147-.173.213-.253.561-.679.985-1.32.985-2.304 0-2.06-1.637-3.75-4-3.75ZM5.75 12h4.5a.75.75 0 0 1 0 1.5h-4.5a.75.75 0 0 1 0-1.5ZM6 15.25a.75.75 0 0 1 .75-.75h2.5a.75.75 0 0 1 0 1.5h-2.5a.75.75 0 0 1-.75-.75Z"></path></svg>',
        'warning' => '<svg viewBox="0 0 16 16" version="1.1" width="16" height="16" aria-hidden="true"><path d="M6.457 1.047c.659-1.234 2.427-1.234 3.086 0l6.082 11.378A1.75 1.75 0 0 1 14.082 15H1.918a1.75 1.75 0 0 1-1.543-2.575Zm1.763.707a.25.25 0 0 0-.44 0L1.698 13.132a.25.25 0 0 0 .22.368h12.164a.25.25 0 0 0 .22-.368Zm.53 3.996v2.5a.75.75 0 0 1-1.5 0v-2.5a.75.75 0 0 1 1.5 0ZM9 11a1 1 0 1 1-2 0 1 1 0 0 1 2 0Z"></path></svg>',
        'danger' => '<svg viewBox="0 0 16 16" version="1.1" width="16" height="16" aria-hidden="true"><path d="M4.47.22A.749.749 0 0 1 5 0h6c.199 0 .389.079.53.22l4.25 4.25c.141.14.22.331.22.53v6a.749.749 0 0 1-.22.53l-4.25 4.25A.749.749 0 0 1 11 16H5a.749.749 0 0 1-.53-.22L.22 11.53A.749.749 0 0 1 0 11V5c0-.199.079-.389.22-.53Zm.84 1.28L1.5 5.31v5.38l3.81 3.81h5.38l3.81-3.81V5.31L10.69 1.5ZM8 4a.75.75 0 0 1 .75.75v3.5a.75.75 0 0 1-1.5 0v-3.5A.75.75 0 0 1 8 4Zm0 8a1 1 0 1 1 0-2 1 1 0 0 1 0 2Z"></path></svg>',
        'info' => '<svg viewBox="0 0 16 16" version="1.1" width="16" height="16" aria-hidden="true"><path d="M0 8a8 8 0 1 1 16 0A8 8 0 0 1 0 8Zm8-6.5a6.5 6.5 0 1 0 0 13 6.5 6.5 0 0 0 0-13ZM6.5 7.75A.75.75 0 0 1 7.25 7h1a.75.75 0 0 1 .75.75v2.75h.25a.75.75 0 0 1 0 1.5h-2a.75.75 0 0 1 0-1.5h.25v-2h-.25a.75.75 0 0 1-.75-.75ZM8 6a1 1 0 1 1 0-2 1 1 0 0 1 0 2Z"></path></svg>',
        'details' => '<svg viewBox="0 0 16 16" version="1.1" width="16" height="16" aria-hidden="true"><path d="M0 1.75C0 .784.784 0 1.75 0h12.5C15.216 0 16 .784 16 1.75v9.5A1.75 1.75 0 0 1 14.25 13H8.06l-2.573 2.573A1.458 1.458 0 0 1 3 14.543V13H1.75A1.75 1.75 0 0 1 0 11.25Zm1.75-.25a.25.25 0 0 0-.25.25v9.5c0 .138.112.25.25.25h2a.75.75 0 0 1 .75.75v2.19l2.72-2.72a.749.749 0 0 1 .53-.22h6.5a.25.25 0 0 0 .25-.25v-9.5a.25.25 0 0 0-.25-.25Zm7 2.25v2.5a.75.75 0 0 1-1.5 0v-2.5a.75.75 0 0 1 1.5 0ZM9 9a1 1 0 1 1-2 0 1 1 0 0 1 2 0Z"></path></svg>',
    ],
],
```

### 标题锚点配置

```php
'head_anchors' => [
    'enabled' => true,
],
```

