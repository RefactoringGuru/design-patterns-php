<?php

namespace RefactoringGuru\Decorator\RealLife;

/**
 * Decorator Design Pattern
 *
 * Intent: Attach additional responsibilities to an object dynamically.
 * Decorators provide a flexible alternative to subclassing for extending
 * functionality.
 *
 * Example: Decorator pattern allows to construct complex content filtering
 * rules to clean-up content before rendering it on a web page. Different
 * scenarios, such as comments, forum posts, emails require different sets of
 * filters, that can be assembled and passed along with the source text.
 */

/**
 * Component interface.
 */
interface InputFormat
{
    public function formatText(string $text): string;
}

/**
 * ConcreteComponent. Original text, as is, no filtering and formatting.
 */
class TextInput implements InputFormat
{
    public function formatText(string $text): string
    {
        return $text;
    }
}

/**
 * Decorator. Defines basic decoration interface.
 */
class TextFormat implements InputFormat
{
    /**
     * @var InputFormat
     */
    protected $inputFormat;

    public function __construct(InputFormat $inoutFormat)
    {
        $this->inputFormat = $inoutFormat;
    }

    /**
     * Decorator delegates all work to a wrapped component.
     */
    public function formatText(string $text): string
    {
        return $this->inputFormat->formatText($text);
    }
}

/**
 * ConcreteDecorator. Filters all tags from text.
 */
class PlainTextFilter extends TextFormat
{
    public function formatText(string $text): string
    {
        $text = parent::formatText($text);
        return strip_tags($text);
    }
}

/**
 * ConcreteDecorator. Filters some of the tags from text.
 */
class DangerousHTMLTagsFilter extends TextFormat
{
    private $dangerousTagPatterns = [
        "|<script.*?>([\s\S]*)?</script>|i", // ...
    ];

    private $dangerousAttributes = [
        "onclick", "onkeypress", // ...
    ];


    public function formatText(string $text): string
    {
        $text = parent::formatText($text);

        foreach ($this->dangerousTagPatterns as $pattern) {
            $text = preg_replace($pattern, '', $text);
        }

        foreach ($this->dangerousAttributes as $attribute) {
            $text = preg_replace_callback('|<(.*?)>|', function ($matches) use ($attribute) {
                $result = preg_replace("|$attribute=|i", '', $matches[1]);
                return "<" . $result . ">";
            }, $text);
        }

        return $text;
    }
}

/**
 * ConcreteDecorator. Basic markdown formatter.
 */
class MarkdownFormat extends TextFormat
{
    public function formatText(string $text): string
    {
        $text = parent::formatText($text);

        // Format block elements.
        $chunks = preg_split('|\n\n|', $text);
        foreach ($chunks as &$chunk) {
            // Format headers.
            if (preg_match('|^#+|', $chunk)) {
                $chunk = preg_replace_callback('|^(#+)(.*?)$|', function ($matches) {
                    $h = strlen($matches[1]);
                    return "<h$h>" . trim($matches[2]) . "</h$h>";
                }, $chunk);
            } // Format paragraphs.
            else {
                $chunk = "<p>$chunk</p>";
            }
        }
        $text = implode("\n\n", $chunks);

        // Format inline elements.
        $text = preg_replace("|__(.*?)__|", '<strong>$1</strong>', $text);
        $text = preg_replace("|\*\*(.*?)\*\*|", '<strong>$1</strong>', $text);
        $text = preg_replace("|_(.*?)_|", '<em>$1</em>', $text);
        $text = preg_replace("|\*(.*?)\*|", '<em>$1</em>', $text);

        return $text;
    }
}


/**
 * Client code. Part of a real website that renders user-generated content.
 */
function displayCommentAsAWebsite(InputFormat $format, string $text)
{
    //..

    print($format->formatText($text));

    //..
}

/**
 * Input formatters are very handy to deal with user-generated content.
 * Displaying it as is could be very dangerous, especially if anonymous
 * users can create it (such as comments).
 */
$dangerousComment = <<<HERE
Hello! Nice blog post!
Please visit my <a href='http://www.iwillhackyou.com'>homepage</a>.
<script src="http://www.iwillhackyou.com/script.js">
  performXSSAttack();
</script>
HERE;

/**
 * Naive comment rendering (unsafe).
 */
$naiveInput = new TextInput();
print("Website renders comments without filtering (unsafe):\n");
displayCommentAsAWebsite($naiveInput, $dangerousComment);
print("\n\n\n");

/**
 * Filtered comment rendering (safe).
 */
$filteredInput = new PlainTextFilter($naiveInput);
print("Website renders comments after stripping all tags (safe):\n");
displayCommentAsAWebsite($filteredInput, $dangerousComment);
print("\n\n\n");


/**
 * Decorator allows to stack multiple input formats to get a fine-grained
 * control over the rendered content.
 */
$dangerousForumPost = <<<HERE
# Welcome

This is my first post on this **gorgeous** forum.

<script src="http://www.iwillhackyou.com/script.js">
  performXSSAttack();
</script>
HERE;

/**
 * Naive post rendering (unsafe, no formatting).
 */
$naiveInput = new TextInput();
print("Website renders a forum post without filtering and formatting (unsafe, ugly):\n");
displayCommentAsAWebsite($naiveInput, $dangerousForumPost);
print("\n\n\n");

/**
 * Markdown formatter + filtering dangerous tags (safe, pretty).
 */
$text = new TextInput();
$markdown = new MarkdownFormat($text);
$filteredInput = new DangerousHTMLTagsFilter($markdown);
print("Website renders a forum post after translating markdown markup" .
    "and filtering some dangerous HTML tags and attributes (safe, pretty):\n");
displayCommentAsAWebsite($filteredInput, $dangerousForumPost);
print("\n\n\n");

