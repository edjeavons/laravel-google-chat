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

class GoogleChatCardMarkdown
{
    /**
     * Convert GitHub-Flavoured Markdown to the HTML formatting syntax supported by Google Chat card widgets.
     */
    public static function convert(string $markdown): string
    {
        $environment = new Environment(['html_input' => 'allow']);
        $environment->addExtension(new CommonMarkCoreExtension);
        $environment->addExtension(new GithubFlavoredMarkdownExtension);

        return (new static)->render((new MarkdownParser($environment))->parse($markdown));
    }

    private function render(Node $node): string
    {
        if ($node instanceof Document) {
            return trim(implode('<br><br>', array_filter(array_map(
                fn (Node $child) => $this->render($child),
                iterator_to_array($node->children())
            ), fn (string $content) => $content !== '')));
        }

        if ($node instanceof Paragraph) {
            return $this->renderChildren($node);
        }

        if ($node instanceof Heading) {
            return '<b>'.$this->renderChildren($node).'</b>';
        }

        if ($node instanceof BlockQuote) {
            return implode('<br>', array_map(
                fn (string $line) => '&gt; '.$line,
                explode("\n", $this->renderChildren($node))
            ));
        }

        if ($node instanceof FencedCode || $node instanceof IndentedCode) {
            return '<pre>'.htmlspecialchars(rtrim($node->getLiteral()), ENT_NOQUOTES).'</pre>';
        }

        if ($node instanceof ListBlock) {
            return $this->renderList($node);
        }

        if ($node instanceof Table) {
            return $this->renderTable($node);
        }

        if ($node instanceof ThematicBreak) {
            return '---';
        }

        if ($node instanceof HtmlBlock || $node instanceof HtmlInline) {
            return $node->getLiteral();
        }

        if ($node instanceof Text) {
            return htmlspecialchars($node->getLiteral(), ENT_NOQUOTES);
        }

        if ($node instanceof Newline) {
            return '<br>';
        }

        if ($node instanceof Strong) {
            return '<b>'.$this->renderChildren($node).'</b>';
        }

        if ($node instanceof Emphasis) {
            return '<i>'.$this->renderChildren($node).'</i>';
        }

        if ($node instanceof Strikethrough) {
            return '<s>'.$this->renderChildren($node).'</s>';
        }

        if ($node instanceof Code) {
            return '<code>'.htmlspecialchars($node->getLiteral(), ENT_NOQUOTES).'</code>';
        }

        if ($node instanceof Link) {
            return '<a href="'.$node->getUrl().'">'.$this->renderChildren($node).'</a>';
        }

        if ($node instanceof Image) {
            return '<a href="'.$node->getUrl().'">'.$this->renderChildren($node).'</a>';
        }

        if ($node instanceof TaskListItemMarker) {
            return $node->isChecked() ? '[x] ' : '[ ] ';
        }

        return $this->renderChildren($node);
    }

    private function renderChildren(Node $node): string
    {
        return implode('', array_map(
            fn (Node $child) => $this->render($child),
            iterator_to_array($node->children())
        ));
    }

    private function renderList(ListBlock $list): string
    {
        $tag = $list->getListData()->type === ListBlock::TYPE_BULLET ? 'ul' : 'ol';
        $items = [];

        foreach ($list->children() as $item) {
            if (! $item instanceof ListItem) {
                continue;
            }

            $content = [];
            foreach ($item->children() as $child) {
                $content[] = $this->render($child);
            }

            $items[] = '<li>'.implode('', $content).'</li>';
        }

        return '<'.$tag.'>'.implode('', $items).'</'.$tag.'>';
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
                        $cells[] = $this->renderChildren($cell);
                    }
                }
                $rows[] = implode(' | ', $cells);
            }
        }

        return implode('<br>', $rows);
    }
}
