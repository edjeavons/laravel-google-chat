<?php

namespace NotificationChannels\GoogleChat;

use League\CommonMark\Environment\Environment;
use League\CommonMark\Extension\CommonMark\CommonMarkCoreExtension;
use League\CommonMark\Extension\CommonMark\Node\Block\BlockQuote;
use League\CommonMark\Extension\CommonMark\Node\Block\FencedCode;
use League\CommonMark\Extension\CommonMark\Node\Block\Heading;
use League\CommonMark\Extension\CommonMark\Node\Block\HtmlBlock;
use League\CommonMark\Extension\CommonMark\Node\Block\IndentedCode;
use League\CommonMark\Extension\CommonMark\Node\Block\ListBlock;
use League\CommonMark\Extension\CommonMark\Node\Block\ListItem;
use League\CommonMark\Extension\CommonMark\Node\Block\ThematicBreak;
use League\CommonMark\Extension\CommonMark\Node\Inline\Code;
use League\CommonMark\Extension\CommonMark\Node\Inline\Emphasis;
use League\CommonMark\Extension\CommonMark\Node\Inline\HtmlInline;
use League\CommonMark\Extension\CommonMark\Node\Inline\Image;
use League\CommonMark\Extension\CommonMark\Node\Inline\Link;
use League\CommonMark\Extension\CommonMark\Node\Inline\Strong;
use League\CommonMark\Extension\GithubFlavoredMarkdownExtension;
use League\CommonMark\Extension\Strikethrough\Strikethrough;
use League\CommonMark\Extension\Table\Table;
use League\CommonMark\Extension\Table\TableCell;
use League\CommonMark\Extension\Table\TableRow;
use League\CommonMark\Extension\Table\TableSection;
use League\CommonMark\Extension\TaskList\TaskListItemMarker;
use League\CommonMark\Node\Block\Document;
use League\CommonMark\Node\Block\Paragraph;
use League\CommonMark\Node\Inline\Newline;
use League\CommonMark\Node\Inline\Text;
use League\CommonMark\Node\Node;
use League\CommonMark\Parser\MarkdownParser;

class GoogleChatMarkdown
{
    /**
     * Convert GitHub-Flavoured Markdown to the formatting syntax supported by Google Chat text messages.
     */
    public static function convert(string $markdown): string
    {
        $environment = new Environment(['html_input' => 'strip']);
        $environment->addExtension(new CommonMarkCoreExtension);
        $environment->addExtension(new GithubFlavoredMarkdownExtension);

        return (new static)->render(new MarkdownParser($environment)->parse($markdown));
    }

    private function render(Node $node, int $listDepth = 0): string
    {
        if ($node instanceof Document) {
            return trim(implode("\n\n", array_filter(array_map(
                fn (Node $child) => $this->render($child),
                iterator_to_array($node->children())
            ), fn (string $content) => $content !== '')));
        }

        if ($node instanceof Paragraph) {
            return $this->renderChildren($node, $listDepth);
        }

        if ($node instanceof Heading) {
            return '*'.$this->renderChildren($node, $listDepth).'*';
        }

        if ($node instanceof BlockQuote) {
            return implode("\n", array_map(
                fn (string $line) => '> '.$line,
                explode("\n", $this->renderChildren($node, $listDepth))
            ));
        }

        if ($node instanceof FencedCode || $node instanceof IndentedCode) {
            return "```\n".rtrim($node->getLiteral())."\n```";
        }

        if ($node instanceof ListBlock) {
            return $this->renderList($node, $listDepth);
        }

        if ($node instanceof Table) {
            return $this->renderTable($node);
        }

        if ($node instanceof ThematicBreak) {
            return '---';
        }

        if ($node instanceof HtmlBlock || $node instanceof HtmlInline) {
            return trim(strip_tags($node->getLiteral()));
        }

        if ($node instanceof Text) {
            return $this->escape($node->getLiteral());
        }

        if ($node instanceof Newline) {
            return "\n";
        }

        if ($node instanceof Strong) {
            return '*'.$this->renderChildren($node, $listDepth).'*';
        }

        if ($node instanceof Emphasis) {
            return '_'.$this->renderChildren($node, $listDepth).'_';
        }

        if ($node instanceof Strikethrough) {
            return '~'.$this->renderChildren($node, $listDepth).'~';
        }

        if ($node instanceof Code) {
            return '`'.$node->getLiteral().'`';
        }

        if ($node instanceof Link) {
            return '<'.$node->getUrl().'|'.$this->renderChildren($node, $listDepth).'>';
        }

        if ($node instanceof Image) {
            return $this->renderChildren($node, $listDepth).' ('.$node->getUrl().')';
        }

        if ($node instanceof TaskListItemMarker) {
            return $node->isChecked() ? '[x]' : '[ ]';
        }

        return $this->renderChildren($node, $listDepth);
    }

    private function renderChildren(Node $node, int $listDepth): string
    {
        return implode('', array_map(
            fn (Node $child) => $this->render($child, $listDepth),
            iterator_to_array($node->children())
        ));
    }

    private function renderList(ListBlock $list, int $depth): string
    {
        $items = [];
        $number = $list->getListData()->start ?? 1;

        foreach ($list->children() as $item) {
            if (! $item instanceof ListItem) {
                continue;
            }

            $content = [];
            foreach ($item->children() as $child) {
                $content[] = $child instanceof ListBlock
                    ? $this->renderList($child, $depth + 1)
                    : $this->render($child, $depth + 1);
            }

            $prefix = $list->getListData()->type === ListBlock::TYPE_BULLET ? '- ' : $number++.'. ';
            $lines = explode("\n", implode("\n", array_filter($content)));
            $indent = str_repeat('    ', $depth);
            $items[] = $indent.$prefix.ltrim((string) array_shift($lines));

            foreach ($lines as $line) {
                $items[] = $indent.'    '.$line;
            }
        }

        return implode("\n", $items);
    }

    private function renderTable(Table $table): string
    {
        $rows = [];

        foreach ($table->children() as $section) {
            if (! $section instanceof TableSection) {
                continue;
            }

            foreach ($section->children() as $row) {
                if (! $row instanceof TableRow) {
                    continue;
                }

                $cells = [];
                foreach ($row->children() as $cell) {
                    if ($cell instanceof TableCell) {
                        $cells[] = $this->renderChildren($cell, 0);
                    }
                }
                $rows[] = implode(' | ', $cells);
            }
        }

        return implode("\n", $rows);
    }

    private function escape(string $text): string
    {
        return str_replace(
            ['\\', '*', '_', '~', '`', '<', '>', '|'],
            ['\\\\', '\\*', '\\_', '\\~', '\\`', '\\<', '\\>', '\\|'],
            $text
        );
    }
}
