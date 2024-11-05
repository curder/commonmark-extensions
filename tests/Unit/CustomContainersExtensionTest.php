<?php

test('it can render info containers', function () {
    // 测试默认标题
    $result = getCustomContainersConverter()->convert("::: info\nThis is an info box. **OK**\n:::")->getContent();
    expect($result)->toBe('<div class="info custom-block"><div class="custom-block-title">INFO</div><div class="custom-block-content"><p>This is an info box. <strong>OK</strong></p></div></div>'."\n");

    // 测试自定义标题
    $result = getCustomContainersConverter()->convert("::: info 自定义标题\nThis is an info box.\n:::")->getContent();
    expect($result)->toBe('<div class="info custom-block"><div class="custom-block-title">自定义标题</div><div class="custom-block-content"><p>This is an info box.</p></div></div>'."\n");
});

test('it can render tip containers', function () {
    $result = getCustomContainersConverter()->convert("::: tip\nThis is a tip.\n:::")->getContent();
    expect($result)->toBe('<div class="tip custom-block"><div class="custom-block-title">TIP</div><div class="custom-block-content"><p>This is a tip.</p></div></div>'."\n");

    $result = getCustomContainersConverter()->convert("::: tip 小技巧\nThis is a tip.\n:::")->getContent();
    expect($result)->toBe('<div class="tip custom-block"><div class="custom-block-title">小技巧</div><div class="custom-block-content"><p>This is a tip.</p></div></div>'."\n");
});

test('it can render warning containers', function () {
    $result = getCustomContainersConverter()->convert("::: warning\nThis is a warning.\n:::")->getContent();
    expect($result)->toBe('<div class="warning custom-block"><div class="custom-block-title">WARNING</div><div class="custom-block-content"><p>This is a warning.</p></div></div>'."\n");

    $result = getCustomContainersConverter()->convert("::: warning 警告\nThis is a warning.\n:::")->getContent();
    expect($result)->toBe('<div class="warning custom-block"><div class="custom-block-title">警告</div><div class="custom-block-content"><p>This is a warning.</p></div></div>'."\n");
});

test('it can render danger containers', function () {
    $result = getCustomContainersConverter()->convert("::: danger\nThis is a danger message.\n:::")->getContent();
    expect($result)->toBe('<div class="danger custom-block"><div class="custom-block-title">DANGER</div><div class="custom-block-content"><p>This is a danger message.</p></div></div>'."\n");

    $result = getCustomContainersConverter()->convert("::: danger STOP\nDanger zone, do not proceed\n:::")->getContent();
    expect($result)->toBe('<div class="danger custom-block"><div class="custom-block-title">STOP</div><div class="custom-block-content"><p>Danger zone, do not proceed</p></div></div>'."\n");
});

test('it can render details containers', function () {
    $result = getCustomContainersConverter()->convert("::: details\nThis is a details block.\n:::")->getContent();
    expect($result)->toBe('<details class="details custom-block"><summary class="custom-block-title">Details</summary><div class="custom-block-content"><p>This is a details block.</p></div></details>'."\n");

    $result = getCustomContainersConverter()->convert("::: details 小描述\nThis is a details block.\n:::")->getContent();
    expect($result)->toBe('<details class="details custom-block"><summary class="custom-block-title">小描述</summary><div class="custom-block-content"><p>This is a details block.</p></div></details>'."\n");
});

test('it can render details containers when has {open} flag', function () {
    $result = getCustomContainersConverter()->convert("::: details {open}\nThis is a details block.\n:::")->getContent();
    expect($result)->toBe('<details class="details custom-block" open=""><summary class="custom-block-title">Details</summary><div class="custom-block-content"><p>This is a details block.</p></div></details>'."\n");

    $result = getCustomContainersConverter()->convert("::: details 小描述 {open}\nThis is a details block.\n:::")->getContent();
    expect($result)->toBe('<details class="details custom-block" open=""><summary class="custom-block-title">小描述</summary><div class="custom-block-content"><p>This is a details block.</p></div></details>'."\n");
});

