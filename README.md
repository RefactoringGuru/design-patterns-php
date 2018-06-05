# Design Patterns in PHP

This repository is part of the [Refactoring.Guru](https://refactoring.guru/design-patterns) project.

It contains PHP examples for all classic GoF design patterns.

Each pattern includes two examples:

- **Structural** examples show the internal structure of patterns with detailed comments.
- **RealWold** examples show how the patterns can be used in a real-world PHP application.


## Requirements

These examples require PHP 7.0 and newer, although they can be easily
replicated in older versions of PHP.

This particular version was picked because it provides explicit argument and return type declarations. This helps to better understand some features of patterns that are not very obvious in dynamically typed language.

All examples can be launched in command line, using a PHP executable as follows:

```
php src/Path-to-example/Example.php
```

For the best experience, we recommend working with examples in [PHPStorm](https://www.jetbrains.com/phpstorm/).


## Roadmap

- [ ] Memento: RealLife
- [ ] State: RealLife

I'm out of decent ideas for real-world usages for these two in PHP apps. If you had used them in your project, feel free to suggest me an idea by posting an Issue.

## FAQ

#### 1. What is the _Client Code_?

_Client_ means _client of classes, defined as part of a pattern_, which is simply the caller of the given methods or user of the given classes. In other words, it's the part of your application's code that uses the pattern's classes.


## Contributor's Guide

We appreciate any help, whether it's a simple fix of a typo or a whole new example. Just [make a fork](https://help.github.com/articles/fork-a-repo/), do your change and submit a [pull request](https://help.github.com/articles/creating-a-pull-request-from-a-fork/).

Here's a style guide which might help you to keep your changes consistent with our code:

1. All code should match the [PSR2 coding style guide](https://github.com/php-fig/fig-standards/blob/master/accepted/PSR-2-coding-style-guide.md)

2. Try to hard wrap the code at 80th's character. It helps to list the code on the website without scrollbars.

3. Examples should match following namespace convention: RefactoringGuru/{pattern-name}/{example-name}. For instance:

    ```php
    <?php
    
    namespace RefactoringGuru/FactoryMethod/Example/Buttons;
    
    class Button {
    ...
    ```

4. Aim to put all code within one file. We realize that it's not how it supposed to be done in production. But it helps people to better understand examples, since all code fits into one screen.

5. Comments may or may not have language tags in them, such as this:

    ```php
    /**
     * EN: All products families have the same varieties (MacOS/Windows).
     *
     * This is a MacOS variant of a button.
     *
     * RU: Все семейства продуктов имеют одни и те же вариации (MacOS/Windows).
     *
     * Это вариант кнопки под MacOS.
     */
    ```

    Don't be scared and ignore the non-English part of such comments. If you want to change something in a comment like this, then do it. Even if you do it wrong, we'll tell you how to fix it during the Pull Request.



## License

This work is licensed under a Creative Commons Attribution-NonCommercial-NoDerivatives 4.0 International License.

<a rel="license" href="http://creativecommons.org/licenses/by-nc-nd/4.0/"><img alt="Creative Commons License" style="border-width:0" src="https://i.creativecommons.org/l/by-nc-nd/4.0/80x15.png" /></a>