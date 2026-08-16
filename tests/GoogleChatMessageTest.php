<?php

namespace NotificationChannels\GoogleChat\Tests;

use NotificationChannels\GoogleChat\Card;
use NotificationChannels\GoogleChat\Exceptions\CouldNotSendNotification;
use NotificationChannels\GoogleChat\GoogleChatMarkdown;
use NotificationChannels\GoogleChat\GoogleChatMessage;

class GoogleChatMessageTest extends TestCase
{
    public function test_it_stores_space_endpoint()
    {
        $message = GoogleChatMessage::create()->to('example_space');

        $this->assertEquals('example_space', $message->getSpace());
    }

    public function test_it_creates_simple_test_message()
    {
        $message = GoogleChatMessage::create('Example Simple Message');

        $this->assertEquals(['text' => 'Example Simple Message'], $message->toArray());
    }

    public function test_it_appends_text()
    {
        $message = GoogleChatMessage::create('Example Text: ')
            ->text('More Text ')
            ->text('Even More Text');

        $this->assertEquals(
            [
                'text' => 'Example Text: More Text Even More Text',
            ],
            $message->toArray()
        );
    }

    public function test_it_sets_fallback_text()
    {
        $message = GoogleChatMessage::create()->fallbackText('Server alert: CPU utilisation is 94%.');

        $this->assertSame(
            ['fallbackText' => 'Server alert: CPU utilisation is 94%.'],
            $message->toArray()
        );
    }

