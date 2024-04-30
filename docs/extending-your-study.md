# Extending your study

Now that you have a working study, there are various ways to extend its capabilities.

(If you *don't* have a working study please see [how to create a first note survey](A-first-NOTE-survey.md).)

## Libraries

Libraries are provided through jsDelivr, a public archive of open source projects. This minimises the number of libraries you must download and manage yourself. For the most common libraries it is only necessary to create an array of names.

NOTE should load the Bootstrap 4 libraries by default, so let's test this. Before you add a library, add the following to *page_welcome.php* after the title:

```php
<div class="text-right">Some right-aligned text</div>
```

When you view this page you should see that the text is right-aligned as expected.

![Screenshot showing some right-aligned text](img/right-aligned-b4.png)

This is because we are using the Bootstrap 4 class *text-right* for right-aligning text. But Bootstrap version 5 has been available for a long time and it is probably better to develop new surveys using the more recent library. In your config file *config.php* add the following after the study title:

```php
$_NOTE_MODULE['CSS_LIBRARIES'] = ['bootstrap5'];
$_NOTE_MODULE['JS_LIBRARIES'] = ['bootstrap5'];
```

Note that the options are being declared as arrays even though you are specifying one library in each. This makes it easy to add libraries later if necessary.

This should cause NOTE to load the JavaScript and CSS libraries for Bootstrap 5 and not 4. If you save and refresh the page you should see that the text is no longer right-aligned.

![Screenshot showing bootstrap 4 text not correctly aligned](img/right-aligned-b5-wrong.png)

Why is this? One of the breaking changes introduced in Bootstrap 5 was the replacement of '*-right*' with '*-end*' in all class names. (Similarly, '*-left*' was replaced by '*-start*'.) So the class *text-right* no longer has any meaning within Bootstrap. To fix this we should update the code on *page_welcome.php*:

```php
<div class="text-end">Some right-aligned text in Bootstrap 5</div>
```

If you save and refresh the page again the text should be right-aligned again:

![Screenshot showing right-aligned text in Bootstrap 5](img/right-aligned-b5-end.png)

By seeing the code 'fail' and changing it you have been able to confirm that Bootstrap 5 is loading correctly.
