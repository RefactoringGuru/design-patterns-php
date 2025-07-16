<?php

namespace RefactoringGuru\Memento\RealWorld;

/**
 * Memento Design Pattern
 *
 * Intent: Lets you save and restore the previous state of an object without
 * revealing the details of its implementation.
 */


/**
 * Real-World Memento Pattern Example: Text Editor with Undo/Redo
 *
 * This real-world example demonstrates how a text editor implements undo/redo
 * functionality using the Memento pattern. The pattern allows the editor to
 * save snapshots of its state and restore them later without exposing the
 * internal structure of the editor's state to the history manager.
 */


/**
 * The Originator (TextEditor) holds some important state that may change over time.
 *
 * This class represents a text editor that maintains various aspects of state:
 * - Document content (the actual text)
 * - Cursor position within the document
 * - Currently selected text
 * - Formatting options applied to the text
 *
 * The TextEditor defines methods for saving the state inside a memento and
 * another method for restoring the state from it. The key principle is that
 * the editor can create snapshots of its state and restore from them without
 * exposing the internal structure of that state to external classes.
 *
 * The Originator's business logic (typing, deleting, formatting) may affect
 * its internal state. Therefore, the client should backup the state before
 * launching methods of the business logic via the createMemento() method.
 */
class TextEditor
{
    /**
     * Document content
     *
     * The main text content of the document. This is the primary state
     * that users interact with and forms the core of what needs to be
     * preserved in mementos.
     *
     * @var string
     */
    private $content;

    /**
     * Current cursor position
     *
     * The position of the text cursor within the document. This affects
     * where new text will be inserted and is important for maintaining
     * the user's editing context when restoring states.
     *
     * @var int
     */
    private $cursorPosition;

    /**
     * Currently selected text
     *
     * The text that is currently selected by the user. This is important
     * for operations like cut, copy, paste, and formatting application.
     *
     * @var string
     */
    private $selectedText;

    /**
     * Current formatting settings
     *
     * An array containing the current formatting options applied to the text.
     * This includes properties like bold, italic, font size, etc.
     *
     * @var array
     */
    private $formatting;

    /**
     * Constructor
     *
     * Creates a new text editor with default empty state. The editor starts
     * with no content, cursor at position 0, no selected text, and default
     * formatting options.
     */
    public function __construct()
    {
        $this->content = "";
        $this->cursorPosition = 0;
        $this->selectedText = "";
        $this->formatting = ['bold' => false, 'italic' => false, 'fontSize' => 12];
    }

    /**
     * Business logic method: Type text
     *
     * Simulates typing text at the current cursor position. This method
     * represents the core business logic that modifies the editor's state.
     * The text is inserted at the cursor position and the cursor is moved
     * to the end of the inserted text.
     *
     * This is the type of operation that would typically be preceded by
     * a call to createMemento() to save the current state for undo purposes.
     *
     * @param string $text The text to insert at the cursor position
     */
    public function type(string $text): void
    {
        $this->content = substr_replace($this->content, $text, $this->cursorPosition, 0);
        $this->cursorPosition += strlen($text);
        echo "Typed: '$text' | Content: '{$this->content}'\n";
    }

    /**
     * Business logic method: Delete text
     *
     * Simulates deleting text before the cursor position. This method
     * represents another core business operation that modifies the editor's
     * state. The specified number of characters are removed from before
     * the cursor position.
     *
     * @param int $length The number of characters to delete
     */
    public function delete(int $length): void
    {
        if ($this->cursorPosition >= $length) {
            $deleted = substr($this->content, $this->cursorPosition - $length, $length);
            $this->content = substr_replace($this->content, '', $this->cursorPosition - $length, $length);
            $this->cursorPosition -= $length;
            echo "Deleted: '$deleted' | Content: '{$this->content}'\n";
        }
    }

    /**
     * Business logic method: Select text
     *
     * Simulates selecting a portion of text in the document. This operation
     * changes the selectedText state property and would typically be used
     * before applying formatting or performing cut/copy operations.
     *
     * @param int $start The starting position of the selection
     * @param int $length The length of the selection
     */
    public function selectText(int $start, int $length): void
    {
        $this->selectedText = substr($this->content, $start, $length);
        echo "Selected: '{$this->selectedText}'\n";
    }