    public function test_it_converts_github_flavoured_markdown()
    {
        $markdown = <<<'MARKDOWN'
## Release notes

**Ready** for _production_ with ~~no known issues~~.

- [x] Tests pass
- [ ] Deploy

1. Create the release
2. Notify the team

> Review [the merge request](https://example.com/merge_requests/1).

```php
return true;
```
MARKDOWN;

        $this->assertSame(<<<'CHAT'
**Release notes**

**Ready** for _production_ with ~no known issues~.

* [x] Tests pass
* [ ] Deploy

1. Create the release
2. Notify the team

> Review <https://example.com/merge_requests/1|the merge request>.

```
return true;
```
CHAT, GoogleChatMarkdown::convert($markdown));
    }

    public function test_it_converts_release_notes_with_headings_paragraphs_and_lists()
    {
        $markdown = <<<'MARKDOWN'
## Summary

This release improves background processing.

## Updates

- **Change:** Refresh the list view.
- **Change:** Improve the message layout.

## Contributors

- Jane Doe
- John Smith
MARKDOWN;

        $this->assertSame(<<<'CHAT'
**Summary**

This release improves background processing.

**Updates**

* **Change:** Refresh the list view.
* **Change:** Improve the message layout.

**Contributors**

* Jane Doe
* John Smith
CHAT, GoogleChatMarkdown::convert($markdown));
    }

    public function test_it_converts_nested_lists_with_consistent_indentation()
    {
        $markdown = <<<'MARKDOWN'
- Item 1
  - Subitem 1.1
    - Deep item 1.1.1
  - Subitem 1.2
- Item 2
MARKDOWN;

        $this->assertSame(<<<'CHAT'
* Item 1
    * Subitem 1.1
        * Deep item 1.1.1
    * Subitem 1.2
* Item 2
CHAT, GoogleChatMarkdown::convert($markdown));
    }

    public function test_it_converts_tables_images_and_raw_html_to_readable_text()
    {
        $markdown = <<<'MARKDOWN'
| Name | Status |
| --- | --- |
| API | Ready |

![Architecture](https://example.com/architecture.png)

<mark>Highlighted</mark>
MARKDOWN;

        $this->assertSame(<<<'CHAT'
Name | Status
API | Ready

Architecture (https://example.com/architecture.png)

<mark>Highlighted</mark>
CHAT, GoogleChatMarkdown::convert($markdown));
    }

    public function test_it_appends_converted_markdown()
    {
        $message = GoogleChatMessage::create('Merged: ')->markdown('**Ready**');

        $this->assertSame(['text' => 'Merged: **Ready**'], $message->toArray());
    }

    public function test_it_creates_lines()
    {
        $message = GoogleChatMessage::create('Line 1')->line('Line 2');

        $this->assertEquals(
            [
                'text' => "Line 1\nLine 2",
            ],
            $message->toArray()
        );
    }

    public function test_it_creates_bold_text()
    {
        $message = GoogleChatMessage::create()->bold('Some Bold Text');

        $this->assertEquals(
            [
                'text' => '*Some Bold Text*',
            ],
            $message->toArray()
        );
    }

    public function test_it_creates_italic_text()
    {
        $message = GoogleChatMessage::create()->italic('Some Italic Text');

        $this->assertEquals(
            [
                'text' => '_Some Italic Text_',
            ],
            $message->toArray()
        );
    }

    public function test_it_creates_strikethrough_text()
    {
        $message = GoogleChatMessage::create()->strikethrough('Some Strikethrough Text');

        $this->assertEquals(
            [
                'text' => '~Some Strikethrough Text~',
            ],
            $message->toArray()
        );

        // Using Alias method
        $message = GoogleChatMessage::create()->strike('Some Strikethrough Text');

        $this->assertEquals(
            [
                'text' => '~Some Strikethrough Text~',
            ],
            $message->toArray()
        );
    }

    public function test_it_creates_monospace_text()
    {
        $message = GoogleChatMessage::create()->monospace('Some Monospace Text');

        $this->assertEquals(
            [
                'text' => '`Some Monospace Text`',
            ],
            $message->toArray()
        );

        // Using Alias method
        $message = GoogleChatMessage::create()->mono('Some Monospace Text');

        $this->assertEquals(
            [
                'text' => '`Some Monospace Text`',
            ],
            $message->toArray()
        );
    }

    public function test_it_creates_monospace_block_text()
    {
        $message = GoogleChatMessage::create()->monospaceBlock('Some Monospace Block Text');

        $this->assertEquals(
            [
                'text' => '```Some Monospace Block Text```',
            ],
            $message->toArray()
        );
    }

    public function test_it_creates_link_text()
    {
        $message = GoogleChatMessage::create()->link('http://example.com');

        $this->assertEquals(
            [
                'text' => 'http://example.com',
            ],
            $message->toArray()
        );

        $message = GoogleChatMessage::create()->link('http://example.com', 'Example Link');

        $this->assertEquals(
            [
                'text' => '<http://example.com|Example Link>',
            ],
            $message->toArray()
        );
    }

    public function test_it_creates_mention_text()
    {
        $message = GoogleChatMessage::create()->mention('123456789');

        $this->assertEquals(
            [
                'text' => '<users/123456789>',
            ],
            $message->toArray()
        );
    }

    public function test_it_creates_mention_all_text()
    {
        $message = GoogleChatMessage::create()->mentionAll();

        $this->assertEquals(
            [
                'text' => '<users/all>',
            ],
            $message->toArray()
        );

        $message = GoogleChatMessage::create()->mentionAll('Hey ', '!');

        $this->assertEquals(
            [
                'text' => 'Hey <users/all>!',
            ],
            $message->toArray()
        );
    }

    public function test_it_rejects_non_cards()
    {
        $this->expectException(CouldNotSendNotification::class);
        $this->expectExceptionMessage('Cannot pass object of type: stdClass');

        GoogleChatMessage::create()->card(new \stdClass);
    }

    public function test_it_can_add_card()
    {
        $message = GoogleChatMessage::create()->card(Card::create());

        $this->assertEquals(
            [
                'cardsV2' => [
                    [
                        'cardId' => 'card-1',
                        'card' => [
                            'sections' => [],
                        ],
                    ],
                ],
            ],
            $message->toArray()
        );
    }

    public function test_it_supports_closure_card_builder()
    {
        $message = GoogleChatMessage::create()
            ->card(fn (Card $c) => $c->id('custom-id')->header('Title'));

        $this->assertEquals(
            [
                'cardsV2' => [
                    [
                        'cardId' => 'custom-id',
                        'card' => [
                            'header' => ['title' => 'Title'],
                            'sections' => [],
                        ],
                    ],
                ],
            ],
            $message->toArray()
        );
    }

    public function test_it_supports_update_message()
    {
        $message = GoogleChatMessage::create('Updated')
            ->updateMessage('spaces/AAAA/messages/BBB', ['cardsV2', 'text']);

        $this->assertTrue($message->isUpdate());
        $this->assertEquals('spaces/AAAA/messages/BBB', $message->getUpdateMessageName());
        $this->assertEquals(['cardsV2', 'text'], $message->getUpdateMask());
    }

    public function test_it_creates_threaded_messages_by_key()
    {
        $message = GoogleChatMessage::create()->thread('test-thread-key');

        $this->assertEquals(
            [
                'thread' => [
                    'threadKey' => 'test-thread-key',
                ],
            ],
            $message->toArray()
        );
    }

    public function test_it_creates_threaded_messages_by_name()
    {
        $message = GoogleChatMessage::create()->thread('test-thread-name', true);

        $this->assertEquals(
            [
                'thread' => [
                    'name' => 'test-thread-name',
                ],
            ],
            $message->toArray()
        );
    }

    public function test_it_recognises_non_threaded_messages()
    {
        $message = GoogleChatMessage::create('Example Non-Threaded Message');
        $this->assertFalse($message->isThreaded());
    }

    public function test_it_recognises_threaded_messages()
    {
        $message = GoogleChatMessage::create('Example Threaded Message')->thread('test-thread-key');
        $this->assertTrue($message->isThreaded());
    }
}