test('it can render details containers when open is true', function () {
    $converter = getCustomContainersConverter([
        'custom_containers' => [
            'details' => [
                'open' => true,
            ],
        ],
    ]);
    $result = $converter->convert("::: details\nThis is a details block.\n:::")->getContent();
    expect($result)->toBe('<details class="details custom-block" open=""><summary class="custom-block-title">Details</summary><div class="custom-block-content"><p>This is a details block.</p></div></details>'."\n");

    $result = $converter->convert("::: details 小描述\nThis is a details block.\n:::")->getContent();
    expect($result)->toBe('<details class="details custom-block" open=""><summary class="custom-block-title">小描述</summary><div class="custom-block-content"><p>This is a details block.</p></div></details>'."\n");
});

test('it can use custom default labels', function () {
    $converter = getCustomContainersConverter([
        'custom_containers' => [
            'labels' => [
                'tip' => '提示',
                'warning' => '警告',
                'danger' => '危险',
                'info' => '信息',
                'details' => '详细信息',
            ],
        ],
    ]);

    $result = $converter->convert("::: info\nThis is an info box.\n:::")->getContent();
    expect($result)->toBe('<div class="info custom-block"><div class="custom-block-title">信息</div><div class="custom-block-content"><p>This is an info box.</p></div></div>'."\n");
});

test('it can use custom CSS class_name', function () {
    $converter = getCustomContainersConverter([
        'custom_containers' => [
            'class_name' => [
                'container' => 'my-container',
                'title' => 'my-title',
                'content' => 'my-content',
            ],
        ],
    ]);

    // 测试普通容器(tip, warning等)的自定义类
    $result = $converter->convert("::: tip\n这是一个提示框\n:::")->getContent();
    expect($result)->toBe(
        '<div class="tip my-container">'.
        '<div class="my-title">TIP</div>'.
        '<div class="my-content"><p>这是一个提示框</p></div>'.
        '</div>'."\n"
    );

    // 测试details容器的自定义类
    $result = $converter->convert("::: details\n这是一个详情框\n:::")->getContent();
    expect($result)->toBe(
        '<details class="details my-container">'.
        '<summary class="my-title">Details</summary>'.
        '<div class="my-content"><p>这是一个详情框</p></div>'.
        '</details>'."\n"
    );
});

test('it uses default CSS class_name when not configured', function () {
    $converter = getCustomContainersConverter();

    $result = $converter->convert("::: info\n默认样式的信息框\n:::")->getContent();
    expect($result)->toBe(
        '<div class="info custom-block">'.
        '<div class="custom-block-title">INFO</div>'.
        '<div class="custom-block-content"><p>默认样式的信息框</p></div>'.
        '</div>'."\n"
    );
});

test('it can partially override CSS class_name', function () {
    $converter = getCustomContainersConverter([
        'custom_containers' => [
            'class_name' => [
                'container' => 'my-container',
                // 只覆盖container类,保持其他类为默认值
            ],
        ],
    ]);

    $result = $converter->convert("::: warning\n部分自定义样式的警告框\n:::")->getContent();
    expect($result)->toBe(
        '<div class="warning my-container">'.
        '<div class="custom-block-title">WARNING</div>'.
        '<div class="custom-block-content"><p>部分自定义样式的警告框</p></div>'.
        '</div>'."\n"
    );
});

test('it can combine custom class_name with type-specific class_name', function () {
    $converter = getCustomContainersConverter([
        'custom_containers' => [
            'class_name' => [
                'container' => 'my-container',
                'title' => 'my-title',
                'content' => 'my-content',
            ],
        ],
    ]);

    // 验证type类(danger)和自定义容器类(my-container)都被正确应用
    $result = $converter->convert("::: danger\n危险提示\n:::")->getContent();
    expect($result)->toBe(
        '<div class="danger my-container">'.
        '<div class="my-title">DANGER</div>'.
        '<div class="my-content"><p>危险提示</p></div>'.
        '</div>'."\n"
    );
});