    /**
     * Business logic method: Apply formatting
     *
     * Applies formatting options to the selected text. This method modifies
     * the formatting state of the editor by merging the provided formatting
     * options with the existing ones.
     *
     * @param array $formatting An array of formatting options to apply
     */
    public function applyFormatting(array $formatting): void
    {
        $this->formatting = array_merge($this->formatting, $formatting);
        echo "Applied formatting: " . json_encode($formatting) . "\n";
    }

    /**
     * Business logic method: Set cursor position
     *
     * Moves the cursor to a specific position within the document. The
     * position is constrained to be within the document bounds.
     *
     * @param int $position The new cursor position
     */
    public function setCursorPosition(int $position): void
    {
        $this->cursorPosition = min($position, strlen($this->content));
        echo "Cursor moved to position: {$this->cursorPosition}\n";
    }

    /**
     * Creates a memento with the current state
     *
     * This is the key method of the Memento pattern. It creates a snapshot
     * of the current state of the editor and returns it as a memento object.
     * The memento encapsulates all the state information needed to restore
     * the editor to its current state at a later time.
     *
     * The important aspect is that this method returns a memento interface,
     * not the concrete implementation. This ensures that the caretaker
     * (history manager) cannot access the internal state directly.
     *
     * @return EditorMemento A memento containing the current state
     */
    public function createMemento(): EditorMemento
    {
        return new EditorSnapshot(
            $this->content,
            $this->cursorPosition,
            $this->selectedText,
            $this->formatting
        );
    }

    /**
     * Restores the Originator's state from a memento object
     *
     * This method accepts a memento and restores the editor's state to
     * match the state stored in the memento. This is the complementary
     * operation to createMemento().
     *
     * The method uses the memento's getState() method to retrieve the
     * state information and then applies it to the editor's internal
     * state variables.
     *
     * @param EditorMemento $memento The memento to restore from
     */
    public function restoreFromMemento(EditorMemento $memento): void
    {
        $state = $memento->getState();
        $this->content = $state['content'];
        $this->cursorPosition = $state['cursorPosition'];
        $this->selectedText = $state['selectedText'];
        $this->formatting = $state['formatting'];
        echo "State restored | Content: '{$this->content}' | Cursor: {$this->cursorPosition}\n";
    }

    /**
     * Get current state for display purposes
     *
     * This is a utility method that returns a string representation of the
     * current state. It's used for debugging and demonstration purposes
     * and is not part of the core Memento pattern.
     *
     * @return string A string representation of the current state
     */
    public function getCurrentState(): string
    {
        return "Content: '{$this->content}' | Cursor: {$this->cursorPosition} | " .
               "Selected: '{$this->selectedText}' | Format: " . json_encode($this->formatting);
    }
}

/**
 * The Memento interface provides a way to retrieve the memento's metadata,
 * such as creation date or name. However, it doesn't expose the Originator's state.
 *
 * This interface defines the contract that all memento implementations must
 * follow. It provides methods for accessing metadata about the memento
 * (like timestamp and description) but crucially does NOT provide direct
 * access to the originator's state.
 *
 * The interface serves as a barrier between the caretaker and the actual
 * state data, ensuring that the caretaker can manage mementos without
 * being able to access or modify the state they contain.
 *
 * The getState() method is included here but should be considered a
 * "package-private" method that only the originator should call.
 */
interface EditorMemento
{
    /**
     * Get the timestamp when this memento was created
     *
     * This method provides metadata about when the memento was created,
     * which is useful for displaying history information to users.
     *
     * @return string The timestamp when the memento was created
     */
    public function getTimestamp(): string;

    /**
     * Get a human-readable description of this memento
     *
     * This method provides a description of the memento that can be
     * displayed to users in a history list. It typically includes
     * information like the timestamp and a preview of the content.
     *
     * @return string A human-readable description
     */
    public function getDescription(): string;

    /**
     * Get the state data from this memento
     *
     * This method returns the actual state data stored in the memento.
     * It should only be called by the originator when restoring state.
     * The caretaker should not call this method directly.
     *
     * @return array The state data stored in this memento
     */
    public function getState(): array;
}

/**
 * The Concrete Memento contains the infrastructure for storing the Originator's state.
 *
 * This class implements the EditorMemento interface and provides the actual
 * storage for the editor's state. It stores all the state information
 * passed to it during construction and provides methods to retrieve
 * this information when needed.
 *
 * The concrete memento is immutable - once created, its state cannot be
 * changed. This ensures that the saved state remains consistent and
 * cannot be accidentally modified by external code.
 *
 * The class also maintains metadata about the memento itself, such as
 * the timestamp when it was created, which is useful for display and
 * management purposes.
 */
