# CommonMark Extensions

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
],
```

### 标题锚点配置

```php
'head_anchors' => [
    'enabled' => true,
],
```

