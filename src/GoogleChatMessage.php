<?php

namespace NotificationChannels\GoogleChat;

use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Support\Arr;
use NotificationChannels\GoogleChat\Concerns\ValidatesCardComponents;

class GoogleChatMessage implements Arrayable
{
    use ValidatesCardComponents;

    /**
     * The configured message payload.
     *
     * @var array
     */
    protected $payload = [];

    /**
     * The Space's webhook URL or resource name.
     */
    protected ?string $endpoint = null;

    /**
     * Cards assigned to the message.
     *
     * @var Card[]
     */
    protected array $cards = [];

    /**
     * Target message resource name if performing an update/patch.
     */
    protected ?string $updateMessageName = null;

    /**
     * Fields mask for update operation.
     */
    protected array $updateMask = ['cardsV2'];

    /**
     * Message reply option query parameter.
     */
    protected ?string $replyOption = null;

    /**
     * Set a specific space's webhook URL where this message should be sent to.
     *
     * @param  string  $space  Either a fully-qualified URL, or a nested configuration key
     */
    public function to(string $space): static
    {
        $this->endpoint = $space;

        return $this;
    }

    /**
     * Set this message to update/replace an existing message resource in Google Chat.
     *
     * @param  string  $name  Target message resource name (e.g. 'spaces/AAAA/messages/client-123')
     * @param  array  $updateMask  Fields to modify ('cardsV2', 'text', etc.)
     */
    public function updateMessage(string $name, array $updateMask = ['cardsV2']): static
    {
        $this->updateMessageName = $name;
        $this->updateMask = $updateMask;

        return $this;
    }

    public function updateMask(array $updateMask): static
    {
        $this->updateMask = $updateMask;

        return $this;
    }

    public function isUpdate(): bool
    {
        return $this->updateMessageName !== null;
    }

    public function getUpdateMessageName(): ?string
    {
        return $this->updateMessageName;
    }

    public function getUpdateMask(): array
    {
        return $this->updateMask;
    }

    /**
     * Append text content as a simple text message.
     */
    public function text(string $message): static
    {
        $this->payload['text'] = ($this->payload['text'] ?? '').$message;

        return $this;
    }

    /**
     * Set the plain-text description used when cards cannot be displayed.
     */
    public function fallbackText(string $text): static
    {
        $this->payload['fallbackText'] = $text;

        return $this;
    }

    /**
     * Append GitHub-Flavoured Markdown converted to Google Chat text formatting.
     */
    public function markdown(string $message): static
    {
        return $this->text(GoogleChatMarkdown::convert($message));
    }

    /**
     * Append simple text content on a new line.
     */
    public function line(string $message): static
    {
        $this->text("\n".$message);

        return $this;
    }

    /**
     * Append bold text.
     */
    public function bold(string $message): static
    {
        $this->text("*{$message}*");

        return $this;
    }

    /**
     * Append italic text.
     */
    public function italic(string $message): static
    {
        $this->text("_{$message}_");

        return $this;
    }

    /**
     * Append strikethrough text.
     */
    public function strikethrough(string $message): static
    {
        $this->text("~{$message}~");

        return $this;
    }

    /**
     * Append strikethrough text.
     */
    public function strike(string $message): static
    {
        return $this->strikethrough($message);
    }

    /**
     * Append monospace text.
     */
    public function monospace(string $message): static
    {
        $this->text("`{$message}`");

        return $this;
    }

    /**
     * Append monospace text.
     */
    public function mono(string $message): static
    {
        return $this->monospace($message);
    }

    /**
     * Append monospace block text.
     */
    public function monospaceBlock(string $message): static
    {
        $this->text("```{$message}```");

        return $this;
    }

    /**
     * Append a text link.
     */
    public function link(string $link, ?string $displayText = null): static
    {
        if ($displayText) {
            $link = "<{$link}|{$displayText}>";
        }

        $this->text($link);

        return $this;
    }

    /**
     * Append mention text.
     */
    public function mention(string $userId): static
    {
        $this->text("<users/{$userId}>");

        return $this;
    }

    /**
     * Append mention-all text.
     */
    public function mentionAll(?string $prependText = null, ?string $appendText = null): static
    {
        $this->text("{$prependText}<users/all>{$appendText}");

        return $this;
    }

    /**
     * Add one or more cards to the message using Card instance or closure builder.
     *
     * @param  Card|Card[]|\Closure  $card
     */
    public function card(mixed $card): static
    {
        if ($card instanceof \Closure) {
            $c = Card::make();
            $card($c);
            $cards = [$c];
        } else {
            $cards = Arr::wrap($card);
        }

        $this->guardOnlyInstancesOf(Card::class, $cards);

        $this->cards = array_merge($this->cards, $cards);

        return $this;
    }

    /**
     * Set thread key for grouping replies.
     */
    public function threadKey(string $key): static
    {
        return $this->thread($key, false);
    }

    /**
     * Set thread resource name for replying.
     */
    public function threadName(string $name): static
    {
        return $this->thread($name, true);
    }

    /**
     * Start or reply to a message thread.
     *
     * @param  string  $thread  A thread reference that can be reused later to add replies
     * @param  bool  $isName  Specify the thread using a name rather than a threadKey
     */
    public function thread(string $thread, bool $isName = false): static
    {
        $this->payload['thread'] = [
            $isName ? 'name' : 'threadKey' => $thread,
        ];

        return $this;
    }

    /**
     * Set message reply option query parameter.
     */
    public function replyOption(Enums\MessageReplyOption|string $option): static
    {
        $this->replyOption = $option instanceof Enums\MessageReplyOption ? $option->value : $option;

        return $this;
    }

    public function getReplyOption(): ?string
    {
        return $this->replyOption;
    }

    /**
     * The message is creating or replying to a message thread.
     */
    public function isThreaded(): bool
    {
        return isset($this->payload['thread']);
    }

    /**
     * Return the configured webhook URL of the recipient space, or null if this has
     * not been configured.
     */
    public function getSpace(): ?string
    {
        return $this->endpoint;
    }

    /**
     * Serialize the message to an array representation.
     */
    public function toArray(): array
    {
        $payload = $this->payload;

        if (! empty($this->cards)) {
            $cardsV2 = [];
            foreach ($this->cards as $index => $card) {
                $cardId = $card->getCardId() ?? ('card-'.($index + 1));
                $cardsV2[] = [
                    'cardId' => $cardId,
                    'card' => $card->toArray(),
                ];
            }
            $payload['cardsV2'] = $cardsV2;
        }

        return $payload;
    }

    /**
     * Return a new Google Chat Message instance.
     */
    public static function create(?string $text = null): static
    {
        $message = new static;

        if ($text) {
            $message->text($text);
        }

        return $message;
    }

    /**
     * Return a new Google Chat Message instance.
     */
    public static function make(?string $text = null): static
    {
        return static::create($text);
    }
}