class EditorSnapshot implements EditorMemento
{
    /**
     * Stored document content
     *
     * The document content at the time this memento was created.
     * This is stored as a private property to ensure it cannot be
     * modified after creation.
     *
     * @var string
     */
    private $content;

    /**
     * Stored cursor position
     *
     * The cursor position at the time this memento was created.
     *
     * @var int
     */
    private $cursorPosition;

    /**
     * Stored selected text
     *
     * The selected text at the time this memento was created.
     *
     * @var string
     */
    private $selectedText;

    /**
     * Stored formatting options
     *
     * The formatting options at the time this memento was created.
     *
     * @var array
     */
    private $formatting;

    /**
     * Creation timestamp
     *
     * The timestamp when this memento was created. This is used for
     * display purposes and helps users understand when each state
     * was saved.
     *
     * @var string
     */
    private $timestamp;

    /**
     * Constructor
     *
     * Creates a new memento with the provided state information.
     * The timestamp is automatically set to the current time when
     * the memento is created.
     *
     * All parameters are stored as private properties to ensure
     * the memento is immutable after creation.
     *
     * @param string $content The document content to store
     * @param int $cursorPosition The cursor position to store
     * @param string $selectedText The selected text to store
     * @param array $formatting The formatting options to store
     */
    public function __construct(
        string $content,
        int $cursorPosition,
        string $selectedText,
        array $formatting
    ) {
        $this->content = $content;
        $this->cursorPosition = $cursorPosition;
        $this->selectedText = $selectedText;
        $this->formatting = $formatting;
        $this->timestamp = date('Y-m-d H:i:s');
    }

    /**
     * Get the creation timestamp
     *
     * Returns the timestamp when this memento was created. This is
     * used by the caretaker for display and management purposes.
     *
     * @return string The timestamp when this memento was created
     */
    public function getTimestamp(): string
    {
        return $this->timestamp;
    }

    /**
     * Get a human-readable description
     *
     * Returns a description of this memento that can be displayed
     * to users in a history list. The description includes the
     * timestamp, a preview of the content, and the cursor position.
     *
     * @return string A human-readable description of this memento
     */
    public function getDescription(): string
    {
        $preview = strlen($this->content) > 20
            ? substr($this->content, 0, 20) . "..."
            : $this->content;
        return "{$this->timestamp} - \"{$preview}\" (pos: {$this->cursorPosition})";
    }

    /**
     * Get the state data (used by Originator for restoration)
     *
     * Returns the complete state data stored in this memento.
     * This method should only be called by the originator when
     * restoring state. The caretaker should not access this
     * method directly.
     *
     * @return array The complete state data stored in this memento
     */
    public function getState(): array
    {
        return [
            'content' => $this->content,
            'cursorPosition' => $this->cursorPosition,
            'selectedText' => $this->selectedText,
            'formatting' => $this->formatting
        ];
    }
}

/**
 * The Caretaker doesn't depend on the Concrete Memento class.
 *
 * This class represents the Caretaker in the Memento pattern. It is
 * responsible for managing the collection of mementos and providing
 * undo/redo functionality. The key principle is that the caretaker
 * doesn't have access to the originator's state stored inside the
 * memento - it works with all mementos via the base Memento interface.
 *
 * The caretaker maintains a history of mementos and provides methods
 * to save new states, undo to previous states, and redo to future
 * states. It also manages the history size to prevent memory issues
 * and provides utility methods for displaying the history.
 *
 * The caretaker knows when to save mementos and when to restore them,
 * but it doesn't know or care about the actual state data contained
 * within the mementos.
 */
class EditorHistory
{
    /**
     * Collection of mementos
     *
     * An array that stores all the mementos (snapshots) of the editor's
     * state. Each memento represents a specific point in the editor's
     * history that can be restored.
     *
     * @var array
     */
    private $history = [];

    /**
     * Current position in history
     *
     * The index of the current position in the history array. This is
     * used to track where we are in the undo/redo chain. Values:
     * - -1: No history available
     * - 0 to count-1: Valid position in history
     *
     * @var int
     */
    private $currentIndex = -1;

