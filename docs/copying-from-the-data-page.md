# Copying data from the data page

It can be challenging to copy data from the data page to another application (Excel, SPSS etc.) so some scripts have been included within NOTE to make it a bit easier. Your code needs to be written to take advantage of this feature but it's easy.

## 1. Decide on an ID for the block you wish to copy

The ID can be any string that would normally be used to identify an HTML element. This example will use `responses` as an ID.

**You aren't limited to a single block** so you can use multiple IDs and follow the process below for each ID.

## 1. Create a button for copying data to the clipboard

A button is needed to copy the data. Include code like this:

```html
 <button type="button" class="btn btn-primary clipboardable" target="#responses" id="data-button">Copy the response data to the clipboard</button>
```

The `clipboardable` class attaches a click event to start the copying process. The `target` attribute must be the ID you chose earlier. The initial `#` is needed because that's how IDs are referenced.

## 2. Create a report message

Near to the button (so you can see it) add the following:

```html
<div class="clipboardMessage" id="responses-message"></div>
```

This must have the class `clipboardMessage` and have an ID of the text you decided on earlier with `-responses-message` appended. (There's no need to add a `#` here because the `id` attribute declaration doesn't need one.)

## 3. Enclose the data you wish to copy

Surround the data with a `<pre>` or `<div>` tag that has an ID of the text you decided on earlier with `-tsv` appended. For example:

```html
<pre id="responses-tsv">
    // Your data goes here
</pre>
```

or:

```html
<div id="data-tsv">
    // Your data goes here
</div>
```

## 4. Test

Always test everything! These should be all you need to add copying to the clipboard to your data page. Once copied you should be able to paste the contents into your analysis application.

## 5. Hints

- Since the data is going into your application there's no need for fancy formatting, tables etc. So you might want to use data that is as 'raw' as possible with tabs or spaces separating columns. (That's why the data ID ends with `-tsv`: the system originally used tab separated values.) The `<pre>` tag helps to limit the unnecessary formatting.
- You could put the button and the message in two columns of a table or Bootstrap `row` to save space and help prevent the message from shifting data up and down.