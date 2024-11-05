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

it('can render containers with icon', function () {
    $converter = getCustomContainersConverter(['custom_containers' => [
        'icons' => ['enabled' => true],
    ]]);

    $info = $converter->convert("::: info\nThis is an info box.\n:::")->getContent();
    expect($info)->toBe('<div class="info custom-block"><div class="custom-block-title"><div><svg viewBox="0 0 16 16" version="1.1" width="16" height="16" aria-hidden="true"><path d="M0 8a8 8 0 1 1 16 0A8 8 0 0 1 0 8Zm8-6.5a6.5 6.5 0 1 0 0 13 6.5 6.5 0 0 0 0-13ZM6.5 7.75A.75.75 0 0 1 7.25 7h1a.75.75 0 0 1 .75.75v2.75h.25a.75.75 0 0 1 0 1.5h-2a.75.75 0 0 1 0-1.5h.25v-2h-.25a.75.75 0 0 1-.75-.75ZM8 6a1 1 0 1 1 0-2 1 1 0 0 1 0 2Z"></path></svg></div><span>INFO</span></div><div class="custom-block-content"><p>This is an info box.</p></div></div>'."\n");

    $tip = $converter->convert("::: tip\nHelpful advice for doing things better or more easily.\n:::")->getContent();
    expect($tip)->toBe('<div class="tip custom-block"><div class="custom-block-title"><div><svg viewBox="0 0 16 16" version="1.1" width="16" height="16" aria-hidden="true"><path d="M8 1.5c-2.363 0-4 1.69-4 3.75 0 .984.424 1.625.984 2.304l.214.253c.223.264.47.556.673.848.284.411.537.896.621 1.49a.75.75 0 0 1-1.484.211c-.04-.282-.163-.547-.37-.847a8.456 8.456 0 0 0-.542-.68c-.084-.1-.173-.205-.268-.32C3.201 7.75 2.5 6.766 2.5 5.25 2.5 2.31 4.863 0 8 0s5.5 2.31 5.5 5.25c0 1.516-.701 2.5-1.328 3.259-.095.115-.184.22-.268.319-.207.245-.383.453-.541.681-.208.3-.33.565-.37.847a.751.751 0 0 1-1.485-.212c.084-.593.337-1.078.621-1.489.203-.292.45-.584.673-.848.075-.088.147-.173.213-.253.561-.679.985-1.32.985-2.304 0-2.06-1.637-3.75-4-3.75ZM5.75 12h4.5a.75.75 0 0 1 0 1.5h-4.5a.75.75 0 0 1 0-1.5ZM6 15.25a.75.75 0 0 1 .75-.75h2.5a.75.75 0 0 1 0 1.5h-2.5a.75.75 0 0 1-.75-.75Z"></path></svg></div><span>TIP</span></div><div class="custom-block-content"><p>Helpful advice for doing things better or more easily.</p></div></div>'."\n");

    $warning = $converter->convert("::: warning\nThis is a warning box.\n:::")->getContent();
    expect($warning)->toBe('<div class="warning custom-block"><div class="custom-block-title"><div><svg viewBox="0 0 16 16" version="1.1" width="16" height="16" aria-hidden="true"><path d="M6.457 1.047c.659-1.234 2.427-1.234 3.086 0l6.082 11.378A1.75 1.75 0 0 1 14.082 15H1.918a1.75 1.75 0 0 1-1.543-2.575Zm1.763.707a.25.25 0 0 0-.44 0L1.698 13.132a.25.25 0 0 0 .22.368h12.164a.25.25 0 0 0 .22-.368Zm.53 3.996v2.5a.75.75 0 0 1-1.5 0v-2.5a.75.75 0 0 1 1.5 0ZM9 11a1 1 0 1 1-2 0 1 1 0 0 1 2 0Z"></path></svg></div><span>WARNING</span></div><div class="custom-block-content"><p>This is a warning box.</p></div></div>'."\n");

    $danger = $converter->convert("::: danger\nThis is a danger box.\n:::")->getContent();
    expect($danger)->toBe('<div class="danger custom-block"><div class="custom-block-title"><div><svg viewBox="0 0 16 16" version="1.1" width="16" height="16" aria-hidden="true"><path d="M4.47.22A.749.749 0 0 1 5 0h6c.199 0 .389.079.53.22l4.25 4.25c.141.14.22.331.22.53v6a.749.749 0 0 1-.22.53l-4.25 4.25A.749.749 0 0 1 11 16H5a.749.749 0 0 1-.53-.22L.22 11.53A.749.749 0 0 1 0 11V5c0-.199.079-.389.22-.53Zm.84 1.28L1.5 5.31v5.38l3.81 3.81h5.38l3.81-3.81V5.31L10.69 1.5ZM8 4a.75.75 0 0 1 .75.75v3.5a.75.75 0 0 1-1.5 0v-3.5A.75.75 0 0 1 8 4Zm0 8a1 1 0 1 1 0-2 1 1 0 0 1 0 2Z"></path></svg></div><span>DANGER</span></div><div class="custom-block-content"><p>This is a danger box.</p></div></div>'."\n");

    $details = $converter->convert("::: details\nThis is a details box\n:::")->getContent();
    expect($details)->toBe('<details class="details custom-block"><summary class="custom-block-title"><div><svg viewBox="0 0 16 16" version="1.1" width="16" height="16" aria-hidden="true"><path d="M0 1.75C0 .784.784 0 1.75 0h12.5C15.216 0 16 .784 16 1.75v9.5A1.75 1.75 0 0 1 14.25 13H8.06l-2.573 2.573A1.458 1.458 0 0 1 3 14.543V13H1.75A1.75 1.75 0 0 1 0 11.25Zm1.75-.25a.25.25 0 0 0-.25.25v9.5c0 .138.112.25.25.25h2a.75.75 0 0 1 .75.75v2.19l2.72-2.72a.749.749 0 0 1 .53-.22h6.5a.25.25 0 0 0 .25-.25v-9.5a.25.25 0 0 0-.25-.25Zm7 2.25v2.5a.75.75 0 0 1-1.5 0v-2.5a.75.75 0 0 1 1.5 0ZM9 9a1 1 0 1 1-2 0 1 1 0 0 1 2 0Z"></path></svg></div><span>Details</span></summary><div class="custom-block-content"><p>This is a details box</p></div></details>'."\n");
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