    /**
     * Reference to the originator
     *
     * The caretaker maintains a reference to the originator (TextEditor)
     * so it can save new states and restore previous states. This creates
     * a collaboration between the caretaker and the originator.
     *
     * @var TextEditor
     */
    protected $editor;

    /**
     * Maximum history size
     *
     * The maximum number of mementos to keep in history. This prevents
     * memory issues by limiting the history size. When the limit is
     * reached, the oldest mementos are removed.
     *
     * @var int
     */
    private $maxHistorySize;

    /**
     * Constructor
     *
     * Creates a new history manager for the given text editor.
     * The history starts empty and the maximum history size can be
     * configured to prevent memory issues.
     *
     * @param TextEditor $editor The text editor to manage history for
     * @param int $maxHistorySize Maximum number of states to keep in history
     */
    public function __construct(TextEditor $editor, int $maxHistorySize = 50)
    {
        $this->editor = $editor;
        $this->maxHistorySize = $maxHistorySize;
    }

    /**
     * Save the current state to history
     *
     * This method creates a memento of the current editor state and
     * adds it to the history. If we're not at the end of the history
     * (because we've done some undos), any "future" states are removed
     * before adding the new state.
     *
     * The method also maintains the maximum history size by removing
     * the oldest states when the limit is exceeded.
     *
     * This method should be called before performing any operation
     * that modifies the editor's state and that the user might want
     * to undo later.
     */
    public function saveState(): void
    {
        // Remove any "future" states if we're not at the end
        // This happens when the user has done some undos and then
        // performs a new operation - we need to remove the "future"
        // states that are no longer reachable
        if ($this->currentIndex < count($this->history) - 1) {
            $this->history = array_slice($this->history, 0, $this->currentIndex + 1);
        }

        // Add the new state
        $this->history[] = $this->editor->createMemento();
        $this->currentIndex++;

        // Maintain maximum history size to prevent memory issues
        if (count($this->history) > $this->maxHistorySize) {
            array_shift($this->history);
            $this->currentIndex--;
        }

        echo "State saved to history (index: {$this->currentIndex})\n";
    }

    /**
     * Undo the last action
     *
     * This method restores the editor to the previous state in the history.
     * It moves the current index back by one and restores the editor
     * state from the memento at that position.
     *
     * The method returns true if the undo was successful, or false if
     * there's nothing to undo (already at the beginning of history).
     *
     * @return bool True if undo was successful, false if nothing to undo
     */
    public function undo(): bool
    {
        if ($this->currentIndex > 0) {
            $this->currentIndex--;
            $memento = $this->history[$this->currentIndex];
            $this->editor->restoreFromMemento($memento);
            echo "Undo successful\n";
            return true;
        }

        echo "Nothing to undo\n";
        return false;
    }

    /**
     * Redo the next action
     *
     * This method restores the editor to the next state in the history.
     * It moves the current index forward by one and restores the editor
     * state from the memento at that position.
     *
     * Redo is only possible if we've previously done some undos and
     * haven't performed any new operations since then.
     *
     * @return bool True if redo was successful, false if nothing to redo
     */
    public function redo(): bool
    {
        if ($this->currentIndex < count($this->history) - 1) {
            $this->currentIndex++;
            $memento = $this->history[$this->currentIndex];
            $this->editor->restoreFromMemento($memento);
            echo "Redo successful\n";
            return true;
        }

        echo "Nothing to redo\n";
        return false;
    }

    /**
     * Display the history of states
     *
     * This method shows a list of all mementos in the history, with
     * an indicator showing the current position. This is useful for
     * debugging and for providing users with a visual representation
     * of their editing history.
     *
     * The method uses the memento's getDescription() method to get
     * human-readable information about each state.
     */
    public function showHistory(): void
    {
        echo "\n=== Editor History ===\n";
        if (empty($this->history)) {
            echo "No history available\n";
            return;
        }

        foreach ($this->history as $index => $memento) {
            $marker = $index === $this->currentIndex ? " -> " : "    ";
            echo "{$marker}[{$index}] {$memento->getDescription()}\n";
        }
        echo "======================\n\n";
    }

    /**
     * Clear all history
     *
     * This method removes all mementos from the history and resets
     * the current index. This might be useful when starting a new
     * document or when the user explicitly wants to clear the history.
     */
    public function clearHistory(): void
    {
        $this->history = [];
        $this->currentIndex = -1;
        echo "History cleared\n";
    }
}

