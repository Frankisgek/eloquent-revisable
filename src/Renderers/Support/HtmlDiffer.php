<?php

namespace TestMonitor\Revisable\Renderers\Support;

use Ssddanbrown\HtmlDiff\Diff;

/**
 * The ssddanbrown/htmldiff differ, comparing an inline formatting element as a whole so that
 * wrapping or unwrapping a word registers as a change — at the cost of detail inside it.
 */
class HtmlDiffer extends Diff
{
    /**
     * @var string[] Compared as a whole. Only short spans belong here: <span> and <code> wrap
     *               whole values.
     */
    protected const array INLINE_TAGS = [
        'strong', 'strike', 'mark', 'sub', 'sup', 'em', 'a', 'b', 'i', 's', 'u',
    ];

    public function __construct(string $before, string $after)
    {
        parent::__construct($before, $after);

        $this->addBlockExpression($this->inlineElementPattern());
    }

    /**
     * One recursive pattern rather than one per tag: PCRE won't return overlapping matches, so a
     * nested element can't end up grouped separately from the element around it.
     */
    protected function inlineElementPattern(): string
    {
        $tags = implode('|', static::INLINE_TAGS);

        return '/<(' . $tags . ')(?:\s[^>]*)?>'
            . '(?:[^<]++|<(?!\/?(?:' . $tags . ')(?:[\s>]|\/>))[^>]*+>|(?R))*+'
            . '<\/\1>/is';
    }
}
