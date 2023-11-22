# Exporting files from React to NOTE

## Compile a package

Within the React development environment running `npm run build` should create a compiled version in the `dist` folder.

## Copy to NOTE

Copy the files to the appropriate folder on NOTE. There will probably be a `main.js` file along with supporting files (CSS, fonts etc.). You probably don't need the Bootstrap CSS file as it is included by NOTE already.

If a PHP file is used as an AJAX backend it will also need to be copied. If the PHP file operates on an SQLite database check that the path is correct.