/**
 * Advanced History Manager with named snapshots
 *
 * This class extends the basic EditorHistory to provide additional
 * functionality for managing named snapshots. Named snapshots allow
 * users to save specific states with custom names and restore them
 * later, similar to bookmarks in a browser.
 *
 * This demonstrates how the Memento pattern can be extended to provide
 * more sophisticated history management features while maintaining
 * the core principles of the pattern.
 */
class AdvancedEditorHistory extends EditorHistory
{
    /**
     * Collection of named snapshots
     *
     * An associative array that stores mementos with user-defined names.
     * This allows users to save important states with memorable names
     * and restore them later, regardless of the regular undo/redo history.
     *
     * @var array
     */
    private $namedSnapshots = [];

    /**
     * Save a named snapshot
     *
     * This method creates a memento of the current editor state and
     * stores it with a user-defined name. Named snapshots exist
     * independently of the regular undo/redo history and can be
     * restored at any time.
     *
     * @param string $name The name to associate with this snapshot
     */
    public function saveNamedSnapshot(string $name): void
    {
        $this->namedSnapshots[$name] = $this->editor->createMemento();
        echo "Named snapshot '{$name}' saved\n";
    }

    /**
     * Restore from a named snapshot
     *
     * This method restores the editor to the state stored in a named
     * snapshot. Unlike regular undo/redo, this doesn't affect the
     * current position in the history - it's a direct restoration
     * to a specific saved state.
     *
     * @param string $name The name of the snapshot to restore
     * @return bool True if restoration was successful, false if snapshot not found
     */
    public function restoreNamedSnapshot(string $name): bool
    {
        if (isset($this->namedSnapshots[$name])) {
            $this->editor->restoreFromMemento($this->namedSnapshots[$name]);
            echo "Restored from named snapshot '{$name}'\n";
            return true;
        }

        echo "Named snapshot '{$name}' not found\n";
        return false;
    }

    /**
     * List all named snapshots
     *
     * This method displays all available named snapshots with their
     * descriptions. This helps users see what snapshots are available
     * and choose which one to restore.
     */
    public function listNamedSnapshots(): void
    {
        echo "\n=== Named Snapshots ===\n";
        if (empty($this->namedSnapshots)) {
            echo "No named snapshots\n";
        } else {
            foreach ($this->namedSnapshots as $name => $memento) {
                echo "'{$name}' - {$memento->getDescription()}\n";
            }
        }
        echo "========================\n\n";
    }
}

/**
 * ============================================================================
 * USAGE EXAMPLE AND DEMONSTRATION
 * ============================================================================
 *
 * The following code demonstrates how to use the Memento pattern implementation
 * with various scenarios that show all the key features of the pattern:
 * - Creating and restoring mementos
 * - Undo/redo functionality
 * - Named snapshots
 * - History management
 */

echo "=== Text Editor with Memento Pattern Demo ===\n\n";

// Create editor and history manager
$editor = new TextEditor();
$history = new AdvancedEditorHistory($editor);

// Initial state
echo "1. Initial state:\n";
echo $editor->getCurrentState() . "\n\n";

// Save initial state
$history->saveState();

// Type some text
echo "\n\n2. Typing 'Hello World':\n";
$editor->type("Hello World");
$history->saveState();

// Apply formatting
echo "\n\n3. Applying bold formatting:\n";
$editor->applyFormatting(['bold' => true]);
$history->saveState();

// Save a named snapshot
$history->saveNamedSnapshot("hello_world_bold");

// Type more text
echo "\n\n4. Adding more text:\n";
$editor->type(" - This is amazing!");
$history->saveState();

// Move cursor and delete some text
echo "\n\n5. Deleting some text:\n";
$editor->delete(10);
$history->saveState();

// Show current history
$history->showHistory();

// Demonstrate undo/redo
echo "\n\n6. Undo operations:\n";
$history->undo();
$history->undo();

echo "\n\n7. Redo operation:\n";
$history->redo();

// Show history again
$history->showHistory();

// Demonstrate named snapshots
echo "\n\n8. Named snapshots:\n";
$history->listNamedSnapshots();

echo "\n\n9. Restoring from named snapshot:\n";
$history->restoreNamedSnapshot("hello_world_bold");

echo "\n\n10. Final state:\n";
echo $editor->getCurrentState() . "\n";
